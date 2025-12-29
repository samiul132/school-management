<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashBankController;
use App\Http\Controllers\CashTransferController;
use App\Http\Controllers\AccountHeadController;
use App\Http\Controllers\SubsidiaryController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\SchoolSettingsController;
use App\Http\Controllers\SessionManagementController;
use App\Http\Controllers\MonthManagementController;
use App\Http\Controllers\ShiftManagementController;
use App\Http\Controllers\ClassManagementController;
use App\Http\Controllers\VersionManagementController;
use App\Http\Controllers\SectionManagementController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\FeeheadController;
use App\Http\Controllers\WaverController;
use App\Http\Controllers\FeeAssignController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectAssignController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AdvancePaymentController;
use App\Http\Controllers\OverTimeController;
use App\Http\Controllers\StaffSalaryController;
use App\Http\Controllers\StaffSalaryPaymentController;
use App\Http\Controllers\PostNotificationController;
use App\Http\Controllers\ExpoPushTokenController;

// Custom Auth Routes for React App
use App\Http\Controllers\CustomAuthController;

Route::post('/custom-login', [CustomAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/custom-logout', [CustomAuthController::class, 'logout']);
    Route::post('/custom-logout-all', [CustomAuthController::class, 'logoutAll']);
    Route::get('/custom-user', [CustomAuthController::class, 'user']);
    Route::post('/custom-change-password', [CustomAuthController::class, 'changePassword']);

    Route::put('/user/update-profile', [CustomAuthController::class, 'updateProfile']);

    Route::get('/payments/student-fee-summary/{classWiseStudentId}', [PaymentController::class, 'getStudentFeeSummary']);
});



// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// School Settings Route API
Route::middleware('auth:sanctum')->get('/current-school-settings', function () {
    $settings = app('school.settings');
    
    if (!$settings) {
        return response()->json([
            'success' => false,
            'message' => 'School settings not found'
        ], 404);
    }
    
    return response()->json([
        'success' => true,
        'data' => $settings
    ]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

	// Address routes
	Route::prefix('address')->group(function () {
		Route::get('/districts', [AddressController::class, 'getDistricts']);
		Route::get('/upazilas/{districtId}', [AddressController::class, 'getUpazilasByDistrict']);
	});

	// Cash/Bank Accounts Routes
	Route::apiResource('cash-banks', CashBankController::class);
	Route::get('/cash-banks/{id}/statistics', [CashBankController::class, 'statistics']);
	Route::get('/cash-banks/{id}/balance', [CashBankController::class, 'getBalance']);
	Route::post('/cash-banks/{id}/update-balance', [CashBankController::class, 'updateBalance']);
	Route::post('/cash-banks/bulk-update-balances', [CashBankController::class, 'bulkUpdateBalances']);

	// Cash Transfer Routes
	Route::apiResource('cash-transfers', CashTransferController::class);
	Route::get('/cash-transfers/statistics', [CashTransferController::class, 'statistics']);
	Route::get('/cash-transfers/by-date-range', [CashTransferController::class, 'getByDateRange']);
	Route::get('/cash-transfers/by-account/{accountId}', [CashTransferController::class, 'getByAccount']);

	// Account Heads Routes
	Route::get('/accountheads/statistics', [AccountHeadController::class, 'statistics']);
    Route::get('/accountheads/active', [AccountHeadController::class, 'getActive']);
    Route::post('/accountheads/bulk-status-update', [AccountHeadController::class, 'bulkStatusUpdate']);
    Route::apiResource('accountheads', AccountHeadController::class);

	// Subsidiaries Routes
	Route::apiResource('subsidiaries', SubsidiaryController::class);

	// Vouchers Routes
	Route::get('/vouchers/dropdown/data', [VoucherController::class, 'getDropdownData']);
	Route::apiResource('vouchers', VoucherController::class);

    // School Settings Routes
    Route::apiResource('school-settings', SchoolSettingsController::class);
	// Session Managements Routes
    Route::apiResource('session-managements', SessionManagementController::class);
	// Month Managements Routes
    Route::apiResource('month-managements', MonthManagementController::class);
	// Shift Managements Routes
    Route::apiResource('shift-managements', ShiftManagementController::class);
	// Class Managements Routes
    Route::apiResource('class-managements', ClassManagementController::class);
	// Version Managements Routes
    Route::apiResource('version-managements', VersionManagementController::class);
	// Section Managements Routes
    Route::apiResource('section-managements', SectionManagementController::class);

	// Student Profile Routes
	Route::get('/student-profiles/dropdown/data', [StudentProfileController::class, 'getDropdownData'])->name('student-profiles.dropdown-data');
	Route::apiResource('student-profiles', StudentProfileController::class);

	// FeeHead Routes
	Route::apiResource('fee-heads', FeeheadController::class);
	
	// Waver Routes
	Route::get('/student-profiles/{id}/with-class-data', [StudentProfileController::class, 'getStudentWithClassData']);
	Route::get('/wavers/dropdown-data', [WaverController::class, 'getDropdownData']);
	Route::get('/wavers/get-students-by-class-section', [WaverController::class, 'getStudentsByClassSection']);
	Route::get('/wavers/student/{class_wise_student_id}', [WaverController::class, 'getStudentWavers']);
	Route::get('/wavers/student/{class_wise_student_id}/edit-form', [WaverController::class, 'getStudentWaversForEdit']);
	Route::apiResource('wavers', WaverController::class);
	
	// FeeAssign Routes
	Route::get('/fee-assigns/dropdown-data', [FeeAssignController::class, 'getDropdownData']);
	Route::get('/fee-assigns/students-by-filter', [FeeAssignController::class, 'getStudentsByFilter']);
	Route::get('/fee-assigns/get-existing-data', [FeeAssignController::class, 'getExistingData']); 
	Route::apiResource('fee-assigns', FeeAssignController::class);

	// Payments
	Route::get('/payments/student-fees', [PaymentController::class, 'getStudentFees']);
	Route::get('/payments/student/{studentId}/history', [PaymentController::class, 'studentPaymentHistory']);
	Route::get('/payments/{id}/receipt', [PaymentController::class, 'generateReceipt']);
	Route::apiResource('payments', PaymentController::class);

	// Subject
	Route::apiResource('subjects', SubjectController::class);

	// Subject Assign
	Route::get('/subject-assigns/students-by-class', [SubjectAssignController::class, 'getStudentsByClass']);
	Route::post('/subject-assigns/assign-single-student', [SubjectAssignController::class, 'assignSingleStudent']);
    Route::apiResource('subject-assigns', SubjectAssignController::class);

	// Designations Routes
    Route::apiResource('designations', DesignationController::class);

    // Staffs Routes
    Route::apiResource('staffs', StaffController::class);

    // Attendance Routes 
    Route::get('attendance/summary', [AttendanceController::class, 'summary']);
    Route::get('attendance/staff-list', [AttendanceController::class, 'getStaffList']);
    Route::get('attendance/date/{date}', [AttendanceController::class, 'getByDate']);
    Route::apiResource('attendance', AttendanceController::class);

	// Leave Routes
    Route::get('leave/staff-list', [LeaveController::class, 'getStaffList']);
    Route::get('leave/staff/{staffId}', [LeaveController::class, 'getByStaff']);
    Route::get('leave/date-range', [LeaveController::class, 'getByDateRange']);
    Route::apiResource('leave', LeaveController::class);

    // Advance Payment Routes
    Route::get('advance-payment/staff-list', [AdvancePaymentController::class, 'getStaffList']);
    Route::get('advance-payment/accounts-list', [AdvancePaymentController::class, 'getAccountsList']);
    Route::get('advance-payment/staff/{staffId}', [AdvancePaymentController::class, 'getByStaff']);
    Route::get('advance-payment/date-range', [AdvancePaymentController::class, 'getByDateRange']);
    Route::apiResource('advance-payment', AdvancePaymentController::class);

    // Over Time Routes
    Route::apiResource('over-time', OverTimeController::class);

    // Staff Salary Routes
    Route::apiResource('staff-salary', StaffSalaryController::class);
    Route::post('staff-salary/generate', [StaffSalaryController::class, 'generate'])->name('staff-salary.generate');

    // Staff Salary Payment Routes
    Route::apiResource('staff-salary-payment', StaffSalaryPaymentController::class);
    Route::post('staff-salary-payment/prepare', [StaffSalaryPaymentController::class, 'prepare']);

	// Post Notification Routes
    Route::apiResource('post-notifications', PostNotificationController::class);
    
    // Expo Push Token Management
    Route::post('/save-push-token', [ExpoPushTokenController::class, 'saveToken']);
    Route::post('/remove-push-token', [ExpoPushTokenController::class, 'removeToken']);
});