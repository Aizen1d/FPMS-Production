<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FacultyController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//require_once(public_path('standards/validation_standards.blade.php')); // Custom validations and logged in auths 

// default landing page when server starts
Route::get('/', function () {
    // return view('welcome');
    //return redirect()->route('choose-login'); 
    if (!Auth::guard('faculty')->check() && !Auth::guard('admin')->check()) {
        return view('auth.choose_login');
    }
    else{
        return back();
    }
});

Route::fallback(function () {
    return 'Page not found';
});

// Middleware RevalidateBackHistory (Prevents clicking back button then bypassing sessions, cache etc.)
Route::group(['middleware'=>['RevalidateBackHistory']], function() {
    // So basically users cannot go back and forth when clicking the back button

    // Faculty Side (Faculty cannot logout then back to any faculty routes)
    Route::get('/login-faculty', [AuthController::class, 'loginFaculty'])->name("login-faculty");
    
    Route::get('/faculty-home', [FacultyController::class, 'showFacultyHome'])->name("faculty-home");
    
    Route::get('/faculty-home/department/bulletin', [FacultyController::class, 'showFacultyHomeBulletin'])->name("faculty-home/department/bulletin");
    
    Route::get('/faculty-home/department/members', [FacultyController::class, 'showFacultyHomeMembers'])->name("faculty-home/department/members");
    Route::get('/faculty-home/department/members/search', [FacultyController::class, 'showFacultyHomeMembersSearch'])->name("faculty-home/department/members/search");
    
    Route::get('/faculty-home/department/assigned-tasks', [FacultyController::class, 'showFacultyHomeAssignedTasks'])->name("faculty-home/department/assigned-tasks");
    Route::get('/faculty-home/department/assigned-tasks/completed', [FacultyController::class, 'showFacultyHomeAssignedTasksCategory'])->name("faculty-home/department/assigned-tasks/completed");
    Route::get('/faculty-home/department/assigned-tasks/late-completed', [FacultyController::class, 'showFacultyHomeAssignedTasksCategory'])->name("faculty-home/department/assigned-tasks/late-completed");
    Route::get('/faculty-home/department/assigned-tasks/ongoing', [FacultyController::class, 'showFacultyHomeAssignedTasksCategory'])->name("faculty-home/department/assigned-tasks/ongoing");
    Route::get('/faculty-home/department/assigned-tasks/missing', [FacultyController::class, 'showFacultyHomeAssignedTasksCategory'])->name("faculty-home/department/assigned-tasks/missing");
    Route::get('/faculty-home/department/assigned-tasks/category/search', [FacultyController::class, 'showFacultyHomeAssignedTasksCategorySearch'])->name("faculty-home/department/assigned-tasks/category/search");
    Route::get('/faculty-home/department/assigned-tasks/search', [FacultyController::class, 'showFacultyHomeAssignedTasksSearch'])->name("faculty-home/department/assigned-tasks/search");
    Route::post('/faculty-home/department/leave', [FacultyController::class, 'facultyDepartmentLeave'])->name("faculty-home/department/leave");

    Route::post('/faculty-home/join-department', [FacultyController::class, 'facultyHomeJoinDepartment'])->name("faculty-home/join-department");
    Route::get('/faculty-home/request-department', [FacultyController::class, 'showFacultyHomeRequestDepartment'])->name("faculty-home/request-department");

    Route::get('/faculty-tasks', [FacultyController::class, 'showFacultyTasks'])->name("faculty-tasks");
    Route::get('/faculty-tasks/category/search', [FacultyController::class, 'showFacultyMyTasksCategorySearch'])->name("faculty-tasks/category/search");
    Route::get('/faculty-tasks/completed', [FacultyController::class, 'showFacultyMyTasksCategory'])->name("faculty-tasks/completed");
    Route::get('/faculty-tasks/late-completed', [FacultyController::class, 'showFacultyMyTasksCategory'])->name("faculty-tasks/late-completed");
    Route::get('/faculty-tasks/ongoing', [FacultyController::class, 'showFacultyMyTasksCategory'])->name("faculty-tasks/ongoing");
    Route::get('/faculty-tasks/missing', [FacultyController::class, 'showFacultyMyTasksCategory'])->name("faculty-tasks/missing");

    Route::get('/faculty-tasks/get-task', [FacultyController::class, 'showFacultyGetTask'])->name("faculty-tasks/get-task");
    Route::get('/faculty-tasks/get-task/get-attachments', [FacultyController::class, 'facultyGetTaskGetAttachments'])->name("faculty-tasks/get-task/get-attachments");
    Route::get('/faculty-tasks/get-task/get-submissions-attachments', [FacultyController::class, 'facultyGetSubmissionsAttachments'])->name("faculty-tasks/get-task/get-submissions-attachments");
    Route::post('/faculty-tasks/get-task/download-file', [FacultyController::class, 'facultyGetTaskDownloadFile'])->name("faculty-tasks/get-task/download-file");
    Route::post('/faculty-tasks/get-task/download-all-file', [FacultyController::class, 'facultyGetTaskDownloadAllFile'])->name("faculty-tasks/get-task/download-all-file");
    Route::post('/faculty-tasks/get-task/download-all-file/delete-temp-zip', [FacultyController::class, 'facultyGetTaskDownloadAllFileDeleteTempZip'])->name("faculty-tasks/get-task/download-all-file/delete-temp-zip");
    Route::get('/faculty-tasks/get-task/preview-file-selected', [FacultyController::class, 'facultyGetTaskPreviewFileSelected'])->name("faculty-tasks/get-task/preview-file-selected");
    Route::get('/faculty-tasks/get-task/preview-file-selected/loading', function () {
        return view('layouts.fileIsLoading');
    });

    Route::get('/faculty-tasks/get-task/instructions', [FacultyController::class, 'showFacultyGetTaskInstructions'])->name("faculty-tasks/get-task/instructions");
    Route::post('/faculty-tasks/get-task/instructions/turn-in', [FacultyController::class, 'facultyInstructionsTaskTurnIn'])->name("faculty-tasks/get-task/instructions/turn-in");
    Route::post('/faculty-tasks/get-task/instructions/unsubmit', [FacultyController::class, 'facultyInstructionsTaskUnsubmit'])->name("faculty-tasks/get-task/instructions/unsubmit");

    Route::get('/faculty-profile', [FacultyController::class, 'showFacultyProfile'])->name("faculty-profile");
    Route::post('/faculty-profile/validate-fields', [FacultyController::class, 'facultyProfileValidateFields'])->name("faculty-profile/validate-fields");
    Route::post('/faculty-profile/save', [FacultyController::class, 'facultyProfileSave'])->name("faculty-profile/save");

    Route::get('/faculty-dashboard/my-tasks', [FacultyController::class, 'showFacultyDashboardMyTasks'])->name("faculty-dashboard/my-tasks");
    Route::get('/faculty-dashboard/assigned-task/timeline', [FacultyController::class, 'showFacultyDashboardAssignedTaskTimeline'])->name("faculty-dashboard/assigned-task/timeline");

    // Admin Side (Admin cannot logout then back to any admin routes)
    Route::get('/login-admin', [AuthController::class, 'loginAdmin'])->name("login-admin");
    Route::get('/admin-home', [AdminController::class, 'showAdminHome'])->name("admin-home");
    Route::get('/admin-home/show-department', [AdminController::class, 'setDepartmentToShow'])->name("admin-home/show-department");
    Route::get('/admin-home/show-department/return', [AdminController::class, 'removeDepartmentSessionOnReturn'])->name("admin-home/show-department/return");

    Route::get('/admin-home/show-department/bulletin', [AdminController::class, 'showAdminDepartmentBulletin'])->name("admin-home/show-department/bulletin");

    Route::get('/admin-home/show-department/members', [AdminController::class, 'showAdminDepartmentMembers'])->name("admin-home/show-department/members");
    Route::get('/admin-home/show-department/members/search', [AdminController::class, 'showAdminDepartmentMembersSearch'])->name("admin-home/show-department/members/search");

    Route::get('/admin-home/show-department/assigned-tasks', [AdminController::class, 'showAdminDepartmentAssignedTasks'])->name("admin-home/show-department/assigned-tasks");
    Route::get('/admin-home/show-department/assigned-tasks/completed', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-home/show-department/assigned-tasks/completed");
    Route::get('/admin-home/show-department/assigned-tasks/late-completed', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-home/show-department/assigned-tasks/late-completed");
    Route::get('/admin-home/show-department/assigned-tasks/ongoing', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-home/show-department/assigned-tasks/ongoing");
    Route::get('/admin-home/show-department/assigned-tasks/missing', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-home/show-department/assigned-tasks/missing");
    Route::get('/admin-home/show-department/assigned-tasks/search', [AdminController::class, 'adminDepartmentAssignedTasksSearch'])->name("admin-home/show-department/assigned-tasks/search");
    Route::post('/admin-home/show-department/assigned-tasks/create-task', [AdminController::class, 'adminDepartmentAssignedTasksCreateTask'])->name("admin-home/show-department/assigned-tasks/create-task");

    Route::get('/admin-tasks/get-task', [AdminController::class, 'showAdminGetTask'])->name("admin-tasks/get-task");
    Route::get('/admin-tasks/get-task/get-attachments', [AdminController::class, 'adminGetTaskGetAttachments'])->name("admin-tasks/get-task/get-attachments");
    Route::post('/admin-tasks/get-task/download-file', [AdminController::class, 'adminGetTaskDownloadFile'])->name("admin-tasks/get-task/download-file");
    Route::post('/admin-tasks/get-task/download-all-file', [AdminController::class, 'adminGetTaskDownloadAllFile'])->name("admin-tasks/get-task/download-all-file");
    Route::post('/admin-tasks/get-task/download-all-file/delete-temp-zip', [AdminController::class, 'adminGetTaskDownloadAllFileDeleteTempZip'])->name("admin-tasks/get-task/download-all-file/delete-temp-zip");
    Route::get('/admin-tasks/get-task/preview-file-selected', [AdminController::class, 'adminGetTaskPreviewFileSelected'])->name("admin-tasks/get-task/preview-file-selected");
    Route::get('/admin-tasks/get-task/preview-file-selected/loading', function () {
        return view('layouts.fileIsLoading');
    });

    Route::get('/admin-tasks/get-task/instructions', [AdminController::class, 'showAdminGetTaskInstructions'])->name("admin-tasks/get-task/instructions");
    Route::get('/admin-tasks/get-task/submissions', [AdminController::class, 'showAdminGetTaskSubmissions'])->name("admin-tasks/get-task/submissions");
    Route::post('/admin-tasks/get-task/submissions/get-attachments', [AdminController::class, 'showAdminGetTaskSubmissionsGetAttachments'])->name("admin-tasks/get-task/submissions/get-attachments");
    Route::post('/admin-tasks/get-task/submissions/decide', [AdminController::class, 'showAdminGetTaskSubmissionsDecide'])->name("admin-tasks/get-task/submissions/decide");
    Route::get('/admin-tasks/get-task/task-overview', [AdminController::class, 'showAdminGetTaskTaskOverview'])->name("admin-tasks/get-task/task-overview");

    Route::get('/admin-home/show-department/requests', [AdminController::class, 'showAdminDepartmentRequests'])->name("admin-home/show-department/requests");
    Route::get('/admin-home/show-department/overview', [AdminController::class, 'showAdminDepartmentOverview'])->name("admin-home/show-department/overview");

    Route::get('/admin-tasks', [AdminController::class, 'showAdminTasks'])->name("admin-tasks");
    Route::get('/admin-tasks/search', [AdminController::class, 'showAdminTasksSearch'])->name("admin-tasks/search");
    Route::get('/admin-tasks/category/search', [AdminController::class, 'showAdminTasksCategorySearch'])->name("admin-tasks/category/search");
    Route::get('/admin-tasks/department/category/search', [AdminController::class, 'showAdminTasksDepartmentCategorySearch'])->name("admin-tasks/department/category/search");
    Route::get('/admin-tasks/completed', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-tasks/completed");
    Route::get('/admin-tasks/late-completed', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-tasks/late-completed");
    Route::get('/admin-tasks/ongoing', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-tasks/ongoing");
    Route::get('/admin-tasks/missing', [AdminController::class, 'showAdminTasksGetCategory'])->name("admin-tasks/missing");

    Route::get('/admin-tasks/researches', [AdminController::class, 'showAdminTasksResearches'])->name("admin-tasks/researches");
    Route::get('/admin-tasks/researches/view', [AdminController::class, 'showAdminTasksResearchesView'])->name("admin-tasks/researches/view");
    Route::get('/admin-tasks/researches/search', [AdminController::class, 'showAdminTasksResearchesSearch'])->name("admin-tasks/researches/search");
    Route::get('/admin-tasks/researches/getAttachments', [AdminController::class, 'showAdminTasksResearchGetAttachments'])->name("admin-tasks/researches/getAttachments");
    Route::get('/admin-tasks/researches/attachment/preview', [AdminController::class, 'showAdminTasksResearchPreviewFileSelected'])->name("admin-tasks/researches/attachment/preview");
    
    Route::post('/admin-tasks/researches/create-research', [AdminController::class, 'adminCreateResearch'])->name("admin-tasks/researches/create-research");
    Route::post('/admin-tasks/researches/update', [AdminController::class, 'adminTasksResearchUpdate'])->name("admin-tasks/researches/update");
    Route::post('/admin-tasks/researches/delete', [AdminController::class, 'adminTasksResearchDelete'])->name("admin-tasks/researches/delete");

    Route::get('/admin-tasks/researches/presented', [AdminController::class, 'showAdminTasksResearchesPresented'])->name("admin-tasks/researches/presented");
    Route::get('/admin-tasks/researches/completed', [AdminController::class, 'showAdminTasksResearchesCompleted'])->name("admin-tasks/researches/completed");
    Route::get('/admin-tasks/researches/published', [AdminController::class, 'showAdminTasksResearchesPublished'])->name("admin-tasks/researches/published");
    Route::get('/admin-tasks/researches/category/search', [AdminController::class, 'showAdminTasksResearchesCategorySearch'])->name("admin-tasks/researches/category/search");

    Route::get('/admin-tasks/extensions', [AdminController::class, 'showAdminTasksExtensions'])->name("admin-tasks/extensions");
    Route::get('/admin-tasks/extensions/view', [AdminController::class, 'showAdminTasksExtensionsView'])->name("admin-tasks/extensions/view");
    Route::get('/admin-tasks/extensions/search', [AdminController::class, 'showAdminTasksExtensionsSearch'])->name("admin-tasks/extensions/search");
    Route::post('/admin-tasks/extensions/create', [AdminController::class, 'adminTasksExtensionsCreate'])->name("admin-tasks/extensions/create");
    Route::post('/admin-tasks/extensions/update', [AdminController::class, 'adminTasksExtensionsUpdate'])->name("admin-tasks/extensions/update");
    Route::post('/admin-tasks/extensions/delete', [AdminController::class, 'adminTasksExtensionsDelete'])->name("admin-tasks/extensions/delete");

    Route::get('/admin-tasks/attendance', [AdminController::class, 'showAdminTasksAttendance'])->name("admin-tasks/attendance");
    Route::get('/admin-tasks/attendance/view', [AdminController::class, 'showAdminTasksAttendanceView'])->name("admin-tasks/attendance/view");
    Route::get('/admin-tasks/attendance/search', [AdminController::class, 'showAdminTasksAttendanceSearch'])->name("admin-tasks/attendance/search");
    Route::post('/admin-tasks/attendance/create', [AdminController::class, 'adminTasksAttendanceCreate'])->name("admin-tasks/attendance/create");
    Route::post('/admin-tasks/attendance/update', [AdminController::class, 'adminTasksAttendanceUpdate'])->name("admin-tasks/attendance/update");
    Route::post('/admin-tasks/attendance/delete', [AdminController::class, 'adminTasksAttendanceDelete'])->name("admin-tasks/attendance/delete");
    Route::get('/admin-tasks/attendance/getAttachments', [AdminController::class, 'showAdminTasksAttendanceGetAttachments'])->name("admin-tasks/attendance/getAttachments");
    Route::get('/admin-tasks/attendance/attachment/preview', [AdminController::class, 'showAdminTasksAttendancePreviewFileSelected'])->name("admin-tasks/attendance/attachment/preview");

    Route::get('/admin-tasks/seminars', [AdminController::class, 'showAdminTasksSeminars'])->name("admin-tasks/seminars");
    Route::get('/admin-tasks/seminars/view', [AdminController::class, 'showAdminTasksSeminarsView'])->name("admin-tasks/seminars/view");
    Route::get('/admin-tasks/seminars/search', [AdminController::class, 'showAdminTasksSeminarsSearch'])->name("admin-tasks/seminars/search");
    Route::post('/admin-tasks/seminars/create', [AdminController::class, 'adminTasksSeminarsCreate'])->name("admin-tasks/seminars/create");
    Route::post('/admin-tasks/seminars/update', [AdminController::class, 'adminTasksSeminarsUpdate'])->name("admin-tasks/seminars/update");
    Route::post('/admin-tasks/seminars/delete', [AdminController::class, 'adminTasksSeminarsDelete'])->name("admin-tasks/seminars/delete");
    Route::get('/admin-tasks/seminars/getAttachments', [AdminController::class, 'showAdminTasksSeminarsGetAttachments'])->name("admin-tasks/seminars/getAttachments");
    Route::get('/admin-tasks/seminars/attachment/preview', [AdminController::class, 'showAdminTasksSeminarsPreviewFileSelected'])->name("admin-tasks/seminars/attachment/preview");

    Route::post('/admin-tasks/get-department-members', [AdminController::class, 'adminGetDepartmentMembers'])->name("/admin-tasks/get-department-members");
    Route::post('/admin-tasks/create-task', [AdminController::class, 'adminCreateTask'])->name("admin-tasks/create-task");
    Route::post('/admin-tasks/update-task', [AdminController::class, 'adminUpdateTask'])->name("admin-tasks/update-task");
    Route::post('/admin-tasks/filter-department', [AdminController::class, 'adminFilterDepartment'])->name("admin-tasks/filter-department");
    Route::post('/admin-tasks/category/filter-department', [AdminController::class, 'adminCategoryFilterDepartment'])->name("admin-tasks/category/filter-department");

    Route::get('/admin-requests/account', [AdminController::class, 'showAdminRequestsAccount'])->name("admin-requests/account");
    Route::get('/admin-requests/account/search', [AdminController::class, 'showAdminRequestsAccountSearch'])->name("admin-requests/account/search");

    Route::post('/admin-requests/account/accept', [AdminController::class, 'adminRequestsAccountAccept'])->name("admin-requests/account/accept");
    Route::post('/admin-requests/account/acceptAll', [AdminController::class, 'adminRequestsAccountAcceptAll'])->name("admin-requests/account/acceptAll");
    Route::post('/admin-requests/account/reject', [AdminController::class, 'adminRequestsAccountReject'])->name("admin-requests/account/reject");
    Route::post('/admin-requests/account/rejectAll', [AdminController::class, 'adminRequestsAccountRejectAll'])->name("admin-requests/account/rejectAll");

    Route::get('/admin-requests/department', [AdminController::class, 'showAdminRequestsDepartment'])->name("admin-requests/department");
    Route::get('/admin-requests/department/search', [AdminController::class, 'showAdminRequestsDepartmentSearch'])->name("admin-requests/department/search");

    Route::post('/admin-requests/department/accept', [AdminController::class, 'adminRequestsDepartmentAccept'])->name("admin-requests/department/accept");
    Route::post('/admin-requests/department/acceptAll', [AdminController::class, 'adminRequestsDepartmentAcceptAll'])->name("admin-requests/department/acceptAll");
    Route::post('/admin-requests/department/reject', [AdminController::class, 'adminRequestsDepartmentReject'])->name("admin-requests/department/reject");
    Route::post('/admin-requests/department/rejectAll', [AdminController::class, 'adminRequestsDepartmentRejectAll'])->name("admin-requests/department/rejectAll");

    Route::get('/admin-dashboard/department-task/statistics', [AdminController::class, 'showAdminDashboardDepartmentTaskStatistics'])->name("admin-dashboard/department-task/statistics");
    Route::get('/admin-dashboard/department-task/timeline', [AdminController::class, 'showAdminDashboardDepartmentTaskTimeline'])->name("admin-dashboard/department-task/timeline");
    Route::post('/admin-dashboard/department-task/get-statistics', [AdminController::class, 'showAdminDashboardDepartmentTaskGetStatistics'])->name("admin-dashboard/department-task/get-statistics");
    Route::post('/admin-dashboard/department-task/timeline/get-statistics', [AdminController::class, 'showAdminDashboardDepartmentTaskTimelineGetStatistics'])->name("admin-dashboard/department-task/get-statistics");

    Route::get('/admin-logs', [AdminController::class, 'showAdminLogs'])->name("admin-logs");
    Route::get('/admin-logs/search', [AdminController::class, 'showAdminLogsSearch'])->name("admin-logs/search");

    //Route::get('/admin-ranks', [AdminController::class, 'showAdminRanks'])->name("admin-ranks");
});

// Landing Page //
Route::get('/choose-login', [AuthController::class, 'chooseLogin'])->name("choose-login");

//////////////////////// Faculty routes ////////////////////////

// Faculty Register Login Logout Route // 
Route::get('/register-faculty', [AuthController::class, 'registerFaculty'])->name("register-faculty");
Route::post('/submit-register-faculty', [AuthController::class, 'submitRegisterFaculty'])->name("submit-register-faculty");
Route::post('/validate-login-faculty', [AuthController::class, 'validateLoginFaculty'])->name("validate-login-faculty");
Route::get('/logout-faculty', [AuthController::class, 'logoutFaculty'])->name("logout-faculty");

// Faculty Forgot Password Route //
Route::get('/faculty-password-forgot', [AuthController::class, 'showFacultyForgotForm'])->name("faculty-password-forgot"); 
Route::post('/faculty-password-link', [AuthController::class, 'sendFacultyResetLink'])->name("faculty-password-link"); 
Route::get('/faculty-password-reset{token}', [AuthController::class, 'showFacultyResetForm'])->name("faculty-password-reset-form"); 
Route::post('/faculty-password-reset', [AuthController::class, 'actionFacultyPasswordReset'])->name("faculty-password-reset-action"); 

//////////////////////// Admin Routes ////////////////////////

// Admin Register Login Logout Route // 
Route::get('/register-admin', [AuthController::class, 'registerAdmin'])->name("register-admin");
Route::post('/submit-register-admin', [AuthController::class, 'submitRegisterAdmin'])->name("submit-register-admin");
Route::post('/validate-login-admin', [AuthController::class, 'validateLoginAdmin'])->name("validate-login-admin")->middleware("throttle:5,3");
Route::get('/logout-admin', [AuthController::class, 'logoutAdmin'])->name("logout-admin");

// Admin Forgot Password Route //
Route::get('/admin-password-forgot', [AuthController::class, 'showAdminForgotForm'])->name("admin-password-forgot"); 
Route::post('/admin-password-link', [AuthController::class, 'sendAdminResetLink'])->name("admin-password-link"); 
Route::get('/admin-password-reset{token}', [AuthController::class, 'showAdminResetForm'])->name("admin-password-reset-form"); 
Route::post('/admin-password-reset', [AuthController::class, 'actionAdminPasswordReset'])->name("admin-password-reset-action"); 
