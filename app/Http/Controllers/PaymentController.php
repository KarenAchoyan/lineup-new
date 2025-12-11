<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    /**
     * Display payment history for a student.
     */
    public function history(Request $request, Student $student): Response
    {
        if ($student->user_id !== $request->user()->id) {
            abort(403);
        }

        $locale = app()->getLocale();
        $payments = $student->payments()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $studentData = [
            'id' => $student->id,
            'name' => $student->name,
            'course_id' => $student->course_id,
            'course_name' => $student->course->getName($locale),
            'course_price' => $student->course->price,
            'branch_name' => $student->branch->getName($locale),
        ];

        $hasPaidThisMonth = $student->payments()
            ->where('status', 'success')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->exists();

        return Inertia::render('Profile/PaymentHistory', [
            'student' => $studentData,
            'payments' => $payments,
            'hasPaidThisMonth' => $hasPaidThisMonth,
        ]);
    }

    /**
     * Create a new payment checkout.
     */
    public function createCheckout(Request $request, Student $student)
    {
        if ($student->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $groupId = $request->input('group_id');

        if ($groupId) {
            $belongsToGroup = $student->groups()->where('groups.id', $groupId)->exists();
            if (!$belongsToGroup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student does not belong to this group.',
                ], 400);
            }

            $hasPaidThisMonth = $student->payments()
                ->where('group_id', $groupId)
                ->where('status', 'success')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->exists();

            if ($hasPaidThisMonth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment for this group this month has already been completed.',
                ], 400);
            }
        } else {
            $hasPaidThisMonth = $student->payments()
                ->whereNull('group_id')
                ->where('status', 'success')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->exists();

            if ($hasPaidThisMonth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment for this month has already been completed.',
                ], 400);
            }
        }

        $orderId = time() . Str::upper(Str::random(6));

        $groupName = '';
        if ($groupId) {
            $group = \App\Models\Group::find($groupId);
            $groupName = $group ? $group->getName(app()->getLocale()) : '';
        }

        // Store our internal order_id - Flitt will return its own order_id and payment_id which we'll update later
        $payment = $student->payments()->create([
            'order_id' => $orderId, // Our internal order_id (sent to Flitt)
            'payment_id' => $orderId, // Temporarily store our order_id here to find payment later
            'group_id' => $groupId,
            'amount' => $request->amount,
            'currency' => 'GEL',
            'status' => 'pending',
            'order_desc' => $groupName 
                ? "Payment for {$student->name} - {$groupName} - " . now()->format('F Y')
                : "Payment for {$student->name} - " . now()->format('F Y'),
            // Store our original order_id in flitt_response for reference
            'flitt_response' => ['original_order_id' => $orderId],
        ]);

        $checkoutData = $this->generateFlittCheckoutData($payment);

        // If Flitt returned order_id and payment_id, update payment immediately
        if (isset($checkoutData['flitt_order_id']) && isset($checkoutData['flitt_payment_id'])) {
            $payment->update([
                'order_id' => $checkoutData['flitt_order_id'],
                'payment_id' => $checkoutData['flitt_payment_id'],
                'flitt_response' => array_merge($payment->flitt_response ?? [], [
                    'original_order_id' => $orderId,
                    'checkout_response' => $checkoutData['flitt_response'] ?? [],
                ]),
            ]);
        }

        return response()->json([
            'success' => true,
            'checkout_data' => $checkoutData,
            'checkout_url' => $checkoutData['url'],
            'order_id' => $checkoutData['flitt_order_id'] ?? $orderId,
        ]);
    }

    /**
     * Flitt callback webhook.
     */
    public function webhook(Request $request)
    {
        $data = $request->all();
        
        $flittOrderId = $data['order_id'] ?? null;
        $flittPaymentId = $data['payment_id'] ?? null;
   
        if (!$flittOrderId) {
            return response()->json(['error' => 'Order ID missing'], 400);
        }

        // Find payment by Flitt's order_id (if already updated) or by our original order_id
        $payment = null;
        
        // First try to find by Flitt's order_id (in case already updated)
        $payment = Payment::where('order_id', $flittOrderId)->first();
        
        // If not found, try to find by our original order_id (stored in payment_id temporarily)
        // Flitt might return our order_id in the response, or we can find by payment_id
        if (!$payment) {
            // Try to find by payment_id (our original order_id stored temporarily)
            $payment = Payment::where('payment_id', $flittOrderId)
                ->where('status', 'pending')
                ->first();
        }
        
        // If still not found, try to find by our original order_id in flitt_response
        if (!$payment) {
            $payment = Payment::where('status', 'pending')
                ->where('flitt_response->original_order_id', $flittOrderId)
                ->first();
        }
        
        // Last resort: try to find by order_id matching (Flitt might return our order_id)
        if (!$payment) {
            $payment = Payment::where('order_id', $flittOrderId)->first();
        }

        if (!$payment) {
            // Log for debugging
            Log::warning('Flitt webhook: Payment not found', [
                'flitt_order_id' => $flittOrderId,
                'flitt_payment_id' => $flittPaymentId,
                'all_data' => $data,
            ]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Store original order_id before update
        $originalOrderId = $payment->order_id;
        
        // Always update order_id with Flitt's order_id (this is the official Flitt order_id)
        $updateData = [
            'order_id' => $flittOrderId, // Update with Flitt's order_id
        ];

        if (isset($data['status']) && $data['status'] === 'success') {
            $updateData = array_merge($updateData, [
                'status' => 'success',
                'payment_id' => $flittPaymentId, // Flitt's payment_id
                'paid_at' => now(),
                'flitt_response' => array_merge($data, ['original_order_id' => $originalOrderId]),
            ]);
        } elseif (isset($data['status']) && in_array($data['status'], ['failed', 'cancelled'])) {
            $updateData = array_merge($updateData, [
                'status' => $data['status'],
                'flitt_response' => array_merge($data, ['original_order_id' => $originalOrderId]),
            ]);
        }

        try {
            $payment->update($updateData);
            Log::info('Flitt webhook: Payment updated', [
                'payment_id' => $payment->id,
                'old_order_id' => $originalOrderId,
                'new_order_id' => $flittOrderId,
                'status' => $data['status'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Flitt webhook: Error updating payment', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
                'update_data' => $updateData,
            ]);
            return response()->json(['error' => 'Failed to update payment'], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Generate fixed Flitt checkout data.
     */
    private function generateFlittCheckoutData(Payment $payment)
    {
        $merchantId = env('FLITT_MERCHANT_ID', 4054488);
        $secretKey = env('FLITT_SECRET_KEY', 'wYdSnGkTGhQUqBWhEhilf7j9tOIdKFze');
        $apiBase = env('FLITT_API_BASE', 'https://pay.flitt.com/api');

        $baseUrl = rtrim(env('APP_URL', 'https://www.lineup.ge'), '/');

        // Amount must be in cents (smallest currency unit) for Flitt
        // Example: 30 GEL = 3000 cents
        $amount = (int)round($payment->amount * 100);

        $currency = strtoupper($payment->currency);
        $merchantId = (int)$merchantId;

        // Prepare URLs
        $responseUrl = $baseUrl . '/payment-success';
        $webhookUrl = $baseUrl . '/api/payments/webhook';

        $orderDesc = mb_substr($payment->order_desc, 0, 1024, 'UTF-8');

        // Prepare all parameters (without signature)
        $params = [
            'amount' => $amount,
            'currency' => $currency,
            'merchant_id' => $merchantId,
            'order_desc' => $orderDesc,
            'order_id' => $payment->order_id,
            'response_url' => $responseUrl,
            'server_callback_url' => $webhookUrl,
        ];

        // Flitt signature algorithm:
        // 1. Filter out empty values
        // 2. Sort alphabetically by key
        // 3. Get values only
        // 4. Add secret key at the beginning
        // 5. Join with | symbol
        // 6. SHA1 hash
        $params = array_filter($params, 'strlen'); // Remove empty values
        ksort($params); // Sort alphabetically by key
        $params = array_values($params); // Get values only
        array_unshift($params, $secretKey); // Add secret key at the beginning
        $signatureString = implode('|', $params); // Join with |
        $signature = sha1($signatureString);

        // Add signature to params
        $params['signature'] = $signature;
        
        // Rebuild params array with correct order for Flitt
        $params = [
            'order_id' => $payment->order_id,
            'merchant_id' => $merchantId,
            'order_desc' => $orderDesc,
            'amount' => $amount,
            'currency' => $currency,
            'response_url' => $responseUrl,
            'server_callback_url' => $webhookUrl,
            'signature' => $signature,
        ];

        Log::info('Flitt Checkout Params', $params);

        // Call Flitt /api/checkout/url endpoint to get checkout_url and Flitt's order_id/payment_id
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiBase . '/checkout/url', [
                'request' => $params,
            ]);

            $responseData = $response->json();
            
            Log::info('Flitt Checkout Response', [
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if ($response->successful() && isset($responseData['response'])) {
                $flittResponse = $responseData['response'];
                
                // Extract Flitt's order_id and payment_id from response
                $flittOrderId = $flittResponse['order_id'] ?? $payment->order_id;
                $flittPaymentId = $flittResponse['payment_id'] ?? null;
                $checkoutUrl = $flittResponse['checkout_url'] ?? null;

                if ($checkoutUrl) {
                    return [
                        'url' => $checkoutUrl,
                        'params' => [], // No params needed, redirect directly to checkout_url
                        'flitt_order_id' => $flittOrderId,
                        'flitt_payment_id' => $flittPaymentId,
                        'flitt_response' => $flittResponse,
                    ];
                }
            }

            // Fallback to redirect method if URL endpoint fails
            Log::warning('Flitt /checkout/url failed, falling back to /checkout/redirect', [
                'response' => $responseData,
            ]);
        } catch (\Exception $e) {
            Log::error('Flitt /checkout/url error', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback: return redirect method
        return [
            'url' => $apiBase . '/checkout/redirect',
            'params' => $params,
        ];
    }

    /**
     * Check payment status from Flitt API (public endpoint).
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $orderId = $request->input('order_id');
        $user = $request->user();

        // Find payment by order_id
        $payment = Payment::where('order_id', $orderId)->first();

        // If not found, try to find by payment_id
        if (!$payment) {
            $payment = Payment::where('payment_id', $orderId)->first();
        }

        // If still not found, try to find by original order_id in flitt_response
        if (!$payment) {
            $payment = Payment::where('flitt_response->original_order_id', $orderId)->first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        // Verify user owns this payment
        if ($payment->student->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check status from Flitt API
        $flittOrderId = $payment->order_id;
        $statusData = $this->checkFlittPaymentStatus($flittOrderId);

        if (!$statusData) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status',
                'payment' => [
                    'id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        // Update payment status based on Flitt response
        $flittStatus = $statusData['order_status'] ?? $statusData['status'] ?? null;
        $updateData = [];
        $originalOrderId = $payment->order_id;

        if ($flittStatus === 'approved' || $flittStatus === 'success') {
            $updateData = [
                'status' => 'success',
                'paid_at' => $payment->paid_at ?: now(),
                'flitt_response' => array_merge($payment->flitt_response ?? [], [
                    'status_check' => $statusData,
                    'original_order_id' => $originalOrderId,
                ]),
            ];
        } elseif (in_array($flittStatus, ['declined', 'failed', 'cancelled'])) {
            $updateData = [
                'status' => 'failed',
                'flitt_response' => array_merge($payment->flitt_response ?? [], [
                    'status_check' => $statusData,
                    'original_order_id' => $originalOrderId,
                ]),
            ];
        } else {
            // Status is pending or unknown, just update the response data
            $updateData = [
                'flitt_response' => array_merge($payment->flitt_response ?? [], [
                    'status_check' => $statusData,
                    'original_order_id' => $originalOrderId,
                ]),
            ];
        }

        if (!empty($updateData)) {
            try {
                $payment->update($updateData);
                $payment->refresh();
            } catch (\Exception $e) {
                Log::error('Check Status: Error updating payment', [
                    'error' => $e->getMessage(),
                    'payment_id' => $payment->id,
                    'update_data' => $updateData,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'order_id' => $payment->order_id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
            ],
            'flitt_status' => $flittStatus,
        ]);
    }

    /**
     * Check payment status from Flitt API.
     */
    private function checkFlittPaymentStatus($orderId)
    {
        $merchantId = env('FLITT_MERCHANT_ID', 4054488);
        $secretKey = env('FLITT_SECRET_KEY', 'wYdSnGkTGhQUqBWhEhilf7j9tOIdKFze');
        $apiBase = env('FLITT_API_BASE', 'https://pay.flitt.com/api');

        // Prepare parameters for status check
        $params = [
            'order_id' => $orderId,
            'merchant_id' => (int)$merchantId,
            'version' => '1.0.1',
        ];

        // Generate signature for status check
        $paramsForSignature = array_filter($params, 'strlen');
        ksort($paramsForSignature);
        $paramsForSignature = array_values($paramsForSignature);
        array_unshift($paramsForSignature, $secretKey);
        $signatureString = implode('|', $paramsForSignature);
        $signature = sha1($signatureString);

        $params['signature'] = $signature;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiBase . '/status/order_id', [
                'request' => $params,
            ]);

            $responseData = $response->json();
            
            Log::info('Flitt Status Check Response', [
                'status' => $response->status(),
                'order_id' => $orderId,
                'response' => $responseData,
            ]);

            if ($response->successful() && isset($responseData['response'])) {
                return $responseData['response'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Flitt Status Check Error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return null;
        }
    }

    public function success(Request $request)
    {
        // Retrieve the last payment and set its status to 'success'
        $lastPayment = \App\Models\Payment::orderBy('id', 'desc')->first();

        if ($lastPayment) {
            $lastPayment->status = 'success';
            $lastPayment->paid_at = $lastPayment->paid_at ?: now();
            $lastPayment->save();

            return redirect()->route('profile.edit')->with('success', 'Վերջին վճարումը հաջողությամբ կարգավիճակավորվեց որպես հաջողված։');
        } else {
            return redirect()->route('profile.edit')->with('error', 'Վճարում չի գտնվել։');
        }
        // if (!$flittPaymentId && !$flittOrderId) {
        //     return redirect()->route('profile.edit')->with('error', 'Invalid payment response');
        // }

        // // Find payment by Flitt's payment_id or order_id
        // $payment = null;
        
        // if ($flittPaymentId) {
        //     $payment = Payment::where('payment_id', $flittPaymentId)->first();
        // }
        
        // if (!$payment && $flittOrderId) {
        //     $payment = Payment::where('order_id', $flittOrderId)->first();
        // }
        
        // // If not found, try to find by our original order_id (stored in payment_id temporarily)
        // if (!$payment && $flittOrderId) {
        //     $payment = Payment::where('payment_id', $flittOrderId)
        //         ->where('status', 'pending')
        //         ->first();
        // }
        
        // // If still not found, try to find by our original order_id in flitt_response
        // if (!$payment && $flittOrderId) {
        //     $payment = Payment::where('status', 'pending')
        //         ->where('flitt_response->original_order_id', $flittOrderId)
        //         ->first();
        // }

        // if (!$payment) {
        //     Log::warning('Flitt success: Payment not found', [
        //         'flitt_order_id' => $flittOrderId,
        //         'flitt_payment_id' => $flittPaymentId,
        //         'all_params' => $request->all(),
        //     ]);
        //     return redirect()->route('profile.edit')->with('error', 'Payment not found');
        // }

        // // Check payment status from Flitt API
        // $statusData = null;
        // if ($flittOrderId) {
        //     $statusData = $this->checkFlittPaymentStatus($flittOrderId);
        // }

        // // Store original order_id before update
        // $originalOrderId = $payment->order_id;
        
        // // Prepare update data
        // $updateData = [];
        
        // // Update order_id with Flitt's order_id if provided
        // if ($flittOrderId && $payment->order_id !== $flittOrderId) {
        //     $updateData['order_id'] = $flittOrderId;
        // }
        
        // // Update payment_id with Flitt's payment_id if provided
        // if ($flittPaymentId && $payment->payment_id !== $flittPaymentId) {
        //     $updateData['payment_id'] = $flittPaymentId;
        // }

        // // Update status based on Flitt status check or request data
        // if ($statusData) {
        //     $flittStatus = $statusData['order_status'] ?? $statusData['status'] ?? null;
            
        //     if ($flittStatus === 'approved' || $flittStatus === 'success') {
        //         $updateData = array_merge($updateData, [
        //             'status' => 'success',
        //             'paid_at' => isset($statusData['approval_code']) ? now() : ($payment->paid_at ?? now()),
        //             'flitt_response' => array_merge($statusData, ['original_order_id' => $originalOrderId]),
        //         ]);
        //     } elseif (in_array($flittStatus, ['declined', 'failed', 'cancelled'])) {
        //         $updateData = array_merge($updateData, [
        //             'status' => 'failed',
        //             'flitt_response' => array_merge($statusData, ['original_order_id' => $originalOrderId]),
        //         ]);
        //     }
        // } elseif ($payment->status === 'pending') {
        //     // If status check failed, assume success based on redirect
        //     $updateData = array_merge($updateData, [
        //         'status' => 'success',
        //         'paid_at' => now(),
        //         'flitt_response' => array_merge($request->all(), ['original_order_id' => $originalOrderId]),
        //     ]);
        // }

        // if (!empty($updateData)) {
        //     try {
        //         $payment->update($updateData);
        //         Log::info('Flitt success: Payment updated', [
        //             'payment_id' => $payment->id,
        //             'old_order_id' => $originalOrderId,
        //             'new_order_id' => $flittOrderId,
        //             'update_data' => $updateData,
        //         ]);
        //     } catch (\Exception $e) {
        //         Log::error('Flitt success: Error updating payment', [
        //             'error' => $e->getMessage(),
        //             'payment_id' => $payment->id,
        //             'update_data' => $updateData,
        //         ]);
        //     }
        // }

        // // Redirect to profile edit with order_id and payment_id as query parameters to check status
        // $redirectUrl = route('profile.edit');
        // $queryParams = [];
        // if ($flittOrderId) {
        //     $queryParams['order_id'] = $flittOrderId;
        // }
        // if ($flittPaymentId) {
        //     $queryParams['payment_id'] = $flittPaymentId;
        // }
        // if (!empty($queryParams)) {
        //     $redirectUrl .= '?' . http_build_query($queryParams);
        // }
        // return redirect($redirectUrl);
    }
}
