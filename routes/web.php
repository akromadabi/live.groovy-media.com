<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\SalaryController as AdminSalaryController;
use App\Http\Controllers\Admin\SalaryRecordController as AdminSalaryRecordController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SalarySchemeController as AdminSalarySchemeController;
use App\Http\Controllers\Admin\TiktokReportController as AdminTiktokReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\BonusTierController as AdminBonusTierController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\AttendanceController as UserAttendanceController;
use App\Http\Controllers\User\SettingsController as UserSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect home to login or dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    // Quick login for development
    Route::get('/quick-login/{role}', [LoginController::class, 'quickLogin'])->name('quick-login');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'role:admin']);

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Attendances
    Route::resource('attendances', AdminAttendanceController::class);
    Route::post('attendances/{attendance}/validate', [AdminAttendanceController::class, 'validate_attendance'])->name('attendances.validate');
    Route::post('attendances/{attendance}/reject', [AdminAttendanceController::class, 'reject'])->name('attendances.reject');
    Route::post('attendances/bulk-validate', [AdminAttendanceController::class, 'bulkValidate'])->name('attendances.bulk-validate');
    Route::post('attendances/bulk-delete', [AdminAttendanceController::class, 'bulkDelete'])->name('attendances.bulk-delete');
    Route::post('attendances/import', [AdminAttendanceController::class, 'import'])->name('attendances.import');
    Route::get('attendances/download-template', [AdminAttendanceController::class, 'downloadTemplate'])->name('attendances.download-template');

    // Salaries
    Route::resource('salaries', AdminSalaryController::class);
    Route::post('salaries/{salary}/recalculate', [AdminSalaryController::class, 'recalculate'])->name('salaries.recalculate');

    // Users
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Salary Schemes
    Route::get('salary-schemes', [AdminSalarySchemeController::class, 'index'])->name('salary-schemes.index');
    Route::get('salary-schemes/{user}/edit', [AdminSalarySchemeController::class, 'edit'])->name('salary-schemes.edit');
    Route::put('salary-schemes/{user}', [AdminSalarySchemeController::class, 'update'])->name('salary-schemes.update');
    Route::post('salary-schemes/apply-to-all', [AdminSalarySchemeController::class, 'applyToAll'])->name('salary-schemes.apply-to-all');

    // Bonus Scheme (simplified from tier system)
    Route::get('bonus-scheme', [AdminBonusTierController::class, 'index'])->name('bonus-tiers.index');
    Route::put('bonus-scheme', [AdminBonusTierController::class, 'update'])->name('bonus-scheme.update');

    // TikTok Reports
    Route::resource('tiktok-reports', AdminTiktokReportController::class)->except(['edit', 'update']);
    Route::post('tiktok-reports/details/{detail}/match-status', [AdminTiktokReportController::class, 'updateMatchStatus'])->name('tiktok-reports.update-match-status');

    // Settings
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/system', [AdminSettingsController::class, 'updateSystem'])->name('settings.update-system');
    Route::post('settings/test-whatsapp', [AdminSettingsController::class, 'testWhatsApp'])->name('settings.test-whatsapp');

    // Profile (separate from settings)
    Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('profile/update-profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update-profile');
    Route::put('profile/update-password', [AdminProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Salary Records (Manual Rekap Gaji)
    Route::resource('salary-records', AdminSalaryRecordController::class);
    Route::post('salary-records/{salary_record}/mark-paid', [AdminSalaryRecordController::class, 'markAsPaid'])->name('salary-records.mark-paid');
    Route::get('salary-records/{id}/slip', [AdminSalaryRecordController::class, 'downloadSlip'])->name('salary-records.slip');
    Route::post('salary-records-generate-all', [AdminSalaryRecordController::class, 'generateAll'])->name('salary-records.generate-all');
    Route::post('salary-records-bulk-download', [AdminSalaryRecordController::class, 'bulkDownload'])->name('salary-records.bulk-download');
});

// User Routes
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Attendances
    Route::get('attendances', [UserAttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/create', [UserAttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances', [UserAttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances/{attendance}/edit', [UserAttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{attendance}', [UserAttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('attendances/{attendance}', [UserAttendanceController::class, 'destroy'])->name('attendances.destroy');

    // Settings
    Route::get('settings', [UserSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/profile', [UserSettingsController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('settings/password', [UserSettingsController::class, 'updatePassword'])->name('settings.update-password');
});

// DEBUG ROUTE - Remove after fixing hosting issue
Route::get('/debug/hosting-check', function () {
    $user = \App\Models\User::where('role', 'user')->first();

    $data = [
        'server_time' => now()->toDateTimeString(),
        'server_timezone' => config('app.timezone'),
        'php_timezone' => date_default_timezone_get(),
        'settings' => [
            'bonus_pcs_threshold' => \App\Models\Setting::getValue('bonus_pcs_threshold', 'NOT_SET'),
            'bonus_amount' => \App\Models\Setting::getValue('bonus_amount', 'NOT_SET'),
        ],
        'sample_user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'has_salary_scheme' => $user->salaryScheme ? 'YES' : 'NO',
            'scheme_data' => $user->salaryScheme,
        ] : 'NO_USER_FOUND',
        'sample_attendances' => \App\Models\Attendance::with('user')
            ->where('sales_count', '>', 0)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'user' => $a->user->name,
                'date' => $a->attendance_date->toDateString(),
                'status' => $a->status,
                'sales' => $a->sales_count,
                'duration_minutes' => $a->live_duration_minutes,
            ]),
        'attendance_statuses' => \App\Models\Attendance::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get(),
    ];

    return response()->json($data, 200, [], JSON_PRETTY_PRINT);
})->name('debug.hosting');
