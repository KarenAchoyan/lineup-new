<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ActiveUserController;
use App\Http\Controllers\Admin\ArchiveController as AdminArchiveController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Sections\DanceController;
use App\Http\Controllers\Sections\PhotoController;
use App\Http\Controllers\Sections\TheatreController;
use App\Http\Controllers\Sections\VocalsController;
use App\Http\Controllers\VolunteeringController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/archive', [ArchiveController::class, 'index'])->name('archive');

use App\Http\Controllers\SectionsController;

// Dynamic sections routes
Route::get('/sections', [SectionsController::class, 'index'])->name('sections.index');
Route::get('/sections/{slug}', [SectionsController::class, 'show'])->name('sections.show');

// Keep old routes for backward compatibility (optional)
Route::get('/sections/dance', [DanceController::class, 'index'])->name('sections.dance');
Route::get('/sections/vocals', [VocalsController::class, 'index'])->name('sections.vocals');
Route::get('/sections/theatre', [TheatreController::class, 'index'])->name('sections.theatre');
Route::get('/sections/photo', [PhotoController::class, 'index'])->name('sections.photo');

Route::get('/volunteering', [VolunteeringController::class, 'index'])->name('volunteering');
Route::get('/volunteering/registration', [VolunteeringController::class, 'registration'])->name('volunteering.registration');
Route::post('/volunteering', [VolunteeringController::class, 'store'])->name('volunteering.store');

Route::get('/collaboration', [CollaborationController::class, 'index'])->name('collaboration');
Route::post('/collaboration', [CollaborationController::class, 'store'])->name('collaboration.store');

Route::get('/news', [NewsController::class, 'index'])->name('news');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/achievements', [GalleryController::class, 'index'])->name('achievements');

Route::get('/active-youth/{id}', [ActiveUserController::class, 'show'])->name('active-youth.show');

Route::get('/privacy', [PrivacyController::class, 'index'])->name('privacy');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/messages/{message}/mark-read', [ProfileController::class, 'markMessageAsRead'])->name('profile.messages.mark-read');
    Route::get('/profile/attendance-data', [ProfileController::class, 'getAttendanceData'])->name('profile.attendance-data');
    
    // Student sub-profiles routes
    Route::post('/profile/students', [ProfileController::class, 'storeStudent'])->name('profile.students.store');
    Route::patch('/profile/students/{student}', [ProfileController::class, 'updateStudent'])->name('profile.students.update');
    Route::delete('/profile/students/{student}', [ProfileController::class, 'destroyStudent'])->name('profile.students.destroy');
    
    // Payment routes
    Route::get('/profile/payments/{student}', [PaymentController::class, 'history'])->name('profile.payments.history');
    Route::post('/profile/payments/{student}/create', [PaymentController::class, 'createCheckout'])->name('profile.payments.create');
    Route::post('/profile/payments/check-status', [PaymentController::class, 'checkStatus'])->name('profile.payments.check-status');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['admin', 'verified'])->group(function () {
        Route::resource('archive', AdminArchiveController::class)->except(['update']);
        Route::post('archive/{archive}', [AdminArchiveController::class, 'update'])->name('archive.update');
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class)->except(['update']);
        Route::post('courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
        Route::get('about/edit', [\App\Http\Controllers\Admin\AboutController::class, 'edit'])->name('about.edit');
        Route::post('about', [\App\Http\Controllers\Admin\AboutController::class, 'update'])->name('about.update');
        Route::get('privacy/edit', [\App\Http\Controllers\Admin\PrivacyController::class, 'edit'])->name('privacy.edit');
        Route::post('privacy', [\App\Http\Controllers\Admin\PrivacyController::class, 'update'])->name('privacy.update');
        Route::get('banner/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banner.edit');
        Route::post('banner', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banner.update');
        Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class)->except(['update']);
        Route::post('staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('staff.update');
        Route::resource('volunteering-submissions', \App\Http\Controllers\Admin\VolunteeringSubmissionController::class)->only(['index', 'show', 'destroy']);
        Route::resource('collaboration-submissions', \App\Http\Controllers\Admin\CollaborationSubmissionController::class)->only(['index', 'show', 'destroy']);
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class)->except(['update']);
        Route::post('news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update'])->name('news.update');
        Route::resource('events', \App\Http\Controllers\Admin\EventController::class)->except(['update']);
        Route::post('events/{event}', [\App\Http\Controllers\Admin\EventController::class, 'update'])->name('events.update');
        Route::resource('galleries', \App\Http\Controllers\Admin\GalleryController::class);
        Route::resource('active-users', \App\Http\Controllers\Admin\ActiveUserController::class);
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/update-password', [\App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('users.update-password');
        Route::get('users/students/{student}/groups', [\App\Http\Controllers\Admin\UserController::class, 'getStudentGroups'])->name('users.students.groups');
        Route::post('users/students/{student}/groups', [\App\Http\Controllers\Admin\UserController::class, 'updateStudentGroups'])->name('users.students.groups.update');
        Route::post('users/{user}/assign-teacher-role', [\App\Http\Controllers\Admin\UserController::class, 'assignTeacherRole'])->name('users.assign-teacher-role');
        Route::post('users/{user}/remove-teacher-role', [\App\Http\Controllers\Admin\UserController::class, 'removeTeacherRole'])->name('users.remove-teacher-role');
        Route::get('payment-history', [\App\Http\Controllers\Admin\PaymentHistoryController::class, 'index'])->name('payment-history.index');
        Route::post('payment-history/cash', [\App\Http\Controllers\Admin\PaymentHistoryController::class, 'store'])->name('payment-history.cash.store');
        Route::get('additional-payments', [\App\Http\Controllers\Admin\AdditionalPaymentController::class, 'index'])->name('additional-payments.index');
        Route::post('additional-payments', [\App\Http\Controllers\Admin\AdditionalPaymentController::class, 'store'])->name('additional-payments.store');
        Route::resource('groups', \App\Http\Controllers\Admin\GroupController::class);
        Route::get('groups/search/students', [\App\Http\Controllers\Admin\GroupController::class, 'searchStudents'])->name('groups.search-students');
        Route::get('groups/{group}/students', [\App\Http\Controllers\Admin\GroupController::class, 'showStudents'])->name('groups.show-students');
        Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/{group}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('attendance/bulk', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkStore'])->name('attendance.bulk-store');
        Route::post('attendance/students/{student}/days', [\App\Http\Controllers\Admin\AttendanceController::class, 'updateAttendanceDays'])->name('attendance.update-attendance-days');
        Route::post('attendance/groups/{group}/bulk-days', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkUpdateAttendanceDays'])->name('attendance.bulk-update-attendance-days');
        Route::get('messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/create', [\App\Http\Controllers\Admin\MessageController::class, 'create'])->name('messages.create');
        Route::post('messages', [\App\Http\Controllers\Admin\MessageController::class, 'store'])->name('messages.store');
        Route::post('messages/send-to-users', [\App\Http\Controllers\Admin\MessageController::class, 'sendToUsers'])->name('messages.send-to-users');
        Route::get('messages/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/reply', [\App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    });

    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->middleware(['teacher', 'verified'])->group(function () {
        Route::resource('groups', \App\Http\Controllers\Teacher\GroupController::class);
        Route::get('groups/search/students', [\App\Http\Controllers\Teacher\GroupController::class, 'searchStudents'])->name('groups.search-students');
        Route::get('attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/{group}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('attendance/bulk', [\App\Http\Controllers\Teacher\AttendanceController::class, 'bulkStore'])->name('attendance.bulk-store');
        Route::post('attendance/students/{student}/days', [\App\Http\Controllers\Teacher\AttendanceController::class, 'updateAttendanceDays'])->name('attendance.update-attendance-days');
        Route::post('attendance/groups/{group}/bulk-days', [\App\Http\Controllers\Teacher\AttendanceController::class, 'bulkUpdateAttendanceDays'])->name('attendance.bulk-update-attendance-days');
        Route::get('messages', [\App\Http\Controllers\Teacher\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [\App\Http\Controllers\Teacher\MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/reply', [\App\Http\Controllers\Teacher\MessageController::class, 'reply'])->name('messages.reply');
        Route::post('messages/{message}/create-group', [\App\Http\Controllers\Teacher\MessageController::class, 'createGroupFromMessage'])->name('messages.create-group');
    });
});

// Payment webhook and success redirect (no auth required)
Route::get('/api/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');

// Locale switching route
Route::post('/locale/switch', [LocaleController::class, 'switch'])
    ->name('locale.switch');

require __DIR__.'/auth.php';
