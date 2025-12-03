<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdditionalPaymentController extends Controller
{
    /**
     * Display a listing of additional payments.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();
        
        // Get all students for dropdown
        $students = Student::with(['user', 'course', 'branch'])
            ->whereHas('user')
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($locale) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'user_name' => $student->user->name ?? 'N/A',
                    'user_email' => $student->user->email ?? 'N/A',
                    'course_name' => $student->course->getName($locale) ?? 'N/A',
                    'branch_name' => $student->branch->getName($locale) ?? 'N/A',
                ];
            });

        // Get additional payments
        $payments = Payment::where('payment_category', 'additional')
            ->with(['student.user', 'student.course', 'student.branch', 'group'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) use ($locale) {
                return [
                    'id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'student_id' => $payment->student_id,
                    'student_name' => $payment->student->name,
                    'user_name' => $payment->student->user->name ?? 'N/A',
                    'user_email' => $payment->student->user->email ?? 'N/A',
                    'course_name' => $payment->student->course->getName($locale) ?? 'N/A',
                    'branch_name' => $payment->student->branch->getName($locale) ?? 'N/A',
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency ?? 'GEL',
                    'order_desc' => $payment->order_desc,
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Admin/AdditionalPayments/Index', [
            'students' => $students,
            'payments' => $payments,
        ]);
    }

    /**
     * Store a new additional payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'order_desc' => 'required|string|max:500',
            'paid_at' => 'nullable|date',
        ]);

        // Generate unique order_id for additional payment
        $orderId = 'ADD-' . strtoupper(Str::random(8)) . '-' . time();

        $payment = Payment::create([
            'student_id' => $validated['student_id'],
            'group_id' => null, // Additional payments are not tied to groups
            'order_id' => $orderId,
            'payment_id' => null,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'GEL',
            'status' => 'success',
            'payment_type' => 'cash',
            'payment_category' => 'additional',
            'order_desc' => $validated['order_desc'],
            'paid_at' => $validated['paid_at'] ?? now(),
            'flitt_response' => null,
        ]);

        return redirect()->route('admin.additional-payments.index')
            ->with('success', 'Կողմնակի վճարումը հաջողությամբ ավելացվել է։');
    }
}
