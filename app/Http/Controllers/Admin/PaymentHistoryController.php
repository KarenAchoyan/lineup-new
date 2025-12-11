<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;

class PaymentHistoryController extends Controller
{
    /**
     * Display payment history with filters.
     */
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        // Get filter parameters
        $month = $request->input('month', now()->format('Y-m'));
        $branchId = $request->input('branch_id', '');
        $courseId = $request->input('course_id', '');
        $status = $request->input('status', '');
        $paymentType = $request->input('payment_type', '');
        $paymentCategory = $request->input('payment_category', '');
        $unpaidThisMonth = filter_var($request->input('unpaid_this_month', false), FILTER_VALIDATE_BOOLEAN);
        $search = $request->input('search', '');

        // Validate and parse month
        if (empty($month) || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }
        
        $year = (int) substr($month, 0, 4);
        $monthNum = (int) substr($month, 5, 2);
        
        // Validate year and month
        if ($year < 2000 || $year > 2100 || $monthNum < 1 || $monthNum > 12) {
            $month = now()->format('Y-m');
            $year = (int) substr($month, 0, 4);
            $monthNum = (int) substr($month, 5, 2);
        }

        // If unpaid this month is selected, show students who haven't paid this month
        if ($unpaidThisMonth) {
            return $this->getUnpaidStudents($locale, $branchId, $courseId, $search);
        }

        // Regular payment history query
        $paymentsQuery = Payment::query()
            ->with(['student.course', 'student.branch', 'student.user', 'group'])
            ->whereHas('student', function ($q) {
                $q->whereNotNull('course_id')
                  ->whereNotNull('branch_id')
                  ->whereNotNull('user_id');
            });

        // Month filter - use paid_at if available, otherwise use created_at
        $paymentsQuery->where(function ($q) use ($year, $monthNum) {
            $q->where(function ($subQ) use ($year, $monthNum) {
                // Payments with paid_at in the selected month
                $subQ->whereNotNull('paid_at')
                    ->whereYear('paid_at', $year)
                    ->whereMonth('paid_at', $monthNum);
            })->orWhere(function ($subQ) use ($year, $monthNum) {
                // Payments without paid_at, use created_at for the selected month
                $subQ->whereNull('paid_at')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $monthNum);
            });
        });

        // Apply branch filter
        if (!empty($branchId)) {
            $paymentsQuery->whereHas('student', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        // Apply course filter
        if (!empty($courseId)) {
            $paymentsQuery->whereHas('student', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        // Apply status filter
        if (!empty($status)) {
            $paymentsQuery->where('status', $status);
        }

        // Apply payment type filter
        if (!empty($paymentType)) {
            $paymentsQuery->where('payment_type', $paymentType);
        }

        // Apply payment category filter
        if (!empty($paymentCategory)) {
            $paymentsQuery->where('payment_category', $paymentCategory);
        } else {
            // By default, show only regular payments (not additional) if no filter is set
            // But we can also show all if needed - let's show all by default
        }

        // Apply search filter
        if (!empty($search)) {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                     ->orWhere('email', 'like', "%{$search}%");
                        });
                })
                ->orWhere('order_id', 'like', "%{$search}%");
            });
        }

        // Get payments - by default, if no category filter is set, show all (both regular and additional)
        // But we can also filter by category if needed
        $payments = $paymentsQuery
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->get()
            ->filter(function ($payment) {
                return $payment->student 
                    && $payment->student->course 
                    && $payment->student->branch 
                    && $payment->student->user;
            })
            ->map(function ($payment) use ($locale) {
                    return [
                        'id' => $payment->id,
                        'order_id' => $payment->order_id,
                        'payment_id' => $payment->payment_id,
                        'amount' => (float) $payment->amount,
                        'currency' => $payment->currency ?? 'GEL',
                        'status' => $payment->status,
                        'payment_type' => $payment->payment_type ?? 'online',
                        'payment_category' => $payment->payment_category ?? 'regular',
                        'order_desc' => $payment->order_desc,
                        'group_id' => $payment->group_id,
                        'group_name' => $payment->group ? $payment->group->getName($locale) : null,
                        'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                        'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                        'student' => [
                            'id' => $payment->student->id,
                            'name' => $payment->student->name,
                            'user_name' => $payment->student->user->name ?? 'N/A',
                            'user_email' => $payment->student->user->email ?? 'N/A',
                            'course_id' => $payment->student->course_id,
                            'branch_id' => $payment->student->branch_id,
                            'course_name' => $payment->student->course->getName($locale) ?? 'N/A',
                            'branch_name' => $payment->student->branch->getName($locale) ?? 'N/A',
                        ],
                    ];
            })
            ->values();

        // Get courses and branches for filter dropdowns
        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        $branches = Branch::active()->ordered()->get()->map(function ($branch) use ($locale) {
            return [
                'id' => $branch->id,
                'name' => $branch->getName($locale),
            ];
        });

        // Get students for cash payment dropdown
        $students = Student::with(['course', 'branch', 'user'])
            ->whereHas('course')
            ->whereHas('branch')
            ->whereHas('user')
            ->get()
            ->filter(function ($student) {
                return $student->course && $student->branch && $student->user;
            })
            ->map(function ($student) use ($locale) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'user_name' => $student->user->name ?? 'N/A',
                    'user_email' => $student->user->email ?? 'N/A',
                    'course_id' => $student->course_id,
                    'branch_id' => $student->branch_id,
                    'course_name' => $student->course->getName($locale) ?? 'N/A',
                    'branch_name' => $student->branch->getName($locale) ?? 'N/A',
                ];
            })
            ->values();

        // Calculate statistics from the filtered payments
        $statistics = [
            'total_amount' => $payments->sum('amount'),
            'success_count' => $payments->where('status', 'success')->count(),
            'pending_count' => $payments->where('status', 'pending')->count(),
            'failed_count' => $payments->where('status', 'failed')->count(),
            'cancelled_count' => $payments->where('status', 'cancelled')->count(),
            'cash_count' => $payments->where('payment_type', 'cash')->count(),
            'online_count' => $payments->where('payment_type', 'online')->count(),
            'regular_count' => $payments->where('payment_category', 'regular')->count(),
            'additional_count' => $payments->where('payment_category', 'additional')->count(),
            'unpaid_count' => 0,
        ];

        return Inertia::render('Admin/PaymentHistory/Index', [
            'payments' => $payments,
            'unpaidStudents' => collect(),
            'courses' => $courses,
            'branches' => $branches,
            'students' => $students,
            'filters' => [
                'month' => $month,
                'branch_id' => $branchId,
                'course_id' => $courseId,
                'status' => $status,
                'payment_type' => $paymentType,
                'payment_category' => $paymentCategory,
                'unpaid_this_month' => $unpaidThisMonth,
                'search' => $search,
            ],
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get unpaid students for current month.
     */
    private function getUnpaidStudents($locale, $branchId, $courseId, $search)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Get all students with their payments for current month
        $studentsQuery = Student::with(['course', 'branch', 'user'])
            ->whereHas('course')
            ->whereHas('branch')
            ->whereHas('user');

        // Apply filters
        if (!empty($branchId)) {
            $studentsQuery->where('branch_id', $branchId);
        }

        if (!empty($courseId)) {
            $studentsQuery->where('course_id', $courseId);
        }

        if (!empty($search)) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $studentsQuery->get();

        // Filter students who haven't paid for their groups this month
        $unpaidStudents = $students->filter(function ($student) use ($currentYear, $currentMonth) {
            // Get all groups for this student
            $studentGroups = $student->groups()->pluck('groups.id');
            
            // Check if student has paid for ALL their groups this month
            foreach ($studentGroups as $groupId) {
                $hasPaidForGroup = Payment::where('student_id', $student->id)
                    ->where('group_id', $groupId)
                    ->where('status', 'success')
                    ->whereYear('paid_at', $currentYear)
                    ->whereMonth('paid_at', $currentMonth)
                    ->exists();
                
                if (!$hasPaidForGroup) {
                    return true; // Student hasn't paid for at least one group
                }
            }
            
            // If student has no groups, check general payment
            if ($studentGroups->isEmpty()) {
                $hasPaid = Payment::where('student_id', $student->id)
                    ->whereNull('group_id')
                    ->whereIn('status', ['success', 'pending'])
                    ->whereYear('paid_at', $currentYear)
                    ->whereMonth('paid_at', $currentMonth)
                    ->exists();
                return !$hasPaid;
            }
            
            return false; // Student has paid for all groups
        })->map(function ($student) use ($locale) {
            // Get last payment (any month)
            $lastPayment = Payment::where('student_id', $student->id)
                ->whereIn('status', ['success', 'pending'])
                ->whereNotNull('paid_at')
                ->orderBy('paid_at', 'desc')
                ->first();
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'user_name' => $student->user->name ?? 'N/A',
                'user_email' => $student->user->email ?? 'N/A',
                'course_id' => $student->course_id,
                'branch_id' => $student->branch_id,
                'course_name' => $student->course->getName($locale) ?? 'N/A',
                'branch_name' => $student->branch->getName($locale) ?? 'N/A',
                'last_payment' => $lastPayment?->paid_at?->format('Y-m-d'),
            ];
        })->values();

        // Get courses and branches
        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        $branches = Branch::active()->ordered()->get()->map(function ($branch) use ($locale) {
            return [
                'id' => $branch->id,
                'name' => $branch->getName($locale),
            ];
        });

        // Get students for cash payment dropdown
        $students = Student::with(['course', 'branch', 'user'])
            ->whereHas('course')
            ->whereHas('branch')
            ->whereHas('user')
            ->get()
            ->filter(function ($student) {
                return $student->course && $student->branch && $student->user;
            })
            ->map(function ($student) use ($locale) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'user_name' => $student->user->name ?? 'N/A',
                    'user_email' => $student->user->email ?? 'N/A',
                    'course_id' => $student->course_id,
                    'branch_id' => $student->branch_id,
                    'course_name' => $student->course->getName($locale) ?? 'N/A',
                    'branch_name' => $student->branch->getName($locale) ?? 'N/A',
                ];
            })
            ->values();

        return Inertia::render('Admin/PaymentHistory/Index', [
            'payments' => collect(),
            'unpaidStudents' => $unpaidStudents,
            'courses' => $courses,
            'branches' => $branches,
            'students' => $students,
            'filters' => [
                'month' => now()->format('Y-m'),
                'branch_id' => $branchId,
                'course_id' => $courseId,
                'status' => '',
                'payment_type' => '',
                'payment_category' => '',
                'unpaid_this_month' => true,
                'search' => $search,
            ],
            'statistics' => [
                'total_amount' => 0,
                'success_count' => 0,
                'pending_count' => 0,
                'failed_count' => 0,
                'cancelled_count' => 0,
                'cash_count' => 0,
                'online_count' => 0,
                'unpaid_count' => $unpaidStudents->count(),
            ],
        ]);
    }

    /**
     * Store a cash payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id' => 'nullable|exists:groups,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'paid_at' => 'nullable|date',
            'order_desc' => 'nullable|string|max:255',
        ]);

        // If group_id is provided, verify student belongs to that group
        if (!empty($validated['group_id'])) {
            $student = Student::findOrFail($validated['student_id']);
            $belongsToGroup = $student->groups()->where('groups.id', $validated['group_id'])->exists();
            if (!$belongsToGroup) {
                return redirect()->back()
                    ->withErrors(['group_id' => 'Student does not belong to this group.'])
                    ->withInput();
            }
        }

        // Generate unique order_id for cash payment
        $orderId = 'CASH-' . strtoupper(Str::random(8)) . '-' . time();

        $payment = Payment::create([
            'student_id' => $validated['student_id'],
            'group_id' => $validated['group_id'] ?? null,
            'order_id' => $orderId,
            'payment_id' => null,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'GEL',
            'status' => 'success',
            'payment_type' => 'cash',
            'order_desc' => $validated['order_desc'] ?? 'Cash payment',
            'paid_at' => $validated['paid_at'] ?? now(),
            'flitt_response' => null,
        ]);

        return redirect()->route('admin.payment-history.index')
            ->with('success', 'Cash payment added successfully.');
    }
}
