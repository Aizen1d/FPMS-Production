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
}
