<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Ensure the student belongs to the authenticated user
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
            'course_name' => $student->course->getName($locale),
            'branch_name' => $student->branch->getName($locale),
        ];

        // Check if student has paid this month
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
        // Ensure the student belongs to the authenticated user
        if ($student->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $groupId = $request->input('group_id');

        // If group_id is provided, verify student belongs to that group
        if ($groupId) {
            $belongsToGroup = $student->groups()->where('groups.id', $groupId)->exists();
            if (!$belongsToGroup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student does not belong to this group.',
                ], 400);
            }

            // Check if already paid for this group this month
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
            // Check if already paid this month (without group)
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

        // Generate unique order ID
        $orderId = 'ORD-' . Str::upper(Str::random(8)) . '-' . time();

        // Get group name for description if group_id is provided
        $groupName = '';
        if ($groupId) {
            $group = \App\Models\Group::find($groupId);
            $groupName = $group ? $group->getName(app()->getLocale()) : '';
        }

        // Create payment record
        $payment = $student->payments()->create([
            'order_id' => $orderId,
            'group_id' => $groupId,
            'amount' => $request->amount,
            'currency' => 'GEL',
            'status' => 'pending',
            'order_desc' => $groupName 
                ? "Payment for {$student->name} - {$groupName} - " . now()->format('F Y')
                : "Payment for {$student->name} - " . now()->format('F Y'),
        ]);

        // Generate Flitt checkout URL
        $checkoutUrl = $this->generateFlittCheckoutUrl($payment, $request->user());

        // Return JSON response for AJAX requests
        return response()->json([
            'success' => true,
            'checkout_url' => $checkoutUrl,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Handle Flitt webhook.
     */
    public function webhook(Request $request)
    {
        $data = $request->all();
        
        // Verify signature (implement based on Flitt documentation)
        // For now, we'll process the webhook
        
        $orderId = $data['order_id'] ?? null;
        
        if (!$orderId) {
            return response()->json(['error' => 'Order ID missing'], 400);
        }

        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Update payment status based on webhook data
        if (isset($data['status']) && $data['status'] === 'success') {
            $payment->update([
                'status' => 'success',
                'payment_id' => $data['payment_id'] ?? null,
                'paid_at' => now(),
                'flitt_response' => $data,
            ]);
        } elseif (isset($data['status']) && in_array($data['status'], ['failed', 'cancelled'])) {
            $payment->update([
                'status' => $data['status'],
                'flitt_response' => $data,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Generate Flitt checkout URL.
     */
    private function generateFlittCheckoutUrl(Payment $payment, $user)
    {
        $merchantId = env('FLITT_MERCHANT_ID', 4054488);
        $secretKey = env('FLITT_SECRET_KEY', 'wYdSnGkTGhQUqBWhEhilf7j9tOIdKFze');
        $apiBase = env('FLITT_API_BASE', 'https://pay.flitt.com/api');

        // Generate signature: merchant_id + order_id + amount + currency + secret_key
        $signatureString = $merchantId . $payment->order_id . ($payment->amount * 100) . $payment->currency . $secretKey;
        $signature = sha1($signatureString);

        $baseUrl = env('APP_URL', 'https://www.lineup.ge');
        
        $params = [
            'order_id' => $payment->order_id,
            'merchant_id' => $merchantId,
            'order_desc' => $payment->order_desc,
            'amount' => $payment->amount * 100, // Convert to cents
            'currency' => $payment->currency,
            'response_url' => $baseUrl . '/payment-success',
            'webhook_url' => $baseUrl . '/api/payments/webhook',
            'signature' => $signature,
        ];

        // Return the checkout URL (Flitt will redirect to this)
        return $apiBase . '/checkout/redirect?' . http_build_query($params);
    }

    /**
     * Handle payment success redirect from Flitt.
     */
    public function success(Request $request)
    {
        $orderId = $request->input('order_id');
        
        if (!$orderId) {
            return redirect()->route('profile.edit')->with('error', 'Invalid payment response');
        }

        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return redirect()->route('profile.edit')->with('error', 'Payment not found');
        }

        // Update payment status if not already updated by webhook
        if ($payment->status === 'pending') {
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);
        }

        return redirect()->route('profile.payments.history', $payment->student_id)
            ->with('success', 'Payment completed successfully!');
    }
}

