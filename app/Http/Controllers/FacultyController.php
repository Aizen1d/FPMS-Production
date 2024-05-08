<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Faculty;
use App\Models\Departments;
use App\Models\DepartmentPendingJoins;
use App\Models\AdminTasks;
use App\Models\FacultyTasks;
use App\Models\Logs;
use Illuminate\Support\Facades\Storage;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Exception;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\AdminTasksResearchesPresented;
use App\Models\AdminTasksResearchesCompleted;
use App\Models\AdminTasksResearchesPublished;
use App\Models\Extension;
use App\Models\Attendance;
use App\Models\Seminars;
use App\Models\Functions;

// Import LengthAwarePaginator
use Illuminate\Pagination\LengthAwarePaginator;

require_once(resource_path('views/validation_standards.php'));

class FacultyController extends Controller
{
    function showFacultyHome() {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();

            if (empty($user->department)) { // if no join request has made yet
                $departments = Departments::paginate(6); // Retrieve (n) departments per page
                return view('faculty.faculty_home', ['departments' => $departments]);
            }
            else if (strpos($user->department, 'Pending') !== false) { // if the faculty user has join request pending
                $userDepartment = $user->department; // e.g Pending-BSIT
            
                $delimiter = '/'; // to get the department name after the Pending/ string
                $parts = explode($delimiter, $userDepartment);
                $departmentName = $parts[1]; // e.g BSIT
            
                $getDepartment = Departments::where('department_name', $departmentName)->first();
            
                return view('faculty.faculty_department_request', ['department' => $getDepartment]);
            }
            else{ // if faculty has a department
                return redirect('faculty-home/department/assigned-tasks');
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeBulletin() {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $department = Departments::where('department_name', $user->department)->first();

            if ($department) { // department exists in the departments table
                return view('faculty.faculty_home_department_bulletin');
            }
            else { // un-authorized
                return back();
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeMembers() {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $department = Departments::where('department_name', $user->department)->first();

            if ($department) { // department exists in the departments table
                $members = Faculty::where('department', $user->department)
                            ->orderBy('first_name', 'asc')
                            ->paginate(9);
                $numberOfMembers = Departments::where('department_name', $user->department)->first();

                return view('faculty.faculty_home_department_members', ['items' => $members, 
                                                                        'departmentName' => $user->department,
                                                                        'numberOfMembers' => $numberOfMembers]);
            }
            else { // un-authorized
                return back();
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeMembersSearch(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $department = Departments::where('department_name', $user->department)->first();

            if ($department) { // department exists in the departments table
                $query = $request->input('query');
        
                if (empty($query)) {
                    $items = Faculty::where('department', $user->department)
                            ->orderBy('first_name', 'asc')
                            ->paginate(9);
                } 
                else {
                    $queryParts = explode(' ', $query);
                    $items = Faculty::where('department', $user->department)
                        ->where(function ($q) use ($queryParts) {
                            foreach ($queryParts as $queryPart) {
                                $q->orWhere('first_name', 'like', "%{$queryPart}%")
                                    ->orWhere('middle_name', 'like', "%{$queryPart}%")
                                    ->orWhere('last_name', 'like', "%{$queryPart}%");
                            }
                        })
                        ->orderBy('first_name', 'asc')
                        ->paginate(9);
                }
            
                $formattedTasks = $items->map(function($item) {
                    return [
                        'fullname' => $item->first_name . ' ' . $item->middle_name . ' ' . $item->last_name,
                        'join_date_formatted' => Carbon::parse($item->department_join_date)->format('F j, Y'),
                        'join_date_time' => Carbon::parse($item->department_join_date)->format('g:i A'),
                    ];
                });
            
                return response()->json($formattedTasks);
            }
            else { // un-authorized
                return back();
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeAssignedTasks() {
        if (Auth::guard('faculty')->check()) {
            // Get the logged-in user
            $user = Auth::guard('faculty')->user();
            $department = $user->department;

            $tasks = AdminTasks::where('faculty_name', $department)
                ->orderBy('date_created', 'desc')
                ->paginate(9);

            $fullname = $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name;     
            $assignedTasksOnly = AdminTasks::whereRaw("assigned_to LIKE '%$fullname%'")->get();

            $clickableRows = [];
            foreach ($assignedTasksOnly as $task) {
                $clickableRows[] = $task->task_name;
            }
            
            return view('faculty.faculty_home_department_assigned_tasks', ['tasks' => $tasks,
                                                                           'clickableRows' => $clickableRows], ['department' => $department]);            
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeAssignedTasksSearch(Request $request) {
        if (Auth::guard('faculty')->check()) {
            // Get the logged-in user
            $user = Auth::guard('faculty')->user();
            $department = $user->department;

            $query = $request->input('query');
            $fullname = $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name; 

            if (empty($query)) {
                $tasks = AdminTasks::where('faculty_name', $department)
                    ->whereRaw("assigned_to REGEXP ?", ["(^|,\\s*){$fullname}(\\s*,|$)"])
                    ->orderBy('date_created', 'desc')
                    ->paginate(9);

            } else {
                $tasks = AdminTasks::where('task_name', 'like', "%{$query}%")
                    ->whereRaw("assigned_to REGEXP ?", ["(^|,\\s*){$fullname}(\\s*,|$)"])
                    ->where('faculty_name', $department)
                    ->orderBy('date_created', 'desc')
                    ->paginate(9);
            }
    
            $assignedTasksOnly = AdminTasks::whereRaw("assigned_to REGEXP ?", ["(^|,\\s*){$fullname}(\\s*,|$)"])->get();

            $clickableRows = [];
            foreach ($assignedTasksOnly as $task) {
                $clickableRows[] = $task->task_name;
            }

            $formattedTasks = $tasks->map(function ($task) {
                return [
                    'task_name' => $task->task_name,
                    'faculty_image' => $task->faculty_image_link,
                    'faculty_name' => $task->faculty_name,
                    'date_created_formatted' => Carbon::parse($task->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($task->created_at)->format('g:i A'),
                    'due_date_formatted' => Carbon::parse($task->due_date)->format('F j, Y'),
                    'due_date_time' => Carbon::parse($task->due_date)->format('g:i A'),
                    'due_date_past' => Carbon::parse($task->due_date)->isPast(),
                    'status' => $task->FacultyTasks->first()->status,
                ];
            });

            return response()->json([
                'fetchTasks' => $formattedTasks,
                'clickableRows' => $clickableRows,
            ]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeAssignedTasksCategory(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $userid = $user->id;
            $department = $user->department;
            $category = $request->input('category'); 
            $route = $request->route();
            
            if ($category === 'completed') {
                $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                    $query->where('status', 'Completed');
                })->whereDoesntHave('FacultyTasks', function ($query) {
                    $query->where('status', '!=', 'Completed');
                })->where('faculty_name', $department)
                ->orderBy('created_at', 'desc')
                  ->paginate(9);

                return view('faculty.faculty_home_department_assigned_tasks_completed', ['tasks' => $tasks], ['department' => $department]);
            }
            else if ($category === 'late-completed') {
                $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                    $query->where('status', 'Late Completed');
                })->whereDoesntHave('FacultyTasks', function ($query) {
                    $query->where('status', '!=', 'Late Completed');
                })->where('faculty_name', $department)
                ->orderBy('created_at', 'desc')
                  ->paginate(9);

                return view('faculty.faculty_home_department_assigned_tasks_late_completed', ['tasks' => $tasks], ['department' => $department]);
            }
            else if ($category === 'ongoing') {
                $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                    $query->where('status', 'Ongoing');
                })->where('faculty_name', $department)
                ->orderBy('created_at', 'desc')
                  ->paginate(9);

                return view('faculty.faculty_home_department_assigned_tasks_ongoing', ['tasks' => $tasks], ['department' => $department]);
            }
            else if ($category === 'missing') {
                $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                    $query->where('status', 'Missing');
                })->where('faculty_name', $department)
                ->orderBy('created_at', 'desc')
                  ->paginate(9);

                return view('faculty.faculty_home_department_assigned_tasks_missing', ['tasks' => $tasks], ['department' => $department]);
            }
            else {
                return back();
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyHomeAssignedTasksCategorySearch(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $userid = $user->id;
            $category = $request->input('category'); 
            $department = $request->input('department');
            $query = $request->input('query');

            if ($category === 'completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->where('task_name', 'like', "%{$query}%")
                        ->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'late-completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->where('faculty_name', $department)
                    ->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'ongoing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Ongoing');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Ongoing');
                    })->where('faculty_name', $department)
                    ->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'missing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Missing');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Missing');
                    })->where('task_name', 'like', "%{$query}%")
                        ->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }

            $formattedTasks = $tasks->map(function ($task) {
                return [
                    'task_name' => $task->task_name,
                    'faculty_image' => $task->faculty_image_link,
                    'faculty_name' => $task->faculty_name,
                    'date_created_formatted' => Carbon::parse($task->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($task->created_at)->format('g:i A'),
                    'due_date_formatted' => Carbon::parse($task->due_date)->format('F j, Y'),
                    'due_date_time' => Carbon::parse($task->due_date)->format('g:i A'),
                    'due_date_past' => Carbon::parse($task->due_date)->isPast(),
                ];
            });

            return response()->json($formattedTasks);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyHomeJoinDepartment(Request $request) {
        if (Auth::guard('faculty')->check()) {
            // Get the logged-in user
            $user = Auth::guard('faculty')->user();
            $department = $request->input('department');

            if (empty($user->first_name) || empty($user->last_name)) {
                return response()->json([
                    'success' => false
                ]);
            }

            // If the faculty has successfully requested for department join.
            Faculty::where('id', $user->id)
            ->update([
                'department' => 'Pending/' . $department,
            ]);

            $join = new DepartmentPendingJoins;
            $join->faculty_id = $user->id;
            $join->department_name = $department;
            $join->save();

            $faculty = $user;
            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $faculty->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $facultyUsername . ') ' . $facultyFullName . ' has requested to join the ' . $department . ' department.',
                'type_of_action' => 'Join Department',
            ]);

            return response()->json([
                'success' => true
            ]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    public function showFacultyHomeRequestDepartment(){
        if (Auth::guard('faculty')->check()) {
            return view('faculty.faculty_department_request');
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasks() {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $department = $user->department;
            $userFullName = $user->first_name . ($user->middle_name ? ' ' . $user->middle_name : '') . ' ' . $user->last_name;
    
            $tasks = AdminTasks::whereRaw("assigned_to REGEXP ?", ["(^|,\\s*){$userFullName}(\\s*,|$)"])
                ->with(['FacultyTasks' => function ($query) use ($user) {
                    $query->select('task_id', 'status')
                        ->where('submitted_by_id', $user->id);
                }])
                ->orderBy('date_created', 'desc')
                ->paginate(9);

            return view('faculty.faculty_tasks', ['tasks' => $tasks], ['department' => $department]);
        }
    }

    function getCategoryTask($status, $userid) {
        $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($status, $userid) {
            $query->where('status', $status);
            $query->where('submitted_by_id', $userid);
        })->orderBy('created_at', 'desc')
          ->paginate(9);
    
        return $tasks;
    } 

    function getCategoryTaskSearch($status, $userid) {
        $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($status, $userid) {
            $query->where('status', $status);
            $query->where('submitted_by_id', $userid);
        })->orderBy('created_at', 'desc')
          ->paginate(9);
    
        return $tasks;
    } 

    function showFacultyMyTasksCategory(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $userid = $user->id;
            $department = $user->department;
            $category = $request->input('category'); 
            $route = $request->route();
            
            if ($category === 'completed') {
                $tasks = $this->getCategoryTask('Completed', $userid);

                if (Str::startsWith($route->getName(), 'faculty-tasks')) {
                    return view('faculty.faculty_tasks_completed', ['tasks' => $tasks], ['department' => $department]);
                }
                else {
                    return view('admin.admin_show_department_assigned_tasks_completed', ['tasks' => $tasks, 'department' => $department]);
                }
            }
            else if ($category === 'late-completed') {
                $tasks = $this->getCategoryTask('Late Completed', $userid);

                if (Str::startsWith($route->getName(), 'faculty-tasks')) {
                    return view('faculty.faculty_tasks_late_completed', ['tasks' => $tasks], ['department' => $department]);
                }
                else {
                    return view('admin.admin_show_department_assigned_tasks_completed', ['tasks' => $tasks, 'department' => $department]);
                }
            }
            else if ($category === 'ongoing') {
                $tasks = $this->getCategoryTask('Ongoing', $userid);

                if (Str::startsWith($route->getName(), 'faculty-tasks')) {
                    return view('faculty.faculty_tasks_ongoing', ['tasks' => $tasks], ['department' => $department]);
                }
                else {
                    return view('admin.admin_show_department_assigned_tasks_completed', ['tasks' => $tasks, 'department' => $department]);
                }
            }
            else if ($category === 'missing') {
                $tasks = $this->getCategoryTask('Missing', $userid);

                if (Str::startsWith($route->getName(), 'faculty-tasks')) {
                    return view('faculty.faculty_tasks_missing', ['tasks' => $tasks], ['department' => $department]);
                }
                else {
                    return view('admin.admin_show_department_assigned_tasks_completed', ['tasks' => $tasks, 'department' => $department]);
                }
            }
            else {
                return back();
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyMyTasksCategorySearch(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $userid = $user->id;
            $category = $request->input('category'); 
            $query = $request->input('query');

            if ($category === 'completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Completed');
                        $query->where('submitted_by_id', $userid);
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);
                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Completed');
                        $query->where('submitted_by_id', $userid);
                    })->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'late-completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Late Completed');
                        $query->where('submitted_by_id', $userid);
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Late Completed');
                        $query->where('submitted_by_id', $userid);
                    })->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'ongoing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Ongoing');
                        $query->where('submitted_by_id', $userid);
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Ongoing');
                        $query->where('submitted_by_id', $userid);
                    })->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'missing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Missing');
                        $query->where('submitted_by_id', $userid);
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($userid) {
                        $query->where('status', 'Missing');
                        $query->where('submitted_by_id', $userid);
                    })->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }

            $formattedTasks = $tasks->map(function ($task) {
                return [
                    'task_name' => $task->task_name,
                    'faculty_image' => $task->faculty_image_link,
                    'faculty_name' => $task->faculty_name,
                    'date_created_formatted' => Carbon::parse($task->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($task->created_at)->format('g:i A'),
                    'due_date_formatted' => Carbon::parse($task->due_date)->format('F j, Y'),
                    'due_date_time' => Carbon::parse($task->due_date)->format('g:i A'),
                    'due_date_past' => Carbon::parse($task->due_date)->isPast(),
                ];
            });

            return response()->json($formattedTasks);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyGetTask(Request $request) {
        if ($request->input('taskName')) {
            $taskName = $request->input('taskName');
            $request->session()->put('taskName', $taskName);

            if ($request->input('requestSource') == 'department') {
                $request->session()->put('selectedIn', 'department');
            }
            else if ($request->input('requestSource') == 'navbar') {
                $request->session()->put('selectedIn', 'navbar');
            }

            if ($request) {
                return redirect('faculty-tasks/get-task/instructions');
            }
            else {
                return back();
            }
        } else {
            return back();
        }
    }

    function facultyGetTaskGetAttachments(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $folderPath = $request->input('folderPath');
            $fileNames = [];
        
            // Get all the contents in the specified directory
            if ($folderPath !== null) {
                $files = Storage::disk('google')->listContents($folderPath);
    
                if (!empty($files)) {
                    foreach ($files as $file) {
                        // Get the file name
                        $fileName = basename($file['path']);

                        // Check if this is a folder with the name 'Submissions'
                        if ($file['type'] === 'dir' && $fileName === 'Submissions') {
                            // Skip this folder
                            continue;
                        }

                        // Get the mime type
                        $mimeType = $file['mimeType'];

                        // Check if this is a zip file
                        if ($mimeType === 'application/zip') {
                            continue;
                        }
    
                        // Add the file name to the array
                        $fileNames[] = $fileName;
                    }
                }
            }
        
            return response()->json($fileNames);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyGetSubmissionsAttachments(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $folderPath = $request->input('folderPath');
            $fileNames = [];
        
            // Get all the contents in the specified directory
            if ($folderPath !== null) {
                $files = Storage::disk('google')->listContents($folderPath);
    
                if (!empty($files)) {
                    foreach ($files as $file) {
                        // Get the file name
                        $fileName = basename($file['path']);

                        // Check if this is a folder with the name 'Submissions'
                        if ($file['type'] === 'dir' && $fileName === 'Submissions') {
                            // Skip this folder
                            continue;
                        }

                        // Get the mime type
                        $mimeType = $file['mimeType'];

                        // Check if this is a zip file
                        if ($mimeType === 'application/zip') {
                            continue;
                        }
    
                        // Add the file name to the array
                        $fileNames[] = $fileName;
                    }
                }
            }
        
            return response()->json($fileNames);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyGetTaskPreviewFileSelected(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $department = $request->input('department');
            $taskName = $request->input('taskName');
            $filename = $request->input('filename');
    
            // Set up the Google Drive API client
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
    
            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);
    
            // Find the file on Google Drive
            $file = 'Created Tasks/Departments/' . $department . '/Tasks/' . $taskName . '/' . $filename;
            $results = $service->files->listFiles([
                'q' => "name = '$filename'",
                'fields' => 'files(id, webViewLink)',
            ]);
    
            // Check if the file was found
            if (count($results->getFiles()) === 0) {
                // File not found
                return response()->json(['error' => 'File not found']);
            } else {
                // Get the file's metadata
                $file = $results->getFiles()[0];
    
                // Get the webViewLink property
                $url = $file->getWebViewLink();
    
                return response()->json(['url' => $url]);
            }
        } else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyGetTaskDownloadFile(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $department = $request->input('department');
            $taskName = $request->input('taskName');
            $filename = $request->input('filename');
    
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
    
            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);
    
            try {
                // Find the file on Google Drive
                $file = 'Created Tasks/Departments/' . $department . '/Tasks/' . $taskName . '/' . $filename;
                $results = $service->files->listFiles([
                    'q' => "name = '$filename'",
                    'fields' => 'files(id)',
                ]);
    
                // Check if the file was found
                if (count($results->getFiles()) === 0) {
                    // File not found
                    return response()->json(['error' => 'File not found']);
                } else {
                    // Get the file's metadata
                    $file = $results->getFiles()[0];

                    $faculty = Auth::guard('faculty')->user();
                    $facultyUsername = $faculty->username;
                    $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

                    Logs::create([
                        'user_id' => $faculty->id,
                        'user_role' => 'Faculty',
                        'action_made' => '(' . $facultyUsername . ') ' . $facultyFullName . ' has downloaded an attachment file named ' . $filename . '.',
                        'type_of_action' => 'Download Attachment File',
                    ]);
    
                    // Return the file ID in the response
                    return response()->json(['fileId' => $file->getId()]);
                }
            } catch (Exception $e) {
                // An error occurred
                return response()->json(['error' => $e->getMessage()]);
            }
        } else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyGetTaskDownloadAllFileDeleteTempZip(Request $request) {
        if (Auth::guard('faculty')->check()) {
            // Get the file name from the request
            $fileName = $request->input('zipName');

            // Set up the Google API client
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));

            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);

            try {
                // Find the file or folder on Google Drive
                $results = $service->files->listFiles([
                    'q' => "name='$fileName'",
                    'fields' => 'files(id)',
                ]);

                // Check if the file or folder was found
                if (count($results->getFiles()) === 0) {
                    // File or folder not found
                    return response()->json(['error' => 'File or folder not found']);
                } else {
                    // Get the file or folder ID
                    $fileId = $results->getFiles()[0]->getId();

                    // Delete the file or folder from Google Drive
                    $service->files->delete($fileId);

                    // Return a success message in the response
                    return response()->json(['success' => 'File or folder deleted successfully']);
                }
            } catch (Exception $e) {
                // An error occurred
                return response()->json(['error' => $e->getMessage()]);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyGetTaskDownloadAllFile(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $folderPath = $request->input('folderPath');
            $department = trim($request->input('department'));
            $taskName = trim($request->input('taskName'));
    
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
    
            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);

            // Set the path to the task folder
            $folderPath = 'Created Tasks/Departments/' . $department . '/Tasks/' . $taskName;

            // Split the path into its individual components
            $pathComponents = explode('/', $folderPath);

            // Start at the root folder
            $folderId = 'root';

            // Search for each folder in the path one at a time
            foreach ($pathComponents as $folderName) {
                // Find the folder on Google Drive
                $results = $service->files->listFiles([
                    'q' => "mimeType='application/vnd.google-apps.folder' and trashed = false and name='$folderName' and '$folderId' in parents",
                    'fields' => 'files(id)',
                ]);

                // Check if the folder was found
                if (count($results->getFiles()) == 0) {
                    // Folder not found, handle error
                    // ...
                    break;
                }

                // Get the folder ID
                $folderId = $results->getFiles()[0]->getId();
            }

            // The $folderId variable now contains the ID of the folder specified by the path
            
            try {
                // Get the folder ID
            $folderId = $results->getFiles()[0]->getId();

            // Find all files inside the folder
            $results = $service->files->listFiles([
                'q' => "'$folderId' in parents",
                'fields' => 'files(id, name)',
            ]);

            // Create a new zip archive
            $zip = new ZipArchive();
            $zipFilename = tempnam(sys_get_temp_dir(), 'zip');
            if ($zip->open($zipFilename, ZipArchive::CREATE) !== true) {
                return response()->json(['error' => 'Failed to create zip file']);
            }

            // Add each file to the zip archive
            foreach ($results->getFiles() as $file) {
                // Get the file ID and name
                $fileId = $file->getId();
                $fileName = $file->getName();
                $mimeType = $file->getMimeType();

                // Skip adding the zip file to itself
                if ($fileName == $taskName . ' Task') {
                    continue;
                }

                // Skip adding folders
                if ($mimeType == 'application/vnd.google-apps.folder') {
                    continue;
                }

                // Download the file content
                $content = $service->files->get($fileId, ['alt' => 'media'])->getBody();

                // Add the file to the zip archive
                $zip->addFromString($fileName, $content);
            }

            // Close the zip archive
            $zip->close();

            // Upload the zip file to Google Drive
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $taskName . ' Task',
                'parents' => [$folderId],
            ]);

            $content = file_get_contents($zipFilename);
            $file = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/zip',
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]);

            // Delete the temporary zip file
            unlink($zipFilename);

            // Return the zip file ID in the response
            return response()->json(['fileId' => $file->getId(),
                                     'fileName' => $taskName]);

            }
            catch (Exception $e) {
                // An error occurred
                return response()->json(['error' => $e->getMessage()]);
            }
            
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }
    
    
    function showFacultyGetTaskInstructions(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $taskName = $request->session()->get('taskName');
            $user = Auth::guard('faculty')->user();
            $userId = $user->id;

            if ($taskName) {
                $adminTaskTable = AdminTasks::where('task_name', $taskName)->first();
                $taskID = $adminTaskTable->id;
                $departmentName = $adminTaskTable->faculty_name;
                $assignedto = $adminTaskTable->assigned_to;
                $due_date = $adminTaskTable->due_date;
                $description = $adminTaskTable->description;

                $members = Faculty::where('department', $departmentName)->get();
                $departments = Departments::all();

                $folderPath = $adminTaskTable->attachments;
                $assignedMembers = explode(',', $assignedto);
                $numberOfAssignedMembers = count($assignedMembers);

                $getUserInFacultyTasks = FacultyTasks::where('submitted_by_id', $userId)
                                     ->where('task_id', $taskID)
                                     ->first();

                if ($getUserInFacultyTasks->date_submitted) {
                    // Parse and format the date and time
                    $turnedInDate = Carbon::parse($getUserInFacultyTasks->date_submitted)->format('F j, Y');
                    $turnedInTime = Carbon::parse($getUserInFacultyTasks->date_submitted)->format('g:i A');
                } 
                else {
                    // Set the turned in date and time to null
                    $turnedInDate = null;
                    $turnedInTime = null;
                }

                $submissionAttachments = $getUserInFacultyTasks->attachments;
                $submissionDescription = $getUserInFacultyTasks->description;
                $decision = $getUserInFacultyTasks->decision;
                
                $isTurnedIn = $getUserInFacultyTasks->date_submitted;

                return view('faculty.faculty_get_task_instructions', [
                    'taskName' => $taskName,
                    'taskID' => $taskID,
                    'departmentName' => $departmentName,
                    'departments' => $departments,
                    'assignedto' => $assignedto,
                    'due_date' => $due_date,
                    'date_created_date' => Carbon::parse($adminTaskTable->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($adminTaskTable->created_at)->format('g:i A'),
                    'date_updated_date' => Carbon::parse($adminTaskTable->updated_at)->format('F j, Y'),
                    'date_updated_time' => Carbon::parse($adminTaskTable->updated_at)->format('g:i A'),
                    'date_due_date' => Carbon::parse($adminTaskTable->due_date)->format('F j, Y'),
                    'date_due_time' => Carbon::parse($adminTaskTable->due_date)->format('g:i A'),
                    'description' => $description,
                    'folderPath' => $folderPath,
                    'assignedMembers' => $assignedMembers,
                    'numberOfAssignedMembers' => $numberOfAssignedMembers,
                    'turnedInDate' => $turnedInDate,
                    'turnedInTime' => $turnedInTime,
                    'submissionAttachments' => $submissionAttachments,
                    'submissionDescription' => $submissionDescription,
                    'isTurnedIn' => $isTurnedIn,
                    'decision' => $decision,
                ], ['members' => $members]);
            }
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyInstructionsTaskTurnIn(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $userId = $user->id;
            $fullname = $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name;  

            $faculty = $request->input('faculty');
            $taskname = $request->input('taskname');
            $description = $request->input('description');
            $files = $request->file('files');

            if ($files) {
                $taskPath = 'Created Tasks/Departments/' . $faculty . '/Tasks/' . $taskname;
                $submissionsPath = $taskPath . '/Submissions';
                $fullnamePath = $submissionsPath . '/' . $fullname;

                // Check if the task directory exists
                if (!Storage::disk('google')->exists($taskPath)) {
                    // The task directory does not exist, so create it
                    Storage::disk('google')->makeDirectory($taskPath);
                    Storage::disk('google')->setVisibility($taskPath, 'public');
                }

                // Check if the Submissions directory exists
                if (!Storage::disk('google')->exists($submissionsPath)) {
                    // The Submissions directory does not exist, so create it
                    Storage::disk('google')->makeDirectory($submissionsPath);
                    Storage::disk('google')->setVisibility($submissionsPath, 'public');
                }

                // Check if the fullname directory exists
                if (!Storage::disk('google')->exists($fullnamePath)) {
                    // The fullname directory does not exist, so create it
                    Storage::disk('google')->makeDirectory($fullnamePath);
                    Storage::disk('google')->setVisibility($fullnamePath, 'public');
                }

                $adapter = Storage::disk('google')->getAdapter();
                $metadata = $adapter->getMetadata('Created Tasks/Departments/' . $faculty . '/Tasks/' . $taskname . '/Submissions/' . $fullname);
                $id = $metadata['path'];

                // Get the path to the Submissions folder
                $folderPath = 'Created Tasks/Departments/' . $faculty . '/Tasks/' . $taskname . '/Submissions/' . $fullname;

                // Store files in '$taskName' folder
                foreach ($files as $file) {
                    // Get the file name
                    $fileName = $file->getClientOriginalName();

                    // Check if this file already exists in the Submissions folder
                    if (!Storage::disk('google')->exists($folderPath . '/' . $fileName)) {
                        // The file does not exist, so upload it
                        Storage::disk('google')->putFileAs(
                            $folderPath,
                            $file,
                            $fileName
                        );
                        Storage::disk('google')->setVisibility($folderPath . '/' . $fileName, 'public');
                    }
                }

                // Remove files from the new folder that wasn't found on $files array
                $folderPath = 'Created Tasks/Departments/' . $faculty . '/Tasks/' . $taskname . '/Submissions/' . $fullname;
                $filesInFolder = Storage::disk('google')->files($folderPath);

                foreach ($filesInFolder as $fileInFolder) {
                    $found = false;
                    foreach ($files as $file) {
                        if ($file->getClientOriginalName() == basename($fileInFolder)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        Storage::disk('google')->delete($fileInFolder);
                    }
                }
            }
            else{
                // Delete the folder since no file
                Storage::disk('google')->delete('Created Tasks/Departments/' . $faculty . '/Tasks/' . $taskname . '/Submissions/' . $fullname);
                $id = null;
            }

            $adminTaskTable = AdminTasks::where('task_name', $taskname)->first();
            $taskId = $adminTaskTable->id;

            $submission = FacultyTasks::where('submitted_by_id', $userId)
                        ->where('task_id', $taskId)
                        ->first();

            $deadline = AdminTasks::where('task_name', $taskname)->first();
            $deadline = $deadline->due_date;
            $deadline = Carbon::parse($deadline);

            $submission->description = $description;
            $submission->attachments = $id;

            if ($deadline->isFuture()) {
                $submission->status = 'Completed';
            } 
            else {
                $submission->status = 'Late Completed';
            }

            if ($submission->decision) {
                $outputStatus = $submission->decision;
            }
            else{
                $submission->decision = 'Not decided';
                $outputStatus = $submission->decision;
            }

            $submission->date_submitted = Carbon::now();
            $submission->save();

            $date_submitted_date = Carbon::parse($submission->date_submitted)->format('F j, Y');
            $date_submitted_time = Carbon::parse($submission->date_submitted)->format('g:i A');

            $faculty = Auth::guard('faculty')->user();
            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $faculty->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $facultyUsername . ') ' . $facultyFullName . ' has turned-in a task named ' . $taskname . '.',
                'type_of_action' => 'Turn-in Task',
            ]);

            return response()->json(['date_submitted' => $submission->date_submitted,
                                     'date_submitted_date' => $date_submitted_date,
                                     'date_submitted_time' => $date_submitted_time,
                                     'submit_status' => $submission->status,
                                     'output_status' => $outputStatus,
                                    ]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyInstructionsTaskUnsubmit(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $userId = $user->id;
            $taskname = $request->input('taskname');

            $adminTaskTable = AdminTasks::where('task_name', $taskname)->first();
            $taskId = $adminTaskTable->id;

            $submission = FacultyTasks::where('submitted_by_id', $userId)
                        ->where('task_id', $taskId)
                        ->first();

            $deadline = AdminTasks::where('task_name', $taskname)->first();
            $deadline = $deadline->due_date;
            $deadline = Carbon::parse($deadline);

            if ($deadline) {
                if ($deadline->isFuture()) {
                    $submission->status = 'Ongoing';
                } 
                else {
                    $submission->status = 'Missing';
                }
            }

            $submission->date_submitted = null;
            $submission->save();

            $faculty = Auth::guard('faculty')->user();
            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $faculty->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $facultyUsername . ') ' . $facultyFullName . ' has unsubmitted a task named ' . $taskname . '.',
                'type_of_action' => 'Unsubmit Task',
            ]);
            
            return response()->json(['date_submitted' => $submission->date_submitted]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    } 

    function showFacultyProfile() {
        if (Auth::guard('faculty')->check()) {
            $username = Auth::guard('faculty')->user()->username;
            $data = Faculty::where('username', $username)->first();
            return view('faculty.faculty_profile', ['data' => $data]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyProfileValidateFields(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $field = $request->input('field');
            $data = $request->input('data');
            $errors = array();
    
            // Get all validation rules
            $validationRules = getFacultyProfileValidation();

            if ($request->input('requestMessage') == 'activateFullNameValidation') {
                $firstnameValidator = Validator::make(['firstname' => $request->input('firstname')], ['firstname' => $validationRules['firstname']], getValidationMessages());
                $lastnameValidator = Validator::make(['lastname' => $request->input('lastname')], ['lastname' => $validationRules['lastname']], getValidationMessages());
            
                if ($firstnameValidator->fails()) {
                    $errors = array_merge($errors, $firstnameValidator->errors()->toArray());
                    //$field .= "fullname";
                }
                if ($lastnameValidator->fails()) {
                    $errors = array_merge($errors, $lastnameValidator->errors()->toArray());
                    //$field .= "lastname";
                }
            }
    
            // Only run validation for the specified field
            if ($field === 'username') {
                $validator = Validator::make(['username' => $data], ['username' => $validationRules['username']], getValidationMessages());
                if ($validator->fails()) {
                    $errors = $validator->errors();
                }
            }

            else if ($field === 'email') {
                $validator = Validator::make(['email' => $data], ['email' => $validationRules['email']], getValidationMessages());
                if ($validator->fails()) {
                    $errors = $validator->errors();
                }
            }
            else if ($field === 'contactnumber') {
                if ($data != '') { // if contact is empty, dont validate since its not required field
                    $validator = Validator::make(['contactnumber' => $data], ['contactnumber' => $validationRules['contactnumber']], getValidationMessages());
                    if ($validator->fails()) {
                        $errors = $validator->errors();
                    }
                }
            }
            else if ($field === 'password') {
                if ($data != '') {
                    $validator = Validator::make([
                        'password' => $data,
                        'password_confirmation' => $request->input('password_confirmation'),
                        ], 
                    ['password' => $validationRules['password'],
                    ], getValidationMessages());
            
                    if ($validator->fails()) {
                        $errors = $validator->errors();
                    }
                }
            }
    
            // Return the field name and error messages in the response
            return response()->json(['field' => $field, 'errors' => $errors]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyProfileSave(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $getUser = Auth::guard('faculty')->user();
            $oldFullname = $getUser->first_name . ($getUser->middle_name ? ' ' . $getUser->middle_name : '') . ' ' . $getUser->last_name;

            $username = $request->input('username');
            $firstname = $request->input('firstname');
            $middlename = $request->input('middlename');
            $lastname = $request->input('lastname');
            $email = $request->input('email');
            $contactnumber = $request->input('contactnumber');
            $gender = $request->input('gender');
            $password = $request->input('password');

            Faculty::where('id', Auth::guard('faculty')->user()->id)
            ->update([
                'username' => $username,
                'first_name' => $firstname,
                'middle_name' => $middlename,
                'last_name' => $lastname,
                'email' => $email,  
                'contact_number' => $contactnumber,
                'gender' => $gender,
            ]);

            // Update the task list where the fullname is matched in a row of assigned_to tasks to sync tasks even changed name
            $newFullName = $firstname . ($middlename ? ' ' . $middlename : '') . ' ' . $lastname;

            AdminTasks::whereRaw("FIND_IN_SET(?, REPLACE(assigned_to, ', ', ','))", [$oldFullname])
                ->update([
                    'assigned_to' => \DB::raw("REPLACE(assigned_to, '$oldFullname', '$newFullName')")
                ]);

            $updateFacultyTask = FacultyTasks::where('submitted_by', $oldFullname)
                ->update(['submitted_by' => $newFullName]);
            

            if ($password) {
                Faculty::where('id', Auth::guard('faculty')->user()->id)
                ->update([
                    'password' => Hash::make($password)
                ]);
            }

            $user = Auth::guard('faculty')->user()->username;

            $data = Faculty::where('username', $user)->first();

            $faculty = Auth::guard('faculty')->user();
            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $faculty->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $facultyUsername . ') ' . $facultyFullName . ' has updated their profile.',
                'type_of_action' => 'Update Profile',
            ]);

            return $data->toJson();
            
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyDashboardMyTasks(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $facultyId = $user->id;

            if (!FacultyTasks::where('submitted_by_id', $facultyId)->first()) {
                return view('faculty.faculty_dashboard_my_tasks', 
                [
                    'status' => null,
                    'data' => json_encode([0]),
                    'assigned' => null,
                    'completed' => null,
                    'late_completed' => null,
                    'ongoing' => null,
                    'missing' => null,
                ]);
            }

            $assigned = FacultyTasks::where('submitted_by_id', $facultyId)->count();
            
            $completed = FacultyTasks::where('submitted_by_id', $facultyId)
                        ->where('status', 'Completed')
                        ->count();

            $late_completed = FacultyTasks::where('submitted_by_id', $facultyId)
                        ->where('status', 'Late Completed')
                        ->count();

            $ongoing = FacultyTasks::where('submitted_by_id', $facultyId)
                        ->where('status', 'Ongoing')
                        ->count();

            $missing = FacultyTasks::where('submitted_by_id', $facultyId)
                        ->where('status', 'Missing')
                        ->count();

            $data = [$completed, $late_completed, $ongoing, $missing];

            return view('faculty.faculty_dashboard_my_tasks', 
                        [
                            'status' => 'datafound',
                            'data' => json_encode($data),
                            'assigned' => $assigned,
                            'completed' => $completed,
                            'late_completed' => $late_completed,
                            'ongoing' => $ongoing,
                            'missing' => $missing,
                        ]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyDashboardAssignedTaskTimeline(Request $request) {
        if (Auth::guard('faculty')->check()) {
            $user = Auth::guard('faculty')->user();
            $facultyId = $user->id;

            if (!FacultyTasks::where('submitted_by_id', $facultyId)->first()) {
                return view('faculty.faculty_dashboard_assigned_task_timeline', 
                        [
                            'status' => null,
                            'assigned' => json_encode([0]),
                            'total' => null,
                        ]);
            }

            $now = Carbon::now();
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $count = FacultyTasks::where('submitted_by_id', $facultyId)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
                array_push($months, $count);
            }

            $total = array_sum($months);

            return view('faculty.faculty_dashboard_assigned_task_timeline', 
                        [  
                            'status' => 'datafound',
                            'assigned' => json_encode($months),
                            'total' => $total,
                        ]);
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyDepartmentLeave(Request $request){
        if (Auth::guard('faculty')->check()) {
            $faculty = Auth::guard('faculty')->user();
            $facultyId = $faculty->id;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            $password = $request->input('password');

            // Check if the password is correct
            if (!Hash::check($password, $faculty->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The password you entered is incorrect. ' 
                ]);            
            }

            // Check if the faculty member have a task assigned, then remove his/her name from the members assigned.
            $facultyTasks = FacultyTasks::where('submitted_by_id', $facultyId)->get();

            if ($facultyTasks) {
                $facultyTasks->each(function ($task) {
                    $task->delete();
                });

                $leaver = trim($facultyFullName); // name to be removed

                $names = AdminTasks::where('assigned_to', 'like', "%{$leaver}%")->first(); // find the row containing the name
                if ($names) {

                    $namesArray = explode(', ', $names->assigned_to); // convert the column value to an array
                    $key = array_search($leaver, $namesArray); // find the key of the name to be removed

                    if ($key !== false) {

                        unset($namesArray[$key]); // remove the name from the array
                        $names->assigned_to = implode(', ', $namesArray); // convert the array back to a string and update the column value
                        $names->save();
                    }
                }

                /*AdminTasks::where('assigned_to', 'like', "%$facultyFullName%")
                    ->update([
                        'assigned_to' => DB::raw("TRIM(BOTH ',' FROM REPLACE(CONCAT(',', assigned_to, ','), ',$facultyFullName,', ','))")
                    ]);
                
                AdminTasks::where('assigned_to', 'like', "%$facultyFullName%")
                ->update([
                    'assigned_to' => DB::raw("TRIM(BOTH ' ' FROM TRIM(BOTH ',' FROM REPLACE(CONCAT(',', assigned_to, ','), ',$facultyFullName,', ',')))")
                ]);*/

            }

            // Delete the row in admin tasks table if assigned to is empty or null.
            AdminTasks::where('assigned_to', '')
                ->orWhereNull('assigned_to')
                ->delete();

            // Decrement the number of members in that department.
            $department = Departments::where('department_name', $faculty->department)->first();

            if ($department) {
                $department->decrement('number_of_members');

                // Remove faculty user from that department.
                $removeFromDepartment = Faculty::where('id', $faculty->id)->first();
                $removeFromDepartment->department = null;
                $removeFromDepartment->save();
            }

            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $faculty->id,
                'user_role' => 'Faculty',
                'action_made' => '(' . $facultyUsername . ') ' . $facultyFullName . ' has left the ' . $department->department_name . ' department.',
                'type_of_action' => 'Leave Department',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => '',
            ]);    
        }
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearches(Request $request){
        if (Auth::guard('faculty')->check()) {
            // TODO: Implement pagination for researches (presented, completed, published) and sort by date created (desc)

            $faculty_id = Auth::guard('faculty')->user()->id;
            
            $researchesPresented = AdminTasksResearchesPresented::with('completedResearch')
            ->where('faculty_id', $faculty_id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

            // include type to researchesPresented
            $researchesPresented->each(function ($item) {
                $item->type = 'Presented';
                $item->title = $item->completedResearch->title; // Access the title
                $item->authors = $item->completedResearch->authors; // Access the authors
            });

            $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')
                ->where('faculty_id', $faculty_id)
                ->get()
                ->each(function ($item) {
                    $item->type = 'Completed';
                });

            $researchesPublished = AdminTasksResearchesPublished::with('completedResearch')
            ->where('faculty_id', $faculty_id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

            // include type to researchesPublished
            $researchesPublished->each(function ($item) {
                $item->type = 'Published';
                $item->title = $item->completedResearch->title; // Access the title
                $item->authors = $item->completedResearch->authors; // Access the authors
            });
            
            $researches = $researchesPresented->concat($researchesCompleted)->concat($researchesPublished);
            
            $researches = $researches->sortByDesc('created_at');
            
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $researches->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($researches), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
            
            return view('faculty.faculty_tasks_researches', ['researches' => $paginator]);
            
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchesView(Request $request){
        if (Auth::guard('faculty')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');
            $faculties = Faculty::all();

            if (!$category || !$id) {
                return back();
            }

            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::with('completedResearch')->find($id);
                $date_presented = Carbon::parse($research->date_presented)->format('Y-m-d');
                $date_completed = Carbon::parse($research->completedResearch->date_completed)->format('Y-m-d');

                if ($research) {
                    return view('faculty.faculty_tasks_researches_presented_view', 
                    [
                        'research' => $research,
                        'date_presented' => $date_presented,
                        'date_completed' => $date_completed,
                        'category' => $category,
                        'id' => $id,
                        'faculties' => $faculties
                    ]);
                } 
                else {
                    return back();
                }
            } 
            else if ($category === 'Completed') {
                $research = AdminTasksResearchesCompleted::find($id);

                if ($research) {
                    // Format the date completed to "yyyy-MM-dd".
                    $dateCompleted = Carbon::parse($research->date_completed)->format('Y-m-d');

                    return view('faculty.faculty_tasks_researches_completed_view', 
                    [
                        'research' => $research,
                        'date_completed' => $dateCompleted,
                        'category' => $category,
                        'id' => $id,
                        'faculties' => $faculties
                    ]);
                } 
                else {
                    return back();
                }
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::with('completedResearch')->find($id);

                if ($research) {
                    // Format the date of Publication to "yyyy-MM-dd".
                    $date_published = Carbon::parse($research->date_published)->format('Y-m-d');
                    $date_completed = Carbon::parse($research->completedResearch->date_completed)->format('Y-m-d');

                    return view('faculty.faculty_tasks_researches_published_view', 
                    [
                        'research' => $research,
                        'date_published' => $date_published,
                        'date_completed' => $date_completed,
                        'category' => $category,
                        'id' => $id,
                        'faculties' => $faculties
                    ]);
                } 
                else {
                    return back();
                }
            } 
            else {
                $research = null;
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchesSearch(Request $request){
        if (Auth::guard('faculty')->check()) {
            $query = $request->input('query');
            $faculty_id = Auth::guard('faculty')->user()->id;

            $researchesCompleted = AdminTasksResearchesCompleted::where('title', 'like', "%{$query}%")
                ->where('faculty_id', $faculty_id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->each(function ($item) {
                    $item->type = 'Completed';
                });

            $researchesPresented = AdminTasksResearchesPresented::with('completedResearch')
                ->whereHas('completedResearch', function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%");
                    $q->where('faculty_id', Auth::guard('faculty')->user()->id);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->each(function ($item) {
                    $item->type = 'Presented';
                    $item->title = $item->completedResearch->title; // Access the title
                    $item->authors = $item->completedResearch->authors; // Access the authors
                });

            $researchesPublished = AdminTasksResearchesPublished::with('completedResearch')
                ->whereHas('completedResearch', function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%");
                    $q->where('faculty_id', Auth::guard('faculty')->user()->id);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->each(function ($item) {
                    $item->type = 'Published';
                    $item->title = $item->completedResearch->title; // Access the title
                    $item->authors = $item->completedResearch->authors; // Access the authors
                });

            $researches = $researchesPresented->concat($researchesCompleted)->concat($researchesPublished);

            $researches = $researches->sortByDesc('created_at')->values();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $researches->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($researches), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedResearches = $paginator->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'authors' => $item->authors,
                    'type' => $item->type,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });
            
            return response()->json(['researches' => $formattedResearches]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchGetAttachments(Request $request){
        if (Auth::guard('faculty')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');
            $folderPath = '';

            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);
                $folderPath = 'Researches/Presented/' . $research->title;
            } 
            else if ($category === 'Completed') {
                $research = AdminTasksResearchesCompleted::find($id);
                $folderPath = 'Researches/Completed/' . $research->title;
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::find($id);
                $folderPath = 'Researches/Published/' . $research->title;
            } 
            else {
                $research = null;
            }

            if ($research) {
                $fileNames = [];

                // Get all the contents in the specified directory
                if ($folderPath !== null) {
                    $files = Storage::disk('google')->listContents($folderPath);
                    
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            // Get the file name
                            $fileName = basename($file['path']);

                            // Check if this is a folder with the name 'Submissions'
                            if ($file['type'] === 'dir' && $fileName === 'Submissions') {
                                // Skip this folder
                                continue;
                            }

                            // Get the mime type
                            $mimeType = $file['mimeType'];

                            // Check if this is a zip file
                            if ($mimeType === 'application/zip') {
                                continue;
                            }

                            // Add the file name to the array
                            $fileNames[] = $fileName;
                        }
                    }
                }

                return response()->json($fileNames);
            } 
            else {
                return response()->json(['error' => 'Research not found.']);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchPreviewFileSelected(Request $request){
        if (Auth::guard('faculty')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');
            $fileName = $request->input('fileName');

            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);
                $folderPath = 'Researches/Presented/' . $research->title;
            } 
            else if ($category === 'Completed') {
                $research = AdminTasksResearchesCompleted::find($id);
                $folderPath = 'Researches/Completed/' . $research->title;
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::find($id);
                $folderPath = 'Researches/Published/' . $research->title;
            } 
            else {
                $research = null;
            }

            // Set up the Google Drive API client
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
    
            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);

            // Find the file on Google Drive
            $findFile = Storage::disk('google')->get($folderPath . '/' . $fileName);

            if (!$findFile) {
                return response()->json(['error' => 'File not found.']);
            }
            
            $results = $service->files->listFiles([
                'q' => "name = '$fileName'",
                'fields' => 'files(id, webViewLink)',
            ]);

            // Check if the file was found
            if (count($results->getFiles()) === 0) {
                return response()->json(['error' => 'File not found.']);
            }
            else {
                // Get the file's metadata
                $file = $results->getFiles()[0];

                // Get the webViewLink property
                $url = $file->getWebViewLink();

                return response()->json(['url' => $url]);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyCreateResearch(Request $request){   
        if (Auth::guard('faculty')->check()) {
            $type = $request->input('type');

            if ($type == 'Presented') {
                $title = $request->input('title');
                $authors = $request->input('authors');
                $host = $request->input('host');
                $level = $request->input('level');
                $files = $request->file('files');

                // Check if the title is unique
                if (AdminTasksResearchesPresented::where('title', $title)->exists()) {
                    return response()->json(['error' => 'A research with this title already exists.']);
                }

                // Check if there are files to upload
                if ($files) {
                    // Create the 'Researches' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches')) {
                        Storage::disk('google')->makeDirectory('Researches');
                        Storage::disk('google')->setVisibility('Researches', 'public');
                    }

                    // Create the 'Presented' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches/Presented')) {
                        Storage::disk('google')->makeDirectory('Researches/Presented');
                        Storage::disk('google')->setVisibility('Researches/Presented', 'public');
                    }

                    // Create the 'Presented' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches/Presented/' . $title)) {
                        Storage::disk('google')->makeDirectory('Researches/Presented/' . $title);
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $title, 'public');
                    }

                    // Store files in the 'Presented' folder
                    foreach ($files as $file) {
                        Storage::disk('google')->putFileAs(
                            'Researches/Presented/' . $title,
                            $file,
                            $file->getClientOriginalName()
                        );
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $title . '/' . $file->getClientOriginalName(), 'public');
                    }
                }
                
                // Create the research object
                $research = new AdminTasksResearchesPresented;
                $research->title = $title;
                $research->authors = $authors;
                $research->host = $host;
                $research->level = $level;
                $research->certificates = $files ? 'Researches/Presented/' . $title : null;
                $research->faculty_id = Auth::guard('faculty')->user()->id;
                $research->save();

                $faculty = Auth::guard('faculty')->user();
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $faculty->id,
                    'user_role' => 'Faculty',
                    'action_made' => '(' . $facultyUsername . ') has created a research titled (' . $title . ').',
                    'type_of_action' => 'Create Research',
                ]);

                // Newly added research object
                $newlyAddedResearch = [
                    'title' => $research->title,
                    'authors' => $research->authors,
                    'date_created_formatted' => Carbon::parse($research->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($research->created_at)->format('g:i A'),
                ];

                // Format all researches
                $researchesPresented = AdminTasksResearchesPresented::orderBy('created_at', 'desc')
                    ->paginate(9);

                $researchesPresented->each(function ($item) {
                    $item->type = 'Presented';
                });

                // Format 
                $formattedResearches = $researchesPresented->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'authors' => $item->authors,
                        'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                        'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                    ];
                });

                return response()->json([
                    'newlyAddedResearch' => $newlyAddedResearch,
                    'allPresentedResearches' => $formattedResearches,
                ]);

            }
            else if ($type == 'Completed') {
                $title = $request->input('title');
                $authors = $request->input('authors');
                $type_funding = $request->input('type_funding');
                $date_completed = $request->input('date_completed');
                $abstract = $request->input('abstract');

                // Check if the title is unique
                if (AdminTasksResearchesCompleted::where('title', $title)->exists()) {
                    return response()->json(['error' => 'A research with this title already exists.']);
                }

                // Create the research object
                $research = new AdminTasksResearchesCompleted;
                $research->title = $title;
                $research->authors = $authors;
                $research->kind_of_research = $type_funding;
                $research->date_completed = $date_completed;
                $research->abstract = $abstract;
                $research->faculty_id = Auth::guard('faculty')->user()->id;
                $research->save();

                $faculty = Auth::guard('faculty')->user();
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $faculty->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $facultyUsername . ') has created a research titled (' . $title . ').',
                    'type_of_action' => 'Create Research',
                ]);

                // Newly added research object
                $newlyAddedResearch = [
                    'title' => $research->title,
                    'authors' => $research->authors,
                    'date_created_formatted' => Carbon::parse($research->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($research->created_at)->format('g:i A'),
                ];

                // Format all researches
                $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')
                    ->paginate(9);

                $researchesCompleted->each(function ($item) {
                    $item->type = 'Completed';
                });

                // Format
                $formattedResearches = $researchesCompleted->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'authors' => $item->authors,
                        'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                        'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                    ];
                });

                return response()->json([
                    'newlyAddedResearch' => $newlyAddedResearch,
                    'allCompletedResearches' => $formattedResearches,
                ]);
            }
            else if ($type == 'Published') {
                $title =  $request->input('title');
                $authors = $request->input('authors');
                $journal = $request->input('journal');
                $date = $request->input('date');
                $link = $request->input('link');

                // Check if the title is unique
                if (AdminTasksResearchesPublished::where('title', $title)->exists()) {
                    return response()->json(['error' => 'A research with this title already exists.']);
                }

                // Create the research object
                $research = new AdminTasksResearchesPublished;
                $research->title = $title;
                $research->authors = $authors;
                $research->name_of_journal = $journal;
                $research->date_published = $date;
                $research->link = $link;
                $research->faculty_id = Auth::guard('faculty')->user()->id;
                $research->save();

                $faculty = Auth::guard('faculty')->user();
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $faculty->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $facultyUsername . ') has created a research titled (' . $title . ').',
                    'type_of_action' => 'Create Research',
                ]);

                // Newly added research object
                $newlyAddedResearch = [
                    'title' => $research->title,
                    'authors' => $research->authors,
                    'date_created_formatted' => Carbon::parse($research->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($research->created_at)->format('g:i A'),
                ];

                // Format all researches
                $researchesPublished = AdminTasksResearchesPublished::orderBy('created_at', 'desc')
                    ->paginate(9);

                $researchesPublished->each(function ($item) {
                    $item->type = 'Published';
                });

                // Format
                $formattedResearches = $researchesPublished->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'authors' => $item->authors,
                        'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                        'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                    ];
                });

                return response()->json([
                    'newlyAddedResearch' => $newlyAddedResearch,
                    'allPublishedResearches' => $formattedResearches,
                ]);
            }
            else {
                return back();
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksResearchUpdate(Request $request){
        if (Auth::guard('faculty')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');

            // Update the research with corresponding category and id
            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);

                $host = $request->input('host');
                $datePresented = $request->input('datePresented');
                $level = $request->input('level');
                $files = $request->file('files');
                $filesCert = $request->file('filesCert');

                $currentPath = $research->special_order;

                $folderPath = $currentPath;
                Storage::disk('google')->setVisibility($folderPath, 'public');
                foreach ($files as $file) {
                    $filePath = $folderPath . '/' . $file->getClientOriginalName();

                    if (!Storage::disk('google')->exists($filePath)) {
                        // The file does not exist yet, upload it
                        $path = Storage::disk('google')->putFileAs($folderPath, $file, $file->getClientOriginalName());
                    
                        // Set the visibility of the file to "public"
                        Storage::disk('google')->setVisibility($path, 'public');
                    }
                }

                // Remove files from the new folder that wasn't found on $files array
                $filesInFolder = Storage::disk('google')->files($folderPath);

                foreach ($filesInFolder as $fileInFolder) {
                    $found = false;
                    foreach ($files as $file) {
                        if ($file->getClientOriginalName() == basename($fileInFolder)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        Storage::disk('google')->delete($fileInFolder);
                    }
                }

                // File upload for certificates
                $currentPathCert = $research->certificates;

                $folderPathCert = $currentPathCert;
                Storage::disk('google')->setVisibility($folderPathCert, 'public');
                foreach ($filesCert as $fileCert) {
                    $filePathCert = $folderPathCert . '/' . $fileCert->getClientOriginalName();

                    if (!Storage::disk('google')->exists($filePathCert)) {
                        // The file does not exist yet, upload it
                        $pathCert = Storage::disk('google')->putFileAs($folderPathCert, $fileCert, $fileCert->getClientOriginalName());
                    
                        // Set the visibility of the file to "public"
                        Storage::disk('google')->setVisibility($pathCert, 'public');
                    }
                }

                // Remove files from the new folder that wasn't found on $files array
                $filesInFolderCert = Storage::disk('google')->files($folderPathCert);

                foreach ($filesInFolderCert as $fileInFolderCert) {
                    $foundCert = false;
                    foreach ($filesCert as $fileCert) {
                        if ($fileCert->getClientOriginalName() == basename($fileInFolderCert)) {
                            $foundCert = true;
                            break;
                        }
                    }
                    if (!$foundCert) {
                        Storage::disk('google')->delete($fileInFolderCert);
                    }
                }

                // Update the research
                $research->host = $host;
                $research->date_presented = $datePresented;
                $research->level = $level;
                $research->special_order = $folderPath;
                $research->certificates = $folderPathCert;
            
                $research->save();

                $faculty = Auth::guard('faculty')->user();
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $faculty->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $facultyUsername . ') has updated a research id (' . $id . ').',
                    'type_of_action' => 'Update Research',
                ]);

                return response()->json(['success' => 'Research updated successfully.']);
            } 
            else if ($category === 'Completed') {
                $research = AdminTasksResearchesCompleted::find($id);

                $title = $request->input('title');
                $authors = $request->input('authors');
                $date = $request->input('date');
                $abstract = $request->input('abstract');

                // Check if the title is unique
                if (AdminTasksResearchesCompleted::where('title', $title)->where('id', '!=', $id)->exists()) {
                    return response()->json(['error' => 'A research with this title already exists.']);
                }

                // Update the research
                $research->title = $title;
                $research->authors = $authors;
                $research->date_completed = $date;
                $research->abstract = $abstract;
                $research->save();

                $faculty = Auth::guard('faculty')->user();
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $faculty->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $facultyUsername . ') has updated a research named (' . $title . ').',
                    'type_of_action' => 'Update Research',
                ]);

                return response()->json(['success' => 'Research updated successfully.']);
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::find($id);

                $journal = $request->input('journal');
                $publishedAt = $request->input('publishedAt');
                $date_published = $request->input('date_published');
                $link = $request->input('link');

                // Update the research
                $research->name_of_journal = $journal;
                $research->published_at = $publishedAt;
                $research->date_published = $date_published;
                $research->link = $link;
                $research->save();

                $faculty = Auth::guard('faculty')->user();
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $faculty->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $facultyUsername . ') has updated a research type (' . $category . ').',
                    'type_of_action' => 'Update Research',
                ]);

                return response()->json(['success' => 'Research updated successfully.']);
            } 
            else {
                $research = null;
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksResearchDelete(Request $request){  
        if (Auth::guard('faculty')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');

            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);

                if ($research) {
                    $folderPath = 'Researches/Presented/' . $research->id;
                    Storage::disk('google')->deleteDirectory($folderPath);
                }
            } 
            else if ($category === 'Completed') {
                $research = AdminTasksResearchesCompleted::with('presentedResearch')->find($id);

                // Delete the folder and its contents of the research presented since presented depends on completed
                if ($research) {
                    $folderPath = 'Researches/Presented/' . $research->presentedResearch->id;
                    Storage::disk('google')->deleteDirectory($folderPath);
                }
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::find($id);
            } 
            else {
                return response()->json(['error' => 'Research not found.']);
            }

            // Delete the research
            $research->delete();

            $faculty = Auth::guard('faculty')->user();
            $facultyUsername = $faculty->username;

            Logs::create([
                'user_id' => $faculty->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $facultyUsername . ') has deleted a research named (' . $title . ').',
                'type_of_action' => 'Delete Research',
             ]);

            return response()->json(['message' => 'Research deleted successfully.']);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchesPresented(Request $request){
        if (Auth::guard('faculty')->check()) {
            $faculty_id = Auth::guard('faculty')->user()->id;
            $researchesPresented = AdminTasksResearchesPresented::with('completedResearch')
            ->where('faculty_id', $faculty_id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

            // include type to researchesPresented
            $researchesPresented->each(function ($item) {
                $item->type = 'Presented';
                $item->title = $item->completedResearch->title; // Access the title
                $item->authors = $item->completedResearch->authors; // Access the authors
            });
            
            $faculties = Faculty::all();
            return view('faculty.faculty_tasks_researches_presented', 
            [
                'researches' => $researchesPresented,
                'faculties' => $faculties
            ]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchesCompleted(Request $request){
        if (Auth::guard('faculty')->check()) {
            $faculty_id = Auth::guard('faculty')->user()->id;

            $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')
            ->where('faculty_id', $faculty_id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

            // include type to researchesCompleted
            $researchesCompleted->each(function ($item) {
                $item->type = 'Completed';
            });

            $faculties = Faculty::all();
            return view('faculty.faculty_tasks_researches_completed', 
            [
                'researches' => $researchesCompleted,
                'faculties' => $faculties
            ]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchesPublished(Request $request){
        if (Auth::guard('faculty')->check()) {
            $faculty_id = Auth::guard('faculty')->user()->id;
            $researchesPublished = AdminTasksResearchesPublished::with('completedResearch')
                ->where('faculty_id', $faculty_id)
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            // include type to researchesPublished
            $researchesPublished->each(function ($item) {
                $item->type = 'Published';
                $item->title = $item->completedResearch->title; // Access the title
                $item->authors = $item->completedResearch->authors; // Access the authors
            });

            $faculties = Faculty::all();
            return view('faculty.faculty_tasks_researches_published', 
            [
                'researches' => $researchesPublished,
                'faculties' => $faculties
            ]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchesCategorySearch(Request $request){
        if (Auth::guard('faculty')->check()) {
            $query = $request->input('query');
            $category = $request->input('category');

            if ($category === 'Presented') {
                $researchesPresented = AdminTasksResearchesPresented::with('completedResearch')
                    ->orderBy('created_at', 'desc')
                    ->whereHas('completedResearch', function ($q) use ($query) {
                        $q->where('title', 'like', '%' . $query . '%');
                        $q->where('faculty_id', Auth::guard('faculty')->user()->id);
                    })
                    ->get()
                    ->each(function ($item) {
                        $item->type = 'Presented';
                        $item->title = $item->completedResearch->title; // Access the title
                        $item->authors = $item->completedResearch->authors; // Access the authors
                    });
        
                $researches = $researchesPresented;
            } else if ($category === 'Completed') {
                $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')
                    ->where('title', 'like', "%{$query}%")
                    ->where('faculty_id', Auth::guard('faculty')->user()->id)
                    ->get()
                    ->each(function ($item) {
                        $item->type = 'Completed';
                    });

                $researches = $researchesCompleted;
            } else if ($category === 'Published') {
                $researchesPublished = AdminTasksResearchesPublished::with('completedResearch')
                    ->orderBy('created_at', 'desc')
                    ->whereHas('completedResearch', function ($q) use ($query) {
                        $q->where('title', 'like', '%' . $query . '%');
                        $q->where('faculty_id', Auth::guard('faculty')->user()->id);
                    })
                    ->get()
                    ->each(function ($item) {
                        $item->type = 'Published';
                        $item->title = $item->completedResearch->title; // Access the title
                        $item->authors = $item->completedResearch->authors; // Access the authors
                    });

                $researches = $researchesPublished;
            } else {
                $researches = [];
            }

            $researches = $researches->sortByDesc('created_at')->values();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $researches->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($researches), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            if ($category === 'Presented') {
                $formattedResearches = $paginator->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'authors' => $item->authors,
                        'host' => $item->host,
                        'level' => $item->level,
                        'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                        'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                    ];
                });
               
                return response()->json(['researches' => $formattedResearches]);
            }
            else if ($category === 'Published') {
                $formattedResearches = $paginator->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'authors' => $item->authors,
                        'name_of_journal' => $item->name_of_journal,
                        'published_at' => $item->published_at,
                        'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                        'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                    ];
                });
               
                return response()->json(['researches' => $formattedResearches]);
            }

            $formattedResearches = $paginator->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'authors' => $item->authors,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });
           
            return response()->json(['researches' => $formattedResearches]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        }
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }
    
    function facultyTasksResearchIsMarkedAsPresented(Request $request) {
        $id = $request->input('id');
        $faculty_id = Auth::guard('faculty')->user()->id;

        // Check if marked as presented already
        $presentedResearchExists = AdminTasksResearchesPresented::where('research_completed_id', $id)
            ->where('faculty_id', $faculty_id)
            ->first();

        if ($presentedResearchExists) {
            return response()->json(['response' => true]);
        }

        return response()->json(['response' => false]);
    }

    function facultyTasksResearchMarkAsPresented(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $completed_research_id = $request->input('completed_research_id');
            $host = $request->input('host');
            $date_presented = $request->input('date_presented');
            $level = $request->input('level');
            $special_order_files = $request->file('special_order_files');
            $certifications_files = $request->file('certifications_files');

            // Check if marked as presented already
            $presentedResearchExists = AdminTasksResearchesPresented::where('research_completed_id', $completed_research_id)->first();
            if ($presentedResearchExists) {
                return response()->json(['error' => 'This research has already been marked as presented.'], 422);
            }

            $completedResearch = AdminTasksResearchesCompleted::find($completed_research_id);
            if ($completedResearch) {
                // create new presented research
                $presentedResearch = new AdminTasksResearchesPresented;

                $presentedResearch->host = $host;
                $presentedResearch->date_presented = $date_presented;
                $presentedResearch->level = $level;
                $presentedResearch->research_completed_id = $completedResearch->id;
                $presentedResearch->special_order = ''; // Initialize special order
                $presentedResearch->certificates = ''; // Initialize certificates
                $presentedResearch->faculty_id = Auth::guard('faculty')->user()->id;
                $presentedResearch->save();

                if ($special_order_files) {
                    // Create the 'Researches' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches')) {
                        Storage::disk('google')->makeDirectory('Researches');
                        Storage::disk('google')->setVisibility('Researches', 'public');
                    }

                    // Create the 'Presented' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches/Presented')) {
                        Storage::disk('google')->makeDirectory('Researches/Presented');
                        Storage::disk('google')->setVisibility('Researches/Presented', 'public');
                    }

                    // Create the 'Presented' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches/Presented/' . $presentedResearch->id)) {
                        Storage::disk('google')->makeDirectory('Researches/Presented/' . $presentedResearch->id);
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $presentedResearch->id, 'public');
                    }

                    // Create the 'Special Order' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches/Presented/' . $presentedResearch->id . '/Special Order')) {
                        Storage::disk('google')->makeDirectory('Researches/Presented/' . $presentedResearch->id . '/Special Order');
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $presentedResearch->id . '/Special Order', 'public');
                    }

                    // Store files in the 'Special Order' folder
                    foreach ($special_order_files as $file) {
                        Storage::disk('google')->putFileAs(
                            'Researches/Presented/' . $presentedResearch->id . '/Special Order',
                            $file,
                            $file->getClientOriginalName()
                        );
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $presentedResearch->id . '/Special Order/' . $file->getClientOriginalName(), 'public');
                    }
                    
                    $presentedResearch->special_order = 'Researches/Presented/' . $presentedResearch->id . '/Special Order';
                }
                
                if ($certifications_files) {
                    // Create the 'Certifications' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Researches/Presented/' . $presentedResearch->id . '/Certifications')) {
                        Storage::disk('google')->makeDirectory('Researches/Presented/' . $presentedResearch->id . '/Certifications');
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $presentedResearch->id . '/Certifications', 'public');
                    }

                    // Store files in the 'Certifications' folder
                    foreach ($certifications_files as $file) {
                        Storage::disk('google')->putFileAs(
                            'Researches/Presented/' . $presentedResearch->id . '/Certifications',
                            $file,
                            $file->getClientOriginalName()
                        );
                        Storage::disk('google')->setVisibility('Researches/Presented/' . $presentedResearch->id . '/Certifications/' . $file->getClientOriginalName(), 'public');
                    }

                    $presentedResearch->certificates = 'Researches/Presented/' . $presentedResearch->id . '/Certifications';
                }

                // save presented research
                $presentedResearch->save();

                return response()->json(['success' => 'Research marked as presented successfully.']);
            }
            else {
                return response()->json(['error' => 'Research not found.']);
            }
            
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksResearchIsMarkedAsPublished(Request $request) 
    {
        $id = $request->input('id');
        $faculty_id = Auth::guard('faculty')->user()->id;

        // Check if marked as published already
        $publishedResearchExists = AdminTasksResearchesPublished::where('research_completed_id', $id)
            ->where('faculty_id', $faculty_id)
            ->first();

        if ($publishedResearchExists) {
            return response()->json(['response' => true]);
        }

        return response()->json(['response' => false]);
    }

    function facultyTasksResearchMarkAsPublished(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $completed_research_id = $request->input('completed_research_id');
            $journal = $request->input('journal');
            $published_at = $request->input('published_at');
            $date_published = $request->input('date_published');
            $link = $request->input('link');

            // Check if marked as published already
            $publishedResearchExists = AdminTasksResearchesPublished::where('research_completed_id', $completed_research_id)->first();
            if ($publishedResearchExists) {
                return response()->json(['error' => 'This research has already been marked as published.'], 422);
            }

            $completedResearch = AdminTasksResearchesCompleted::find($completed_research_id);
            if ($completedResearch) {
                // create new published research
                $publishedResearch = new AdminTasksResearchesPublished;

                $publishedResearch->name_of_journal = $journal;
                $publishedResearch->published_at = $published_at;
                $publishedResearch->date_published = $date_published;
                $publishedResearch->link = $link;
                $publishedResearch->research_completed_id = $completedResearch->id;
                $publishedResearch->faculty_id = Auth::guard('faculty')->user()->id;

                // save published research
                $publishedResearch->save();

                return response()->json(['success' => 'Research marked as published successfully.']);
            }
            else {
                return response()->json(['error' => 'Research not found.']);
            }
            
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchPresentedSpecialOrderAttachments(Request $request){
        if (Auth::guard('faculty')->check()) {
            $id = $request->input('id');

            $research = AdminTasksResearchesPresented::find($id);

            if ($research) {
                $folderPath = $research->special_order;
                $fileNames = [];

                // Get all the contents in the specified directory
                if ($folderPath !== null) {
                    $files = Storage::disk('google')->listContents($folderPath);
                    
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            // Get the file name
                            $fileName = basename($file['path']);

                            // Check if this is a folder with the name 'Submissions'
                            if ($file['type'] === 'dir' && $fileName === 'Submissions') {
                                // Skip this folder
                                continue;
                            }

                            // Get the mime type
                            $mimeType = $file['mimeType'];

                            // Check if this is a zip file
                            if ($mimeType === 'application/zip') {
                                continue;
                            }

                            // Add the file name to the array
                            $fileNames[] = $fileName;
                        }
                    }
                }

                return response()->json($fileNames);
            }
            else {
                return response()->json(['error' => 'Research not found.']);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksResearchPresentedCertificatesAttachments(Request $request){
        if (Auth::guard('faculty')->check()) {
            $id = $request->input('id');

            $research = AdminTasksResearchesPresented::find($id);

            if ($research) {
                $folderPath = $research->certificates;
                $fileNames = [];

                // Get all the contents in the specified directory
                if ($folderPath !== null) {
                    $files = Storage::disk('google')->listContents($folderPath);
                    
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            // Get the file name
                            $fileName = basename($file['path']);

                            // Check if this is a folder with the name 'Submissions'
                            if ($file['type'] === 'dir' && $fileName === 'Submissions') {
                                // Skip this folder
                                continue;
                            }

                            // Get the mime type
                            $mimeType = $file['mimeType'];

                            // Check if this is a zip file
                            if ($mimeType === 'application/zip') {
                                continue;
                            }

                            // Add the file name to the array
                            $fileNames[] = $fileName;
                        }
                    }
                }

                return response()->json($fileNames);
            }
            else {
                return response()->json(['error' => 'Research not found.']);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksAttendance(){
        if (Auth::guard('faculty')->check()) {
            // Return only the attendance that associated with this faculty
            $faculty = Auth::guard('faculty')->user();
            $attendances = Attendance::with('getFunction')
                ->where('faculty_id', $faculty->id)
                ->orderBy('created_at', 'desc')
                ->paginate(9);

            $attendances->each(function ($item) {
                $item->brief_description = $item->getFunction->brief_description;
                $item->remarks = $item->getFunction->remarks;
            });

            return view('faculty.faculty_tasks_attendance', ['items' => $attendances]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksAttendanceView(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $id = $request->input('id');

            $attendance = Attendance::with('getFunction')
                ->where('id', $id)
                ->first();

            if ($attendance) {
               return view('faculty.faculty_tasks_attendance_view', 
               ['item' => $attendance]);
            }
            else {
                return back();
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksAttendanceGetAttachments(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $id = $request->input('id');

            $attendance = Attendance::find($id);

            if ($attendance) {
                $folderPath = $attendance->proof_of_attendance;
                $fileNames = [];

                // Get all the contents in the specified directory
                if ($folderPath !== null) {
                    $files = Storage::disk('google')->listContents($folderPath);
                    
                    if (!empty($files)) {
                        foreach ($files as $file) {
                            // Get the file name
                            $fileName = basename($file['path']);

                            // Check if this is a folder with the name 'Submissions'
                            if ($file['type'] === 'dir' && $fileName === 'Submissions') {
                                // Skip this folder
                                continue;
                            }

                            // Get the mime type
                            $mimeType = $file['mimeType'];

                            // Check if this is a zip file
                            if ($mimeType === 'application/zip') {
                                continue;
                            }

                            // Add the file name to the array
                            $fileNames[] = $fileName;
                        }
                    }
                }

                return response()->json($fileNames);
            }
            else {
                return response()->json(['error' => 'Attendance not found.']);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksAttendanceSearch(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $query = $request->input('query');

            // Return only the attendance that associated with this faculty
            $faculty = Auth::guard('faculty')->user();
            $attendances = Attendance::with('getFunction')
                ->where('faculty_id', $faculty->id)
                ->whereHas('getFunction', function ($q) use ($query) {
                    $q->where('brief_description', 'like', '%' . $query . '%');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $attendances->each(function ($item) {
                $item->brief_description = $item->getFunction->brief_description;
                $item->remarks = $item->getFunction->remarks;
            });

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $attendances->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($attendances), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedAttendances = $paginator->map(function ($item) {
                return [
                    'function_id' => $item->function_id,
                    'brief_description' => $item->brief_description,
                    'remarks' => $item->remarks,
                    'status' => $item->status,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['items' => $formattedAttendances]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksAttendanceCreate(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $function_id = $request->input('function_id');
            $faculty_id = Auth::guard('faculty')->user()->id;
            $date_started = $request->input('date_started');
            $date_completed = $request->input('date_completed');
            $status_attendance = $request->input('status_attendance');
            $reason_absence = $request->input('reason_absence');
            $files = $request->file('files');

            // Check if attendance is added already to the function with the same faculty
            $attendanceExists = Attendance::where('function_id', $function_id)
                ->where('faculty_id', $faculty_id)
                ->first();

            if ($attendanceExists) {
                return response()->json(['error' => 'You have already added attendance to this function.']);
            }

            // Create the Attendance first
            $attendance = new Attendance;
            $attendance->date_started = $date_started;
            $attendance->date_completed = $date_completed;
            $attendance->status_of_attendace = $status_attendance;
            $attendance->reason_for_absence = '';
            $attendance->proof_of_attendance = '';
            $attendance->status = 'Pending';
            $attendance->faculty_id = $faculty_id;
            $attendance->function_id = $function_id;
            $attendance->save();

            if ($status_attendance === 'On Leave') {
                $attendance->reason_for_absence = $reason_absence;
                $attendance->save();
            }
            else {
                if ($files) {
                    // Create the 'Attendance' folder if it doesn't exist
                    if (!Storage::disk('google')->exists('Attendance')) {
                        Storage::disk('google')->makeDirectory('Attendance');
                        Storage::disk('google')->setVisibility('Attendance', 'public');
                    }

                    if (!Storage::disk('google')->exists('Attendance/' . $attendance->id)) {
                        Storage::disk('google')->makeDirectory('Attendance/' . $attendance->id);
                        Storage::disk('google')->setVisibility('Attendance/' . $attendance->id, 'public');
                    }

                    // Store files in the 'Attendance' folder
                    foreach ($files as $file) {
                        Storage::disk('google')->putFileAs(
                            'Attendance/' . $attendance->id,
                            $file,
                            $file->getClientOriginalName()
                        );
                        Storage::disk('google')->setVisibility('Attendance/' . $attendance->id . '/' . $file->getClientOriginalName(), 'public');
                    }

                    $attendance->proof_of_attendance = 'Attendance/' . $attendance->id;
                    $attendance->save();
                }
            }

            return response()->json(['success' => 'Attendance added successfully.']);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksAttendanceUpdate(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $id = $request->input('id');
            $date_started = $request->input('date_started');
            $date_completed = $request->input('date_completed');
            $status_attendance = $request->input('status_attendance');
            $reason_absence = $request->input('reason_absence');
            $files = $request->file('files');

            $attendance = Attendance::find($id);

            if ($attendance) {
                $attendance->date_started = $date_started;
                $attendance->date_completed = $date_completed;
                $attendance->status_of_attendace = $status_attendance;
                $attendance->save();

                if ($status_attendance === 'On Leave') {
                    // Check if previous was not 'On Leave' and there are files

                    if ($attendance->proof_of_attendance !== null) {
                        // Delete the folder and its contents
                        $folderPath = $attendance->proof_of_attendance;
                        Storage::disk('google')->deleteDirectory($folderPath);
                    }

                    $attendance->reason_for_absence = $reason_absence;
                    $attendance->proof_of_attendance = '';
                    $attendance->save();
                }
                else {
                    // Check if previous was 'On Leave' then delete the reason for absence and create the folder then store the files
                    if ($attendance->reason_for_absence !== '') {

                        // Check if there are files
                        if ($files) {
                            // Create the 'Attendance' folder if it doesn't exist
                            if (!Storage::disk('google')->exists('Attendance')) {
                                Storage::disk('google')->makeDirectory('Attendance');
                                Storage::disk('google')->setVisibility('Attendance', 'public');
                            }

                            if (!Storage::disk('google')->exists('Attendance/' . $attendance->id)) {
                                Storage::disk('google')->makeDirectory('Attendance/' . $attendance->id);
                                Storage::disk('google')->setVisibility('Attendance/' . $attendance->id, 'public');
                            }

                            // Store files in the 'Attendance' folder
                            foreach ($files as $file) {
                                Storage::disk('google')->putFileAs(
                                    'Attendance/' . $attendance->id,
                                    $file,
                                    $file->getClientOriginalName()
                                );
                                Storage::disk('google')->setVisibility('Attendance/' . $attendance->id . '/' . $file->getClientOriginalName(), 'public');
                            }

                            $attendance->reason_for_absence = '';
                            $attendance->proof_of_attendance = 'Attendance/' . $attendance->id;
                            $attendance->save();
                        }
                    }
                    else { // Meaning the previous status was not 'On Leave'

                        // Update the folder and its contents
                        $folderPath = $attendance->proof_of_attendance;
                        Storage::disk('google')->setVisibility($folderPath, 'public');
                        foreach ($files as $file) {
                            $filePath = $folderPath . '/' . $file->getClientOriginalName();

                            if (!Storage::disk('google')->exists($filePath)) {
                                // The file does not exist yet, upload it
                                $path = Storage::disk('google')->putFileAs($folderPath, $file, $file->getClientOriginalName());
                            
                                // Set the visibility of the file to "public"
                                Storage::disk('google')->setVisibility($path, 'public');
                            }
                        }

                        // Remove files from the new folder that wasn't found on $files array
                        $filesInFolder = Storage::disk('google')->files($folderPath);

                        foreach ($filesInFolder as $fileInFolder) {
                            $found = false;
                            foreach ($files as $file) {
                                if ($file->getClientOriginalName() == basename($fileInFolder)) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                Storage::disk('google')->delete($fileInFolder);
                            }
                        }

                        $attendance->reason_for_absence = '';
                        $attendance->proof_of_attendance = 'Attendance/' . $attendance->id;
                        $attendance->save();
                    }
                }

                return response()->json(['success' => 'Attendance updated successfully.']);
            }
            else {
                return response()->json(['error' => 'Attendance not found.']);
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksAttendanceIsAdded(Request $request)
    {
        $id = $request->input('id');
        $faculty = Auth::guard('faculty')->user();

        // Check if attendance is added already to the function
        $attendanceExists = Attendance::where('function_id', $id)->where('faculty_id', $faculty->id)->first();
        if ($attendanceExists) {
            return response()->json(['exists' => true]);
        }

        return response()->json(['exists' => false]);
    }

    function showFacultyTasksFunctions(){
        if (Auth::guard('faculty')->check()) {
            $functions = Functions::orderBy('created_at', 'desc')
                ->paginate(9);

            return view('faculty.faculty_tasks_functions', ['items' => $functions]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksFunctionsSearch(Request $request)
    {
        if (Auth::guard('faculty')->check()) {
            $query = $request->input('query');

            $functions = Functions::where('brief_description', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $functions->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($functions), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedFunctions = $paginator->map(function ($item) {
                return [
                    'brief_description' => $item->brief_description,
                    'remarks' => $item->remarks,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['items' => $formattedFunctions]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksSeminars(){
        if (Auth::guard('faculty')->check()) {
            $faculty_id = Auth::guard('faculty')->user()->id; 
            $seminars = Seminars::orderBy('created_at', 'desc')
                ->where('faculty_id', $faculty_id)
                ->paginate(9);

            return view('faculty.faculty_tasks_seminars', ['items' => $seminars]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksSeminarsView(Request $request){
        if (Auth::guard('faculty')->check()) {
            $id = $request->input('id');

            $seminar = Seminars::find($id);

            if ($seminar) {
                return view('faculty.faculty_tasks_seminars_view', 
                ['item' => $seminar]);
            }
            else {
                return back();
            }
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function showFacultyTasksSeminarsSearch(Request $request){
        if (Auth::guard('faculty')->check()) {
            $query = $request->input('query');

            $faculty_id = Auth::guard('faculty')->user()->id;
            $seminars = Seminars::where('title', 'like', "%{$query}%")
                ->where('faculty_id', $faculty_id)
                ->orderBy('created_at', 'desc')
                ->get();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $seminars->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($seminars), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedSeminars = $paginator->map(function ($item) {
                return [
                    'title' => $item->title,
                    'from_date' => Carbon::parse($item->from_date)->format('F j, Y'),
                    'to_date' => Carbon::parse($item->to_date)->format('F j, Y'),
                    'total_no_hours' => $item->total_no_hours,
                    'notes' => $item->notes,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['items' => $formattedSeminars]);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }

    }

    function facultyTasksSeminarsCreate(Request $request){
        if (Auth::guard('faculty')->check()) {
            $faculty_id = Auth::guard('faculty')->user()->id;
            $title = $request->input('title');
            $classification = $request->input('classification');
            $nature = $request->input('nature');
            $type = $request->input('type');
            $source_of_fund = $request->input('source_of_fund');
            $budget = $request->input('budget');
            $organizer = $request->input('organizer');
            $level = $request->input('level');
            $venue = $request->input('venue');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
            $total_no_hours = $request->input('total_no_hours');
            $special_order_files = $request->file('special_order_files');
            $certifications_files = $request->file('certifications_files');
            $compiled_files = $request->file('compiled_files');
            $notes = $request->input('notes');

            // Create the Seminar first
            $seminar = new Seminars;
            $seminar->title = $title;
            $seminar->classification = $classification;
            $seminar->nature = $nature;
            $seminar->type = $type;
            $seminar->source_of_fund = $source_of_fund;
            $seminar->budget = $budget;
            $seminar->organizer = $organizer;
            $seminar->level = $level;
            $seminar->venue = $venue;
            $seminar->from_date = $from_date;
            $seminar->to_date = $to_date;
            $seminar->total_no_hours = $total_no_hours;
            $seminar->special_order = ''; // Initialize special order
            $seminar->certificate = ''; // Initialize certificates
            $seminar->compiled_photos = ''; // Initialize compiled files
            $seminar->faculty_id = $faculty_id;
            $seminar->notes = $notes;
            $seminar->save();

            if ($special_order_files) {
                // Create the 'Seminars' folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars')) {
                    Storage::disk('google')->makeDirectory('Seminars');
                    Storage::disk('google')->setVisibility('Seminars', 'public');
                }

                // Create the 'Special Order' folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars/' . $seminar->id)) {
                    Storage::disk('google')->makeDirectory('Seminars/' . $seminar->id);
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id, 'public');
                }

                // Create the 'Special Order' folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars/' . $seminar->id . '/Special Order')) {
                    Storage::disk('google')->makeDirectory('Seminars/' . $seminar->id . '/Special Order');
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id . '/Special Order', 'public');
                }

                // Store files in the 'Special Order' folder
                foreach ($special_order_files as $file) {
                    Storage::disk('google')->putFileAs(
                        'Seminars/' . $seminar->id . '/Special Order',
                        $file,
                        $file->getClientOriginalName()
                    );
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id . '/Special Order/' . $file->getClientOriginalName(), 'public');
                }

                $seminar->special_order = 'Seminars/' . $seminar->id . '/Special Order';
            }

            if ($certifications_files) {
                // Create the 'Certifications' folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars/' . $seminar->id . '/Certifications')) {
                    Storage::disk('google')->makeDirectory('Seminars/' . $seminar->id . '/Certifications');
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id . '/Certifications', 'public');
                }

                // Store files in the 'Certifications' folder
                foreach ($certifications_files as $file) {
                    Storage::disk('google')->putFileAs(
                        'Seminars/' . $seminar->id . '/Certifications',
                        $file,
                        $file->getClientOriginalName()
                    );
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id . '/Certifications/' . $file->getClientOriginalName(), 'public');
                }

                $seminar->certificate = 'Seminars/' . $seminar->id . '/Certifications';
            }

            if ($compiled_files) {
                // Create the 'Compiled Photos' folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars/' . $seminar->id . '/Compiled Photos')) {
                    Storage::disk('google')->makeDirectory('Seminars/' . $seminar->id . '/Compiled Photos');
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id . '/Compiled Photos', 'public');
                }

                // Store files in the 'Compiled Photos' folder
                foreach ($compiled_files as $file) {
                    Storage::disk('google')->putFileAs(
                        'Seminars/' . $seminar->id . '/Compiled Photos',
                        $file,
                        $file->getClientOriginalName()
                    );
                    Storage::disk('google')->setVisibility('Seminars/' . $seminar->id . '/Compiled Photos/' . $file->getClientOriginalName(), 'public');
                }

                $seminar->compiled_photos = 'Seminars/' . $seminar->id . '/Compiled Photos';
            }

            // save seminar
            $seminar->save();

            return response()->json(['success' => 'Seminar added successfully.']);
        } 
        else if (Auth::guard('admin')->check()) {
            return redirect('admin-home');
        } 
        else {
            return redirect('login-faculty')->with('fail', 'You must be logged in');
        }
    }

    function facultyTasksSeminarsUpdate(Request $request){

    }

    function facultyTasksSeminarsDelete(Request $request){

    }

    function showFacultyTasksSeminarsGetAttachments(Request $request){

    }

    function showFacultyTasksSeminarsPreviewFileSelected(Request $request){

    }
}
