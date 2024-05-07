<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Models\AdminTasks;
use App\Models\AdminTasksResearchesPresented;
use App\Models\AdminTasksResearchesCompleted;
use App\Models\AdminTasksResearchesPublished;
use App\Models\Extension;
use App\Models\Attendance;
use App\Models\Seminars;
use App\Models\Faculty;
use App\Models\FacultyPendingAccounts;
use App\Models\DepartmentPendingJoins;
use App\Models\Departments;
use App\Models\FacultyTasks;
use App\Models\Logs;
use App\Providers\GoogleDriveServiceProvider;
use League\Flysystem\Filesystem;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Support\Facades\Config;
use Google_Service_Drive_DriveFile;
use Exception;
use ZipArchive;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Mail;

// Import LengthAwarePaginator
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    function makeAndCreateFoldersPublic($selectFaculty, $taskName) {
        // Check if the 'Created Tasks' folder exists
        if (!Storage::disk('google')->exists('Created Tasks')) {
            // Create the 'Created Tasks' folder
            Storage::disk('google')->makeDirectory('Created Tasks');
            // Make the 'Created Tasks' folder publicly accessible
            Storage::disk('google')->setVisibility('Created Tasks', 'public');
        }
    
        // Check if the 'Departments' folder exists inside the 'Created Tasks' folder
        if (!Storage::disk('google')->exists('Created Tasks/Departments')) {
            Storage::disk('google')->makeDirectory('Created Tasks/Departments');
            Storage::disk('google')->setVisibility('Created Tasks/Departments', 'public');
        }
    
        // Check if the '$selectFaculty' folder exists inside the 'Created Tasks/Departments' folder
        if (!Storage::disk('google')->exists('Created Tasks/Departments/' . $selectFaculty)) {
            Storage::disk('google')->makeDirectory('Created Tasks/Departments/' . $selectFaculty);
            Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty, 'public');
        }
    
        // Check if the 'Tasks' folder exists inside the 'Created Tasks/Departments/$selectFaculty' folder
        if (!Storage::disk('google')->exists('Created Tasks/Departments/' . $selectFaculty . '/Tasks')) {
            Storage::disk('google')->makeDirectory('Created Tasks/Departments/' . $selectFaculty . '/Tasks');
            Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks', 'public');
        }
    
        // Check if the '$taskName' folder exists inside the 'Created Tasks/Departments/$selectFaculty/Tasks' folder
        if (!Storage::disk('google')->exists('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName)) {
            Storage::disk('google')->makeDirectory('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName);
            Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName, 'public');
        }
    }

    function showAdminHome(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // When viewing a department and goes home, remove session.
            if ($request->session()->get('department')) {
                $request->session()->put('department', '');

                return redirect('admin-home');
            }

            $departments = Departments::paginate(6); // Retrieve (n) departments per page
            return view('admin.admin_home', ['departments' => $departments]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function setDepartmentToShow(Request $request)
    {
        if ($request->input('department')) {
            $department = $request->input('department');
            $request->session()->put('department', $department);

            return redirect('admin-home/show-department/assigned-tasks');
        } else {
            return back();
        }
    }

    function removeDepartmentSessionOnReturn(Request $request)
    {
        if ($request->session()->get('department')) {
            $request->session()->put('department', '');

            return redirect('admin-home');
        } else {
            return back();
        }
    }

    function showAdminDepartmentBulletin(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $department = $request->session()->get('department');

            if ($department) {
                return view('admin.admin_show_department_bulletin', ['department' => $department]);
            } else {
                return back();
            }
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDepartmentMembers(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $department = $request->session()->get('department');

            if ($department) {
                $members = Faculty::where('department', $department)
                    ->orderBy('first_name', 'asc')
                    ->paginate(9);
                $numberOfMembers = Departments::where('department_name', $department)->first();

                return view('admin.admin_show_department_members', [
                    'items' => $members,
                    'departmentName' => $department,
                    'numberOfMembers' => $numberOfMembers
                ]);
            } else {
                return back();
            }
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDepartmentMembersSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $department = $request->session()->get('department');

            if ($department) { // department exists in the departments table
                $query = $request->input('query');

                if (empty($query)) {
                    $items = Faculty::where('department', $department)
                        ->orderBy('first_name', 'asc')
                        ->paginate(9);
                } else {
                    $queryParts = explode(' ', $query);
                    $items = Faculty::where('department', $department)
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

                $formattedTasks = $items->map(function ($item) {
                    return [
                        'fullname' => $item->first_name . ' ' . $item->middle_name . ' ' . $item->last_name,
                        'join_date_formatted' => Carbon::parse($item->department_join_date)->format('F j, Y'),
                        'join_date_time' => Carbon::parse($item->department_join_date)->format('g:i A'),
                    ];
                });

                return response()->json($formattedTasks);
            }
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDepartmentAssignedTasks(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $department = $request->session()->get('department');

            $tasks = AdminTasks::where('faculty_name', $department)
                ->orderBy('created_at', 'desc')
                ->paginate(9);
            
            $members = Faculty::where('department', $department)
                ->orderBy('first_name', 'asc')
                ->get();

            $memberCount = $members->count();
           
            return view('admin.admin_show_department_assigned_tasks', ['tasks' => $tasks], ['members' => $members, 'department' => $department, 'memberCount' => $memberCount]);

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminDepartmentAssignedTasksSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');
            $department = $request->session()->get('department');

            if (empty($query)) {
                $tasks = AdminTasks::where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

            } else {
                $tasks = AdminTasks::where('task_name', 'like', "%{$query}%")
                    ->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
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

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminDepartmentAssignedTasksCreateTask(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $taskName = $request->input('taskname');
            $assignedto = json_decode($request->input('assignto'));
            $selectFaculty = $request->input('selectFaculty');
            $dueDate = $request->input('dateTimePicker');
            $description = $request->input('description');
            $files = $request->file('files');

            // check for uniqueness of task_name
            if (AdminTasks::where('task_name', $taskName)->exists()) {
                // task_name is not unique, return an error
                return response()->json([
                    'error' => 'A task with this name in this or other department already exists. '
                ]);
            }

            if ($files) { // Check if there is file to be uploaded, if so then check if folders are existing
                $this->makeAndCreateFoldersPublic($selectFaculty, $taskName);
            }

            if ($files) { // Check if there are files to upload to gdrive
                // Get path to '$taskName' folder
                $adapter = Storage::disk('google')->getAdapter();
                $metadata = $adapter->getMetadata('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName);
                $id = $metadata['path'];

                // Store files in '$taskName' folder
                foreach ($files as $file) {
                    Storage::disk('google')->putFileAs(
                        'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName,
                        $file,
                        $file->getClientOriginalName()
                    );
                    Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName . '/' . $file->getClientOriginalName(), 'public');
                }
            } else {
                $id = null;
            }

            $adminTask = new AdminTasks;
            $adminTask->task_name = $taskName;
            $adminTask->faculty_image_link = 'admin/images/home.svg';
            $adminTask->faculty_name = $selectFaculty;
            $adminTask->assigned_to = implode(', ', $assignedto);
            $adminTask->description = $description;
            $adminTask->attachments = $id;
            $adminTask->date_created = Carbon::now();
            $adminTask->due_date = $dueDate;
            $adminTask->save();

            // Populate faculty tasks table with each assignee per row
            foreach ($assignedto as $name) {
                $facultyTask = new FacultyTasks;
                $facultyTask->task_id = $adminTask->id;

                // Check if the full name matches a row in the Faculty table
                $faculty = Faculty::whereRaw("REPLACE(TRIM(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)), '  ', ' ') = ?", [$name])->first();

                // If a matching row is found, set the submitted_by_id property to the ID of the row
                if ($faculty) {
                    $facultyTask->submitted_by_id = $faculty->id;
                }

                $facultyTask->submitted_by = $name;
                $facultyTask->status = 'Ongoing';
                $facultyTask->decision = 'Not decided';
                
                $facultyTask->save();
            }

            // Create the newly added task object
            $newlyAddedTask = [
                'task_name' => $adminTask->task_name,
                'faculty_image' => $adminTask->faculty_image_link,
                'faculty_name' => $adminTask->faculty_name,
                'date_created_formatted' => Carbon::parse($adminTask->created_at)->format('F j, Y'),
                'date_created_time' => Carbon::parse($adminTask->created_at)->format('g:i A'),
                'due_date_formatted' => Carbon::parse($adminTask->due_date)->format('F j, Y'),
                'due_date_time' => Carbon::parse($adminTask->due_date)->format('g:i A'),
                'due_date_past' => Carbon::parse($adminTask->due_date)->isPast(),
            ];

            // Get all tasks
            $tasks = AdminTasks::where('faculty_name', $adminTask->faculty_name)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

            // Format all tasks
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

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has created a task named (' . $taskName . ').',
                'type_of_action' => 'Create Task',
            ]);

            // Return the newly added task object and the array of all tasks as separate variables
            return response()->json([
                'newlyAddedTask' => $newlyAddedTask,
                'allTasks' => $formattedTasks,
            ]);

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDepartmentRequests()
    {
        if (Auth::guard('admin')->check()) {
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasks()
    {
        if (Auth::guard('admin')->check()) {
            $tasks = AdminTasks::orderBy('created_at', 'desc')->paginate(9);
            $departments = Departments::all();
            return view('admin.admin_tasks', ['tasks' => $tasks], ['departments' => $departments]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // View all researches (Presented, Completed, Published)
    function showAdminTasksResearches() 
    {
        if (Auth::guard('admin')->check()) {
            // TODO: Implement pagination for researches (presented, completed, published) and sort by date created (desc)
            $researchesPresented = AdminTasksResearchesPresented::orderBy('created_at', 'desc')->get()->each(function ($item) {
                $item->type = 'Presented';
            });
            $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')->get()->each(function ($item) {
                $item->type = 'Completed';
            });
            $researchesPublished = AdminTasksResearchesPublished::orderBy('created_at', 'desc')->get()->each(function ($item) {
                $item->type = 'Published';
            });
            
            $researches = $researchesPresented->concat($researchesCompleted)->concat($researchesPublished);
            
            $researches = $researches->sortByDesc('created_at');
            
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $researches->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($researches), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
            
            return view('admin.admin_tasks_researches', ['researches' => $paginator]);
            
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Create a new research (Presented, Completed, Published)
    function adminCreateResearch(Request $request) 
    {
        if (Auth::guard('admin')->check()) {
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
                $research->save();

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has created a research titled (' . $title . ').',
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
                $research->date_completed = $date_completed;
                $research->abstract = $abstract;
                $research->save();

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has created a research titled (' . $title . ').',
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
                $research->save();

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has created a research titled (' . $title . ').',
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

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // View a specific research category (Presented, Completed, Published)
    function showAdminTasksResearchesView(Request $request) {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');
            $faculties = Faculty::all();

            if (!$category || !$id) {
                return back();
            }

            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);

                if ($research) {
                    return view('admin.admin_tasks_researches_presented_view', 
                    [
                        'research' => $research,
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

                    return view('admin.admin_tasks_researches_completed_view', 
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
                $research = AdminTasksResearchesPublished::find($id);

                if ($research) {
                    // Format the date of Publication to "yyyy-MM-dd".
                    $dateOfPublication = Carbon::parse($research->date_of_publication)->format('Y-m-d');

                    return view('admin.admin_tasks_researches_published_view', 
                    [
                        'research' => $research,
                        'date_of_publication' => $dateOfPublication,
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

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Update the selected file
    function adminTasksResearchUpdate(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');

            // Update the research with corresponding category and id
            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);

                $title = $request->input('title');
                $initialTitle = $research->title;

                $authors = $request->input('authors');
                $host = $request->input('host');
                $level = $request->input('level');
                $files = $request->file('files');

                // Check if the title is unique
                if (AdminTasksResearchesPresented::where('title', $title)->where('id', '!=', $id)->exists()) {
                    return response()->json(['error' => 'A research with this title already exists.']);
                }

                $currentPath = $research->certificates;
                $researchesPresentedPath = 'Researches/Presented';

                if ($initialTitle !== $title) { // If the title has been changed
                    // Create new folder named $taskname (the new task name) 
                    Storage::disk('google')->makeDirectory($researchesPresentedPath . '/' . $title);
                    $newFolderPath = $researchesPresentedPath . '/' . $title;

                    // Get the $files and store them into the new folder
                    foreach ($files as $file) {
                        Storage::disk('google')->putFileAs($newFolderPath,
                        $file, 
                        $file->getClientOriginalName());
                        Storage::disk('google')->setVisibility($newFolderPath, 'public');
                        Storage::disk('google')->setVisibility($newFolderPath . '/' . $file->getClientOriginalName(), 'public');
                    }
            
                    // Get the contents of the old folder
                    $contents = Storage::disk('google')->listContents($currentPath);

                    // Copy the files from the old folder to the new folder
                    if (Storage::disk('google')->exists($newFolderPath)) {
                        foreach ($contents as $item) {
                            Storage::disk('google')->copy($item['path'], $newFolderPath . '/' . basename($item['path']));
                        }

                    }

                    // Remove files from the new folder that wasn't found on $files array
                    $folderPath = $newFolderPath;
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
                    
                    // Delete the old folder
                    Storage::disk('google')->deleteDirectory($currentPath);
                }
                else {
                    $folderPath = $currentPath;
                    Storage::disk('google')->setVisibility($folderPath . '/' . $initialTitle, 'public');
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
                }

                // Update the research
                $research->title = $title;
                $research->authors = $authors;
                $research->host = $host;
                $research->level = $level;
                $research->certificates = 'Researches/Presented/' . $title . '';
                $research->save();

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has updated a research named (' . $title . ').',
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

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has updated a research named (' . $title . ').',
                    'type_of_action' => 'Update Research',
                ]);

                return response()->json(['success' => 'Research updated successfully.']);
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::find($id);

                $title = $request->input('title');
                $authors = $request->input('authors');
                $journal = $request->input('journal');
                $date = $request->input('date');
                $link = $request->input('link');

                // Check if the title is unique
                if (AdminTasksResearchesPublished::where('title', $title)->where('id', '!=', $id)->exists()) {
                    return response()->json(['error' => 'A research with this title already exists.']);
                }

                // Update the research
                $research->title = $title;
                $research->authors = $authors;
                $research->name_of_journal = $journal;
                $research->date_published = $date;
                $research->link = $link;
                $research->save();

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has updated a research named (' . $title . ').',
                    'type_of_action' => 'Update Research',
                ]);

                return response()->json(['success' => 'Research updated successfully.']);
            } 
            else {
                $research = null;
            }
        }
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Delete the selected research
    function adminTasksResearchDelete(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category');
            $id = $request->input('id');

            if ($category === 'Presented') {
                $research = AdminTasksResearchesPresented::find($id);

                if ($research) {
                    $title = $research->title;
                    $folderPath = $research->certificates;
    
                    // Delete the folder and its contents
                    Storage::disk('google')->deleteDirectory($folderPath);       
                }
            } 
            else if ($category === 'Completed') {
                $research = AdminTasksResearchesCompleted::find($id);
            } 
            else if ($category === 'Published') {
                $research = AdminTasksResearchesPublished::find($id);
            } 
            else {
                return response()->json(['error' => 'Research not found.']);
            }

            // Delete the research
            $research->delete();

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has deleted a research named (' . $title . ').',
                'type_of_action' => 'Delete Research',
             ]);

            return response()->json(['message' => 'Research deleted successfully.']);

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Search for researches by title for all categories
    function showAdminTasksResearchesSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            $researchesPresented = AdminTasksResearchesPresented::where('title', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get()
                ->each(function ($item) {
                    $item->type = 'Presented';
                });

            $researchesCompleted = AdminTasksResearchesCompleted::where('title', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get()
                ->each(function ($item) {
                    $item->type = 'Completed';
                });

            $researchesPublished = AdminTasksResearchesPublished::where('title', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get()
                ->each(function ($item) {
                    $item->type = 'Published';
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
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Search for researches by category
    function showAdminTasksResearchesCategorySearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');
            $category = $request->input('category');

            if ($category === 'Presented') {
                $researchesPresented = AdminTasksResearchesPresented::orderBy('created_at', 'desc')
                    ->where('title', 'like', "%{$query}%")
                    ->get()
                    ->each(function ($item) {
                        $item->type = 'Presented';
                    });

                $researches = $researchesPresented;
            } else if ($category === 'Completed') {
                $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')
                    ->where('title', 'like', "%{$query}%")
                    ->get()
                    ->each(function ($item) {
                        $item->type = 'Completed';
                    });

                $researches = $researchesCompleted;
            } else if ($category === 'Published') {
                $researchesPublished = AdminTasksResearchesPublished::orderBy('created_at', 'desc')
                    ->where('title', 'like', "%{$query}%")
                    ->get()
                    ->each(function ($item) {
                        $item->type = 'Published';
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
            $formattedResearches = $paginator->map(function ($item) {
                return [
                    'title' => $item->title,
                    'authors' => $item->authors,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['researches' => $formattedResearches]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDashboardResearch()
    {
        if (Auth::guard('admin')->check()) {
            $faculties = Faculty::all();

            return view('admin.admin_dashboard_researches', ['faculties' => $faculties]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDashboardResearchGetAnalytics(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $member = $request->input('member');

            $researchesPresented = AdminTasksResearchesPresented::where('authors', 'like', "%{$member}%")->get();
            $researchesCompleted = AdminTasksResearchesCompleted::where('authors', 'like', "%{$member}%")->get();
            $researchesPublished = AdminTasksResearchesPublished::where('authors', 'like', "%{$member}%")->get();
            
            $totalResearches = $researchesPresented->count() + $researchesCompleted->count() + $researchesPublished->count();

            $data = [
                $researchesPresented->count(),
                $researchesCompleted->count(),
                $researchesPublished->count(),
            ];

            return response()->json([
                'data' => json_encode($data),
                'totalResearches' => $totalResearches,
                'researchesPresented' => $researchesPresented->count(),
                'researchesCompleted' => $researchesCompleted->count(),
                'researchesPublished' => $researchesPublished->count(),
            ]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Get attachments of a specific research
    function showAdminTasksResearchGetAttachments(Request $request)
    {
        if (Auth::guard('admin')->check()) {
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

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Return the URL of the selected file
    function showAdminTasksResearchPreviewFileSelected(Request $request)
    {
        if (Auth::guard('admin')->check()) {
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

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Show the researches presented page
    function showAdminTasksResearchesPresented() {
        if (Auth::guard('admin')->check()) {
            $researchesPresented = AdminTasksResearchesPresented::orderBy('created_at', 'desc')
                ->paginate(9);

            // include type to researchesPresented
            $researchesPresented->each(function ($item) {
                $item->type = 'Presented';
            });

            $faculties = Faculty::all();
            return view('admin.admin_tasks_researches_presented', 
            [
                'researches' => $researchesPresented,
                'faculties' => $faculties
            ]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    // Show the researches completed page
    function showAdminTasksResearchesCompleted() {
        if (Auth::guard('admin')->check()) {
            $researchesCompleted = AdminTasksResearchesCompleted::orderBy('created_at', 'desc')
                ->paginate(9);

            // include type to researchesCompleted
            $researchesCompleted->each(function ($item) {
                $item->type = 'Completed';
            });

            $faculties = Faculty::all();
            return view('admin.admin_tasks_researches_completed', 
            [
                'researches' => $researchesCompleted,
                'faculties' => $faculties
            ]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksResearchesPublished() {
        if (Auth::guard('admin')->check()) {
            $researchesPublished = AdminTasksResearchesPublished::orderBy('created_at', 'desc')
                ->paginate(9);

            // include type to researchesPublished
            $researchesPublished->each(function ($item) {
                $item->type = 'Published';
            });

            $faculties = Faculty::all();
            return view('admin.admin_tasks_researches_published', 
            [
                'researches' => $researchesPublished,
                'faculties' => $faculties
            ]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksExtensions()
    {
        if (Auth::guard('admin')->check()) {
            $extensions = Extension::orderBy('created_at', 'desc')
                ->paginate(9);

            return view('admin.admin_tasks_extensions', 
            [
                'items' => $extensions,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksExtensionsSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            $extensions = Extension::where('title', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $extensions->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($extensions), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedExtensions = $paginator->map(function ($item) {
                return [
                    'title' => $item->title,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['items' => $formattedExtensions]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksExtensionsCreate(Request $request) 
    {
        if (Auth::guard('admin')->check()) {
            $titleProgram = $request->input('titleProgram');
            $titleProject = $request->input('titleProject');
            $titleActivity = $request->input('titleActivity');
            $place = $request->input('place');
            $level = $request->input('level');
            $classification = $request->input('classification');
            $type = $request->input('type');
            $keywords = $request->input('keywords');
            $typeFunding = $request->input('typeFunding');
            $fundingAgency = $request->input('fundingAgency');
            $amountFunding = $request->input('amountFunding');
            $totalHours = $request->input('totalHours');
            $numberOfTrainees = $request->input('numberOfTrainees');
            $classificationOfTrainees = $request->input('classificationOfTrainees');
            $nature = $request->input('nature');
            $status = $request->input('status');
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');
            $extensionType = $request->input('extensionType');         

            // Create new extension object
            $extension = new Extension;
            $extension->title_of_extension_program = $titleProgram;
            $extension->title_of_extension_project = $titleProject;
            $extension->title_of_extension_activity = $titleActivity;
            $extension->place = $place;
            $extension->level = $level;
            $extension->classification = $classification;
            $extension->type = $type;
            $extension->keywords = $keywords;
            $extension->type_of_funding = $typeFunding;
            $extension->funding_agency = $fundingAgency;
            $extension->amount_of_funding = $amountFunding;
            $extension->total_no_of_hours = $totalHours;
            $extension->no_of_trainees = $numberOfTrainees;
            $extension->classification_of_trainees = $classificationOfTrainees;
            $extension->nature_of_involvement = $nature;
            $extension->status = $status;
            $extension->from_date = $dateFrom;
            $extension->to_date = $dateTo;
            $extension->type_of_extension = $extensionType;
            $extension->save();

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has created a new extension type of (' . $extensionType . ').',
                'type_of_action' => 'Create Extension',
            ]);

            $newlyAddedExtension = Extension::where('title_of_extension_program', $titleProgram)->first();
            $extensions = Extension::orderBy('created_at', 'desc')->get();
        
            return response()->json([
                'newlyAddedExtension' => $newlyAddedExtension,
                'allExtensions' => $extensions,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksExtensionsView(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $extension = Extension::find($id);

            if ($extension) {
                // Format the date conducted to "yyyy-MM-dd".
                $dateConducted = Carbon::parse($extension->date_conducted)->format('Y-m-d');

                return view('admin.admin_tasks_extensions_view', 
                [
                    'item' => $extension,
                    'date_conducted' => $dateConducted,
                    'id' => $id,
                ]);
            } 
            else {
                return back();
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksExtensionsUpdate(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $extension = Extension::find($id);

            $title = $request->input('title');
            $date = $request->input('date');
            $partner = $request->input('partner');
            $beneficiaries = $request->input('beneficiaries');
            $evaluation = $request->input('evaluation');

            // Check if the title is unique
            if (Extension::where('title', $title)->where('id', '!=', $id)->exists()) {
                return response()->json(['error' => 'An extension with this title already exists.']);
            }

            // Update the extension
            $extension->title = $title;
            $extension->date_conducted = $date;
            $extension->partner = $partner;
            $extension->beneficiaries = $beneficiaries;
            $extension->evaluation = $evaluation;
            $extension->save();

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has updated an extension titled (' . $title . ').',
                'type_of_action' => 'Update Extension',
            ]);

            return response()->json(['success' => 'Extension updated successfully.']);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksExtensionsDelete(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $extension = Extension::find($id);

            if ($extension) {
                $title = $extension->title;

                // Delete the extension
                $extension->delete();

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has deleted an extension titled (' . $title . ').',
                    'type_of_action' => 'Delete Extension',
                ]);

                return response()->json(['message' => 'Extension deleted successfully.']);
            } 
            else {
                return response()->json(['error' => 'Extension not found.']);
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksAttendance()
    {
        if (Auth::guard('admin')->check()) {
            $attendances = Attendance::orderBy('created_at', 'desc')
                ->paginate(9);

            return view('admin.admin_tasks_attendance', 
            [
                'items' => $attendances,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksAttendanceSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            $attendances = Attendance::where('name_of_activity', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $attendances->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($attendances), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedAttendances = $paginator->map(function ($item) {
                return [
                    'name_of_activity' => $item->name_of_activity,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['items' => $formattedAttendances]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksAttendanceCreate(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $name = $request->input('name');
            $venue = $request->input('venue');
            $host = $request->input('host');
            $date = $request->input('date');
            $files = $request->file('files');

            // Check if the name is unique
            if (Attendance::where('name_of_activity', $name)->exists()) {
                return response()->json(['error' => 'An attendance with this name already exists.']);
            }

            if ($files) {
                // Create the attendance folder if it doesn't exist
                if (!Storage::disk('google')->exists('Attendance')) {
                    Storage::disk('google')->makeDirectory('Attendance');
                    Storage::disk('google')->setVisibility('Attendance', 'public');
                }

                // Create the name folder if it doesn't exist
                if (!Storage::disk('google')->exists('Attendance/' . $name)) {
                    Storage::disk('google')->makeDirectory('Attendance/' . $name);
                    Storage::disk('google')->setVisibility('Attendance/' . $name, 'public');
                }

                // Store the files in the folder
                foreach ($files as $file) {
                    Storage::disk('google')->putFileAs('Attendance/' . $name, $file, $file->getClientOriginalName());
                    Storage::disk('google')->setVisibility('Attendance/' . $name . '/' . $file->getClientOriginalName(), 'public');
                }
            }

            // Create the attendance object
            $attendance = new Attendance;
            $attendance->name_of_activity = $name;
            $attendance->venue = $venue;
            $attendance->host = $host;
            $attendance->date_conducted = $date;
            $attendance->certificates = 'Attendance/' . $name;
            $attendance->save();

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has created an attendance named (' . $name . ').',
                'type_of_action' => 'Create Attendance',
            ]);

            // Newly added attendance object
            $newlyAddedAttendance = [
                'name_of_activity' => $attendance->name_of_activity,
                'date_created_formatted' => Carbon::parse($attendance->created_at)->format('F j, Y'),
                'date_created_time' => Carbon::parse($attendance->created_at)->format('g:i A'),
            ];

            // Format all attendances
            $attendances = Attendance::orderBy('created_at', 'desc')
                ->paginate(9);

            // Format
            $attendances = $attendances->map(function ($item) {
                return [
                    'name_of_activity' => $item->name_of_activity,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json([
                'newlyAddedAttendance' => $newlyAddedAttendance,
                'allAttendance' => $attendances,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksAttendanceView(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $attendance = Attendance::find($id);

            if ($attendance) {
                // Format the date conducted to "yyyy-MM-dd".
                $dateConducted = Carbon::parse($attendance->date_conducted)->format('Y-m-d');

                return view('admin.admin_tasks_attendance_view', 
                [
                    'item' => $attendance,
                    'date_conducted' => $dateConducted,
                    'id' => $id,
                ]);
            } 
            else {
                return back();
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksAttendanceUpdate(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $attendance = Attendance::find($id);

            $name = $request->input('name');
            $initialName = $attendance->name_of_activity;
            
            $venue = $request->input('venue');
            $host = $request->input('host');
            $date = $request->input('date');
            $files = $request->file('files');

            // Check if the name is unique
            if (Attendance::where('name_of_activity', $name)->where('id', '!=', $id)->exists()) {
                return response()->json(['error' => 'An attendance with this name already exists.']);
            }

            $currentPath = $attendance->certificates;
            $attendancePath = 'Attendance';

            if ($initialName !== $name) {
                // Create the new folder named $name
                Storage::disk('google')->makeDirectory($attendancePath . '/' . $name);
                $newFolderPath = $attendancePath . '/' . $name;

                // Get the $files and store them into the new folder
                foreach ($files as $file) {
                    Storage::disk('google')->putFileAs($newFolderPath,
                    $file,
                    $file->getClientOriginalName());
                    Storage::disk('google')->setVisibility($newFolderPath, 'public');
                    Storage::disk('google')->setVisibility($newFolderPath . '/' . $file->getClientOriginalName(), 'public');
                }

                // Get the contents of the old folder
                $contents = Storage::disk('google')->listContents($currentPath);

                // Copy the files from the old folder to the new folder
                if (Storage::disk('google')->exists($newFolderPath)) {
                    foreach ($contents as $item){
                        Storage::disk('google')->copy($item['path'], $newFolderPath . '/' . basename($item['path']));
                    }
                }

                // Remove files from the new folder that wasn't found on $files array
                $folderPath = $newFolderPath;
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
                
                // Delete the old folder
                Storage::disk('google')->deleteDirectory($currentPath);
            }
            else { // The name is the same
                $folderPath = $currentPath;
                Storage::disk('google')->setVisibility($folderPath . '/' . $initialName, 'public');

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
            }

            // Update the attendance
            $attendance->name_of_activity = $name;
            $attendance->venue = $venue;
            $attendance->host = $host;
            $attendance->date_conducted = $date;
            $attendance->certificates = $attendancePath . '/' . $name;
            $attendance->save();

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has updated an attendance named (' . $name . ').',
                'type_of_action' => 'Update Attendance',
            ]);

            return response()->json(['success' => 'Attendance updated successfully.']);
        }
    }

    function adminTasksAttendanceDelete(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $attendance = Attendance::find($id);

            if ($attendance) {
                $name = $attendance->name_of_activity;

                // Delete the attendance
                $attendance->delete();

                $folderPath = $attendance->certificates;
    
                // Delete the folder and its contents
                Storage::disk('google')->deleteDirectory($folderPath);       

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has deleted an attendance named (' . $name . ').',
                    'type_of_action' => 'Delete Attendance',
                ]);

                return response()->json(['message' => 'Attendance deleted successfully.']);
            } 
            else {
                return response()->json(['error' => 'Attendance not found.']);
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }
    
    function showAdminTasksAttendanceGetAttachments(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $attendance = Attendance::find($id);

            if ($attendance) {
                $fileNames = [];

                // Get all the contents in the specified directory
                $files = Storage::disk('google')->listContents($attendance->certificates);
                
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

                return response()->json($fileNames);
            } 
            else {
                return response()->json(['error' => 'Attendance not found.']);
            }

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksAttendancePreviewFileSelected(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $fileName = $request->input('fileName');

            $attendance = Attendance::find($id);

            if ($attendance) {
                // Set up the Google Drive API client
                $client = new Google_Client();
                $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
                $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
                $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
        
                // Set up the Google Drive service
                $service = new Google_Service_Drive($client);

                // Find the file on Google Drive
                $findFile = Storage::disk('google')->get($attendance->certificates . '/' . $fileName);

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
            else {
                return response()->json(['error' => 'Attendance not found.']);
            }

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksSeminars()
    {
        if (Auth::guard('admin')->check()) {
            $seminars = Seminars::orderBy('created_at', 'desc')
                ->paginate(9);

            return view('admin.admin_tasks_seminars', 
            [
                'items' => $seminars,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksSeminarsSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            $seminars = Seminars::where('name_of_activity', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $seminars->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            $paginator = new LengthAwarePaginator($currentItems, count($seminars), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

            // Format created_at date
            $formattedSeminars = $paginator->map(function ($item) {
                return [
                    'name_of_activity' => $item->name_of_activity,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json(['items' => $formattedSeminars]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksSeminarsCreate(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $name = $request->input('name');
            $venue = $request->input('venue');
            $host = $request->input('host');
            $level = $request->input('level');
            $date = $request->input('date');
            $files = $request->file('files');

            // Check if the name is unique
            if (Seminars::where('name_of_activity', $name)->exists()) {
                return response()->json(['error' => 'A seminar with this title already exists.']);
            }

            if ($files) {
                // Create the seminar folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars')) {
                    Storage::disk('google')->makeDirectory('Seminars');
                    Storage::disk('google')->setVisibility('Seminars', 'public');
                }

                // Create the name folder if it doesn't exist
                if (!Storage::disk('google')->exists('Seminars/' . $name)) {
                    Storage::disk('google')->makeDirectory('Seminars/' . $name);
                    Storage::disk('google')->setVisibility('Seminars/' . $name, 'public');
                }

                // Store the files in the folder
                foreach ($files as $file) {
                    Storage::disk('google')->putFileAs('Seminars/' . $name, $file, $file->getClientOriginalName());
                    Storage::disk('google')->setVisibility('Seminars/' . $name . '/' . $file->getClientOriginalName(), 'public');
                }
            }

            // Create the seminar object
            $seminar = new Seminars;
            $seminar->name_of_activity = $name;
            $seminar->venue = $venue;
            $seminar->host = $host;
            $seminar->date_conducted = $date;
            $seminar->level = $level;
            $seminar->certificates = 'Seminars/' . $name;
            $seminar->save();

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has created a seminar named (' . $name . ').',
                'type_of_action' => 'Create Seminar',
            ]);

            // Newly added seminar object
            $newlyAddedSeminar = [
                'name_of_activity' => $seminar->name_of_activity,
                'date_created_formatted' => Carbon::parse($seminar->created_at)->format('F j, Y'),
                'date_created_time' => Carbon::parse($seminar->created_at)->format('g:i A'),
            ];

            // Format all seminars
            $seminars = Seminars::orderBy('created_at', 'desc')
                ->paginate(9);

            // Format
            $seminars = $seminars->map(function ($item) {
                return [
                    'name_of_activity' => $item->name_of_activity,
                    'date_created_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_created_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json([
                'newlyAddedSeminar' => $newlyAddedSeminar,
                'allSeminars' => $seminars,
            ]);
        }
    }

    function showAdminTasksSeminarsView(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $seminar = Seminars::find($id);

            if ($seminar) {
                // Format the date conducted to "yyyy-MM-dd".
                $dateConducted = Carbon::parse($seminar->date_conducted)->format('Y-m-d');

                return view('admin.admin_tasks_seminars_view', 
                [
                    'item' => $seminar,
                    'date_conducted' => $dateConducted,
                    'id' => $id,
                ]);
            } 
            else {
                return back();
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminTasksSeminarsUpdate(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $seminar = Seminars::find($id);

            $name = $request->input('name');
            $initialName = $seminar->name_of_activity;
            
            $venue = $request->input('venue');
            $host = $request->input('host');
            $level = $request->input('level');
            $date = $request->input('date');
            $files = $request->file('files');

            // Check if the name is unique
            if (Seminars::where('name_of_activity', $name)->where('id', '!=', $id)->exists()) {
                return response()->json(['error' => 'A seminar with this title already exists.']);
            }

            $currentPath = $seminar->certificates;
            $seminarPath = 'Seminars';

            if ($initialName !== $name) {
                // Create the new folder named $name
                Storage::disk('google')->makeDirectory($seminarPath . '/' . $name);
                $newFolderPath = $seminarPath . '/' . $name;

                // Get the $files and store them into the new folder
                foreach ($files as $file) {
                    Storage::disk('google')->putFileAs($newFolderPath,
                    $file,
                    $file->getClientOriginalName());
                    Storage::disk('google')->setVisibility($newFolderPath, 'public');
                    Storage::disk('google')->setVisibility($newFolderPath . '/' . $file->getClientOriginalName(), 'public');
                }

                // Get the contents of the old folder
                $contents = Storage::disk('google')->listContents($currentPath);

                // Copy the files from the old folder to the new folder
                if (Storage::disk('google')->exists($newFolderPath)) {
                    foreach ($contents as $item){
                        Storage::disk('google')->copy($item['path'], $newFolderPath . '/' . basename($item['path']));
                    }
                }

                // Remove files from the new folder that wasn't found on $files array
                $folderPath = $newFolderPath;
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

                // Delete the old folder
                Storage::disk('google')->deleteDirectory($currentPath);
            }
            else {
                $folderPath = $currentPath;
                Storage::disk('google')->setVisibility($folderPath . '/' . $initialName, 'public');

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
            }

            // Update the seminar
            $seminar->name_of_activity = $name;
            $seminar->venue = $venue;
            $seminar->host = $host;
            $seminar->level = $level;
            $seminar->date_conducted = $date;
            $seminar->certificates = $seminarPath . '/' . $name;
            $seminar->save();
            
            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has updated a seminar named (' . $name . ').',
                'type_of_action' => 'Update Seminar',
            ]);

            return response()->json(['success' => 'Seminar updated successfully.']);
        }
    }

    function adminTasksSeminarsDelete(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $seminar = Seminars::find($id);

            if ($seminar) {
                $name = $seminar->name_of_activity;

                // Delete the seminar
                $seminar->delete();

                $folderPath = $seminar->certificates;

                // Delete the folder and its contents
                Storage::disk('google')->deleteDirectory($folderPath);    

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has deleted a seminar named (' . $name . ').',
                    'type_of_action' => 'Delete Seminar',
                ]);

                return response()->json(['message' => 'Seminar deleted successfully.']);
            } 
            else {
                return response()->json(['error' => 'Seminar not found.']);
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksSeminarsGetAttachments(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $seminar = Seminars::find($id);

            if ($seminar) {
                $fileNames = [];

                // Get all the contents in the specified directory
                $files = Storage::disk('google')->listContents($seminar->certificates);
                
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

                return response()->json($fileNames);
            } 
            else {
                return response()->json(['error' => 'Seminar not found.']);
            }

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksSeminarsPreviewFileSelected(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $id = $request->input('id');
            $fileName = $request->input('fileName');

            $seminar = Seminars::find($id);

            if ($seminar) {
                // Set up the Google Drive API client
                $client = new Google_Client();
                $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
                $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
                $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
        
                // Set up the Google Drive service
                $service = new Google_Service_Drive($client);

                // Find the file on Google Drive
                $findFile = Storage::disk('google')->get($seminar->certificates . '/' . $fileName);

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
            else {
                return response()->json(['error' => 'Seminar not found.']);
            }

        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminGetTask(Request $request) {
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
                return redirect('admin-tasks/get-task/instructions');
            }
            else {
                return back();
            }
        } else {
            return back();
        }
    }

    function adminGetTaskGetAttachments(Request $request) {
        if (Auth::guard('admin')->check()) {
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
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminGetTaskPreviewFileSelected(Request $request) {
        if (Auth::guard('admin')->check()) {
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
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminGetTaskDownloadFile(Request $request) {
        if (Auth::guard('admin')->check()) {
            $department = $request->input('department');
            $taskName = $request->input('taskName');
            $filename = $request->input('filename');
            $memberName = $request->input('memberName');
    
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
    
            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);
    
            try {
                // Find the file on Google Drive
                $file = 'Created Tasks/Departments/' . $department . '/Tasks/' . $taskName . '/Submissions/' . $memberName . '/' . $filename;
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

                    $admin = Auth::guard('admin')->user();
                    $adminUsername = $admin->username;

                    Logs::create([
                        'user_id' => $admin->id,
                        'user_role' => 'Admin',
                        'action_made' => '(' . $adminUsername . ') has downloaded an output file named (' . $filename . ').',
                        'type_of_action' => 'Download Output File',
                    ]);
    
                    // Return the file ID in the response
                    return response()->json(['fileId' => $file->getId()]);
                }
            } catch (Exception $e) {
                // An error occurred
                return response()->json(['error' => $e->getMessage()]);
            }
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminGetTaskDownloadAllFileDeleteTempZip(Request $request) {
        if (Auth::guard('admin')->check()) {
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
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminGetTaskDownloadAllFile(Request $request) {
        if (Auth::guard('admin')->check()) {
            $folderPath = $request->input('folderPath');
            $department = trim($request->input('department'));
            $taskName = trim($request->input('taskName'));
            $memberName = ($request->input('memberName'));
    
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
    
            // Set up the Google Drive service
            $service = new Google_Service_Drive($client);

            // Set the path to the task folder
            $folderPath = 'Created Tasks/Departments/' . $department . '/Tasks/' . $taskName . '/Submissions/' . $memberName;

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

                // Skip adding the zip file to itself
                if ($fileName == $taskName . ' Task') {
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
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminGetTaskInstructions(Request $request) {
        if (Auth::guard('admin')->check()) {
            $taskName = $request->session()->get('taskName');

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

                return view('admin.admin_get_task_instructions', [
                    'taskName' => $taskName,
                    'taskID' => $taskID,
                    'departmentName' => $departmentName,
                    'departments' => $departments,
                    'assignedto' => $assignedto,
                    'due_date' => $due_date,
                    'description' => $description,
                    'folderPath' => $folderPath,
                    'assignedMembers' => $assignedMembers,
                    'numberOfAssignedMembers' => $numberOfAssignedMembers,
                ], ['members' => $members]);
            }
            else{
                return back();
            }
        }
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminGetTaskSubmissionsGetAttachments(Request $request){
        if (Auth::guard('admin')->check()) {
            $taskId = $request->input('id');
            $memberName = $request->input('memberName');

            $facultyTaskTable = FacultyTasks::where('submitted_by', $memberName)
                                ->where('task_id', $taskId)
                                ->first();

            $folderPath = $facultyTaskTable->attachments;
            $description = $facultyTaskTable->description;
            $date_submitted = $facultyTaskTable->date_submitted;

            if ($date_submitted === null) { // Hide outputs when unsubmitted so admin can't see it yet
                
                return response()->json(
                    ['fileNames' => [],
                     'description' => '',
                     'date_submitted' => null,
                    ]);
            }

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
        
            return response()->json(
                ['fileNames' => $fileNames,
                 'description' => $description,
                 'date_submitted' => $date_submitted,
                ]);
        }
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminGetTaskSubmissions(Request $request) {
        if (Auth::guard('admin')->check()) {

            if ($request->input('requestSource') == 'department') {
                $request->session()->put('selectedIn', 'department');
            }
            else if ($request->input('requestSource') == 'navbar') {
                $request->session()->put('selectedIn', 'navbar');
            }

            $taskName = $request->session()->get('taskName');

            if ($taskName) {

                $adminTaskTable = AdminTasks::where('task_name', $taskName)->first();

                if (!$adminTaskTable) { // maybe changed task name
                    return redirect('admin-tasks');
                }

                $taskID = $adminTaskTable->id;

                $departmentName = $adminTaskTable->faculty_name;
                $folderPath = $adminTaskTable->attachments;

                $assignedMembers = FacultyTasks::where('task_id', $taskID)
                ->orderByRaw('ISNULL(date_submitted), date_submitted ASC')
                ->get();
            
                return view('admin.admin_get_task_submissions', 
                [
                    'taskName' => $taskName,
                    'taskID' => $taskID,
                    'departmentName' => $departmentName,
                    'assignedMembers' => $assignedMembers,
                    'folderPath' => $folderPath,
                    'deadline' => $adminTaskTable->due_date,
                ]);
            }
            else{
                return back();
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminGetTaskSubmissionsDecide(Request $request) {
        if (Auth::guard('admin')->check()) {
            $memberName = $request->input('memberName');
            $taskName = $request->input('taskName');
            $decision = $request->input('decision');

            if ($decision) {
                $adminTaskTable = AdminTasks::where('task_name', $taskName)->first();
                $taskId = $adminTaskTable->id;
                
                $getUserId = FacultyTasks::where('submitted_by', $memberName)->first();
                $userId = $getUserId->submitted_by_id;

                $submission = FacultyTasks::where('submitted_by_id', $userId)
                            ->where('task_id', $taskId)
                            ->first();
     
                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                if ($decision === 'approve') {
                    $submission->decision = 'Approved';

                    Logs::create([
                        'user_id' => $admin->id,
                        'user_role' => 'Admin',
                        'action_made' => '(' . $adminUsername . ') has approved a task output named (' . $taskName . ') from ' . $memberName . '.',
                        'type_of_action' => 'Approve Task Output',
                    ]);
                } 
                else if ($decision === 'reject') {
                    $submission->decision = 'Rejected';

                    Logs::create([
                        'user_id' => $admin->id,
                        'user_role' => 'Admin',
                        'action_made' => '(' . $adminUsername . ') has rejected a task output named (' . $taskName . ') from ' . $memberName . '.',
                        'type_of_action' => 'Reject Task Output',
                    ]);
                } 

                $submission->save();

                return response()->json($submission->decision);
            }
            else {
                return back();
            }
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDepartmentOverview(Request $request) {
        if (Auth::guard('admin')->check()) {
            $department = $request->session()->get('department');
            
            $adminTaskTable = AdminTasks::where('faculty_name', $department)->first();

            $departmentName = $department;

            if (!$adminTaskTable) {
                return view('admin.admin_show_department_overview', 
                            ['status' => null,
                            'departmentName' => $departmentName,
                            'taskID' => null,
                            'data' => json_encode([1, 2, 3, 4]),
                            'assigned' => null,
                            'completed' => null,
                            'late_completed' => null,
                            'ongoing' => null,
                            'missing' => null,]);
            }

            $taskID = $adminTaskTable->id;
            $adminTasks = AdminTasks::where('faculty_name', $departmentName)->pluck('id');
            $assigned = FacultyTasks::whereIn('task_id', $adminTasks)->count();
            
            $completed = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('status', 'Completed')
                        ->count();

            $late_completed = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('status', 'Late Completed')
                        ->count();

            $ongoing = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('status', 'Ongoing')
                        ->count();

            $missing = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('status', 'Missing')
                        ->count();

            $data = [$completed, $late_completed, $ongoing, $missing];

            return view('admin.admin_show_department_overview', 
                        ['status' => 'datafound',
                         'departmentName' => $departmentName,
                         'taskID' => $taskID,
                         'data' => json_encode($data),
                         'assigned' => $assigned,
                         'completed' => $completed,
                         'late_completed' => $late_completed,
                         'ongoing' => $ongoing,
                         'missing' => $missing,]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminGetTaskTaskOverview(Request $request) {
        if (Auth::guard('admin')->check()) {
            $taskName = $request->session()->get('taskName');
            $adminTaskTable = AdminTasks::where('task_name', $taskName)->first();

            if (!$adminTaskTable) {
                return redirect('admin-tasks');
            }
            
            $taskID = $adminTaskTable->id;
            $departmentName = $adminTaskTable->faculty_name;

            $assigned = FacultyTasks::where('task_id', $taskID)->count();
            $completed = FacultyTasks::where('task_id', $taskID)->where('status', 'Completed')->count();
            $late_completed = FacultyTasks::where('task_id', $taskID)->where('status', 'Late Completed')->count();
            $ongoing = FacultyTasks::where('task_id', $taskID)->where('status', 'Ongoing')->count();
            $missing = FacultyTasks::where('task_id', $taskID)->where('status', 'Missing')->count();

            $data = [$completed, $late_completed, $ongoing, $missing];

            return view('admin.admin_get_task_task_overview', 
                        ['departmentName' => $departmentName,
                         'taskID' => $taskID,
                         'taskName' => $taskName,
                         'data' => json_encode($data),
                         'assigned' => $assigned,
                         'completed' => $completed,
                         'late_completed' => $late_completed,
                         'ongoing' => $ongoing,
                         'missing' => $missing,]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminGetDepartmentMembers(Request $request)
    {
        $department = $request->input('department');

        $members = Faculty::where('department', $department)
            ->orderBy('first_name', 'asc')
            ->get();

        return response()->json([
            'members' => $members
        ]);
    }

    function adminFilterDepartment(Request $request)
    {
        $department = $request->input('department');

        if ($department === 'All') {
            $filterDepartment = AdminTasks::orderBy('created_at', 'desc')->paginate(9);
        }
        else {
            $filterDepartment = AdminTasks::where('faculty_name', $department)
                ->orderBy('created_at', 'desc')
                ->paginate(9);
        }

        $formattedTasks = $filterDepartment->map(function ($task) {
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

    function adminCreateTask(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $taskName = $request->input('taskname');
            $assignedto = json_decode($request->input('assignto'));
            $selectFaculty = $request->input('selectFaculty');
            $dueDate = $request->input('dateTimePicker');
            $description = $request->input('description');
            $files = $request->file('files');

            // check for uniqueness of task_name
            if (AdminTasks::where('task_name', $taskName)->exists()) {
                // task_name is not unique, return an error
                return response()->json([
                    'error' => 'A task with this name already exists.'
                ]);
            }

            // Check for characters that are not letters, numbers or whitespace. 
            if (preg_match('/[^a-zA-Z0-9\s]/', $taskName)) {
                return response()->json([
                    'error' => 'Invalid task name.'
                ]);
            }

            if ($files) { // Check if there is file to be uploaded, if so then check if folders are existing
                $this->makeAndCreateFoldersPublic($selectFaculty, $taskName);
            }

            if ($files) { // Check if there are files to upload to gdrive
                // Get path to '$taskName' folder
                $adapter = Storage::disk('google')->getAdapter();
                $metadata = $adapter->getMetadata('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName);
                $id = $metadata['path'];

                // Store files in '$taskName' folder
                foreach ($files as $file) {
                    Storage::disk('google')->putFileAs(
                        'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName,
                        $file,
                        $file->getClientOriginalName()
                    );
                    Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName . '/' . $file->getClientOriginalName(), 'public');
                }
            } else {
                $id = null;
            }

            $adminTask = new AdminTasks;
            $adminTask->task_name = $taskName;
            $adminTask->faculty_image_link = 'admin/images/home.svg';
            $adminTask->faculty_name = $selectFaculty;
            $adminTask->assigned_to = implode(', ', $assignedto);
            $adminTask->description = $description;
            $adminTask->attachments = $id;
            $adminTask->date_created = Carbon::now();
            $adminTask->due_date = $dueDate;
            $adminTask->save();

            // Populate faculty tasks table with each assignee per row
            foreach ($assignedto as $name) {
                $facultyTask = new FacultyTasks;
                $facultyTask->task_id = $adminTask->id;

                // Check if the full name matches a row in the Faculty table
                $faculty = Faculty::whereRaw("REPLACE(TRIM(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)), '  ', ' ') = ?", [$name])->first();

                // If a matching row is found, set the submitted_by_id property to the ID of the row
                if ($faculty) {
                    $facultyTask->submitted_by_id = $faculty->id;
                }

                $facultyTask->submitted_by = $name;
                $facultyTask->status = 'Ongoing';
                $facultyTask->decision = 'Not decided';
                
                $facultyTask->save();
            }

            // Create the newly added task object
            $newlyAddedTask = [
                'task_name' => $adminTask->task_name,
                'faculty_image' => $adminTask->faculty_image_link,
                'faculty_name' => $adminTask->faculty_name,
                'date_created_formatted' => Carbon::parse($adminTask->created_at)->format('F j, Y'),
                'date_created_time' => Carbon::parse($adminTask->created_at)->format('g:i A'),
                'due_date_formatted' => Carbon::parse($adminTask->due_date)->format('F j, Y'),
                'due_date_time' => Carbon::parse($adminTask->due_date)->format('g:i A'),
                'due_date_past' => Carbon::parse($adminTask->due_date)->isPast(),
            ];

            // Get all tasks
            $tasks = AdminTasks::orderBy('created_at', 'desc')->paginate(9);

            // Format all tasks
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

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has created a task named (' . $taskName . ').',
                'type_of_action' => 'Create Task',
            ]);

            // Return the newly added task object and the array of all tasks as separate variables
            return response()->json([
                'newlyAddedTask' => $newlyAddedTask,
                'allTasks' => $formattedTasks,
            ]);
            
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminUpdateTask(Request $request){
        if (Auth::guard('admin')->check()) {
            $taskID = $request->input('taskID');
            $initialTaskName = $request->input('initialTaskName');
            $taskName = $request->input('taskname');
            $assignedto = json_decode($request->input('assignto'));
            $selectFaculty = $request->input('selectFaculty');
            $dueDate = $request->input('dateTimePicker');
            $description = $request->input('description');
            $files = $request->file('files');

            // check for uniqueness of task_name
            if ($taskName != $initialTaskName && AdminTasks::where('task_name', $taskName)->exists()) {
                // task_name is not unique and has been changed, return an error
                return response()->json([
                    'error' => 'A task with this name already exists.'
                ]);
            }
            
            // Check for characters that are not letters, numbers or whitespace. 
            if (preg_match('/[^a-zA-Z0-9\s]/', $taskName)) {
                return response()->json([
                    'error' => 'Invalid task name.'
                ]);
            }

            $adminTask = AdminTasks::where('id', $taskID)->first();
            $currentPath = $adminTask->attachments;

            if ($currentPath) { // Which means there is a folder path in google drive
                if ($initialTaskName != $taskName) { // If the task name is changed

                    // Create new folder named $taskname (the new task name) 
                    Storage::disk('google')->makeDirectory(dirname($currentPath) . '/' . $taskName);
                    $newFolderPath = dirname($currentPath) . '/' . $taskName;

                    // Get the $files and store them into the new folder
                    foreach ($files as $file) {
                        Storage::disk('google')->putFileAs('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName, 
                        $file, 
                        $file->getClientOriginalName());
                        Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName, 'public');
                        Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName . '/' . $file->getClientOriginalName(), 'public');
                    }
            
                    // Get the contents of the old folder
                    $contents = Storage::disk('google')->listContents($currentPath);

                    // Copy the files from the old folder to the new folder
                    if (Storage::disk('google')->exists('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName)) {
                        foreach ($contents as $item) {
                            Storage::disk('google')->copy($item['path'], $newFolderPath . '/' . basename($item['path']));
                        }

                        // Set the new attachment path in table with new folder path
                        $id = $newFolderPath;
                    }

                    // Remove files from the new folder that wasn't found on $files array
                    $folderPath = 'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName;
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
                    
                    // Delete the old folder
                    Storage::disk('google')->deleteDirectory($currentPath);

                }
                else { // If task name isn't changed but need to modify the content of folder
                    if (!$files) { // If there is no files in the file preview, then just delete the folder
                        $id = null;
                        $folderToRemove = 'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $initialTaskName;
                        Storage::disk('google')->deleteDirectory($folderToRemove);
                    }
                    else { // If there is file to be uploaded in the current folder (current task name)
                        $folderPath = 'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $initialTaskName;
                        Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $initialTaskName, 'public');
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
                        $folderPath = 'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $initialTaskName;
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
                        
                        // Set the path as the current path, stays the same.
                        $id = $currentPath;
                    }
                }
            }
            else { // If current path == null, which means no folder yet in gdrive
                if ($files) { // If there is/are files to upload

                    // Make a new folder named as the task name
                    Storage::disk('google')->makeDirectory(dirname($currentPath) . '/' . $taskName);
                    
                    // Upload the files selected to the gdrive
                    foreach ($files as $file) {
                        Storage::disk('google')->putFileAs('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName, 
                        $file, 
                        $file->getClientOriginalName());
                        Storage::disk('google')->setVisibility('Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName . '/' . $file->getClientOriginalName(), 'public');
                    }

                    // Set the attachment path in the table as the new folder path
                    $id = 'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $taskName;
                }
                else{ // If no files to be uploaded, then set the path in the table to null
                    $id = null;
                }
            }

            // If all files are removed in the file preview, then remove the path in table and remove folder from gdrive
            if (!$files) {
                $id = null;
                $folderToRemove = 'Created Tasks/Departments/' . $selectFaculty . '/Tasks/' . $initialTaskName;
                Storage::disk('google')->deleteDirectory($folderToRemove);
            }

            $adminTask->task_name = $taskName;
            $adminTask->faculty_image_link = 'admin/images/home.svg';
            $adminTask->faculty_name = $selectFaculty;
            $adminTask->assigned_to = implode(', ', $assignedto);
            $adminTask->description = $description;
            $adminTask->attachments = $id;
            $adminTask->due_date = $dueDate;
            $adminTask->save();

            $newAssigned = $assignedto;

            // Delete or Add row if assigned_to is modified
            $existingFacultyTasks = FacultyTasks::where('task_id', $adminTask->id)->get();

            foreach ($existingFacultyTasks as $facultyTask) {
                if (!in_array($facultyTask->submitted_by, $assignedto)) {
                    $facultyTask->delete();
                }
            }
            
            foreach ($assignedto as $name) {
                $faculty = Faculty::whereRaw("REPLACE(TRIM(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)), '  ', ' ') = ?", [$name])->first();
            
                if ($faculty && !FacultyTasks::where('task_id', $adminTask->id)->where('submitted_by_id', $faculty->id)->exists()) {
                    $facultyTask = new FacultyTasks;
                    $facultyTask->task_id = $adminTask->id;
                    $facultyTask->submitted_by = $name;
                    $facultyTask->submitted_by_id = $faculty->id;
                    
                    $facultyTask->status = 'Ongoing';
                    $facultyTask->decision = 'Not decided';

                    $facultyTask->save();
                }
            }

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has updated a task named (' . $taskName . ').',
                'type_of_action' => 'Update Task',
            ]);
            
            return response()->json([
                'newTaskName' => $taskName,
                'assigned_to' => $newAssigned
            ]);
            
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            if (empty($query)) {
                $tasks = AdminTasks::orderBy('created_at', 'desc')->paginate(9);
            } else {
                $tasks = AdminTasks::where('task_name', 'like', "%{$query}%")
                    //->orWhere('faculty_name', 'like', "%{$query}%")
                    //->orWhere('date_created', 'like', "%{$query}%")
                    //->orWhere('due_date', 'like', "%{$query}%")
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
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
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function getCategoryTask($status) {
        $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) use ($status) {
            $query->where('status', $status);
        })->whereDoesntHave('FacultyTasks', function ($query) use ($status) {
            $query->where('status', '!=', $status);
        })->orderBy('created_at', 'desc')
          ->paginate(9);
    
        return $tasks;
    }    

    function showAdminTasksGetCategory(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category'); 
            $departments = Departments::all();
            $route = $request->route();

            /*if (!$request->session()->get('department')) {
                return back();
            }*/

            $department = $request->session()->get('department');

            if ($category === 'completed') {
                if (Str::startsWith($route->getName(), 'admin-tasks')) {
                    $tasks = $this->getCategoryTask('Completed');
                    return view('admin.admin_tasks_completed', ['tasks' => $tasks, 'departments' => $departments]);
                }
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query) {
                        $query->where('status', '!=', 'Completed');
                    })->where('faculty_name', $department)
                      ->orderBy('created_at', 'desc')
                      ->paginate(9);

                    return view('admin.admin_show_department_assigned_tasks_completed', ['tasks' => $tasks, 'department' => $department, 'departments' => $departments]);
                }
            }
            else if ($category === 'late-completed') {
                if (Str::startsWith($route->getName(), 'admin-tasks')) {
                    $tasks = $this->getCategoryTask('Late Completed');
                    return view('admin.admin_tasks_late_completed', ['tasks' => $tasks, 'departments' => $departments]);
                }
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query) {
                        $query->where('status', '!=', 'Late Completed');
                    })->where('faculty_name', $department)
                      ->orderBy('created_at', 'desc')
                      ->paginate(9);
                    return view('admin.admin_show_department_assigned_tasks_late_completed', ['tasks' => $tasks, 'department' => $department, 'departments' => $departments]);
                }
            }
            else if ($category === 'ongoing') {
                if (Str::startsWith($route->getName(), 'admin-tasks')) {
                     // If one member have ongoing
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                    return view('admin.admin_tasks_ongoing', ['tasks' => $tasks, 'departments' => $departments]);
                }
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                    })->where('faculty_name', $department)
                      ->orderBy('created_at', 'desc')
                      ->paginate(9);

                    return view('admin.admin_show_department_assigned_tasks_ongoing', ['tasks' => $tasks, 'department' => $department, 'departments' => $departments]);
                }
            }
            else if ($category === 'missing') {
                if (Str::startsWith($route->getName(), 'admin-tasks')) {
                    // If one member have missing
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                    return view('admin.admin_tasks_missing', ['tasks' => $tasks, 'departments' => $departments]);
                }
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
                    })->where('faculty_name', $department)
                      ->orderBy('created_at', 'desc')
                      ->paginate(9);

                    return view('admin.admin_show_department_assigned_tasks_missing', ['tasks' => $tasks, 'department' => $department, 'departments' => $departments, ]);
                }
                
            }
            else{
                return back();
            }

        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksCategorySearch(Request $request) {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category'); 
            $query = $request->input('query');

            if (!$request->session()->get('department')) {
                return back();
            }

            $department = $request->session()->get('department');

            if ($category === 'completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->orderBy('created_at', 'desc')
                      ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'late-completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->orderBy('created_at', 'desc')
                        ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->where('task_name', 'like', "%{$query}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'ongoing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                    })->where('task_name', 'like', "%{$query}%")
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
                }
            }
            else if ($category === 'missing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
                    })->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
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
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminTasksDepartmentCategorySearch(Request $request) {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category'); 
            $query = $request->input('query');

            if (!$request->session()->get('department')) {
                return back();
            }

            $department = $request->session()->get('department');

            if ($category === 'completed') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                      ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
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
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                        ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->where('task_name', 'like', "%{$query}%")
                        ->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'ongoing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                    })->where('task_name', 'like', "%{$query}%")
                    ->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
                }
            }
            else if ($category === 'missing') {
                if (empty($query)) {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
                    })->where('faculty_name', $department)
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);

                } 
                else {
                    $tasks = AdminTasks::whereHas('FacultyTasks', function ($query) {
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
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminCategoryFilterDepartment(Request $request) {
        if (Auth::guard('admin')->check()) {
            $category = $request->input('category'); 
            $department = $request->input('department');

            if ($category === 'completed') {
                if ($department === 'All') {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
                else {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Completed');
                    })->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'late-completed') {
                if ($department === 'All') {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
                else {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Late Completed');
                    })->whereDoesntHave('FacultyTasks', function ($query){
                        $query->where('status', '!=', 'Late Completed');
                    })->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'ongoing') {
                if ($department === 'All') {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                        })->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
                else {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Ongoing');
                        })->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else if ($category === 'missing') {
                if ($department === 'All') {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
                        })->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
                else {
                    $filterDepartment = AdminTasks::whereHas('FacultyTasks', function ($query) {
                        $query->where('status', 'Missing');
                        })->where('faculty_name', $department)
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);
                }
            }
            else{
                return back();
            }
            
            $formattedTasks = $filterDepartment->map(function ($task) {
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
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminRequestsAccount()
    {
        if (Auth::guard('admin')->check()) {
            $items = FacultyPendingAccounts::orderBy('request_date', 'desc')->paginate(9);
            return view('admin.admin_requests_account', ['items' => $items]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminRequestsAccountSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            if (empty($query)) {
                $items = FacultyPendingAccounts::orderBy('request_date', 'desc')->paginate(9);
            } else {
                $items = FacultyPendingAccounts::where('username', 'like', "%{$query}%")
                    ->orderBy('request_date', 'desc')
                    ->paginate(9);
            }

            $formattedItems = $items->map(function ($item) {
                return [
                    'username' => $item->username,
                    'email' => $item->email,
                    'request_date_formatted' => Carbon::parse($item->request_date)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->request_date)->format('g:i A'),
                ];
            });

            return response()->json($formattedItems);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsAccountAccept(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // send email

            $username = $request->input('username');
            $email = $request->input('email');

            // Retrieve the data/row from the pending table
            $getDataFromPendingTable = FacultyPendingAccounts::where('username', $username)->where('email', $email)->first();

            // Create a new data/row in the faculty table with the same data
            $insertDataToFacultyTable = new Faculty;
            $insertDataToFacultyTable->fill($getDataFromPendingTable->toArray());
            $save = $insertDataToFacultyTable->save();

            // Delete the data/row from the pending table
            $getDataFromPendingTable->delete();

            if ($save) {
                $items = FacultyPendingAccounts::orderBy('request_date', 'desc')->paginate(9);

                $formattedTasks = $items->map(function ($item) {
                    return [
                        'username' => $item->username,
                        'email' => $item->email,
                        'request_date_formatted' => Carbon::parse($item->request_date)->format('F j, Y'),
                        'request_date_time' => Carbon::parse($item->request_date)->format('g:i A'),
                    ];
                });

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                $faculty = $getDataFromPendingTable;
                $facultyUsername = $faculty->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has accepted a Faculty account (' . $facultyUsername . ').',
                    'type_of_action' => 'Accept Account',
                ]);

                Mail::raw('Hello ' . $username . ', your account was successfully approved by ' . $adminUsername . '.', function ($message) use ($email) {
                    $message->to($email)
                            ->subject('Account Approval');
                });                

                return response()->json($formattedTasks);
                //return back()->with('success', "Faculty account successfully registered.");
            } else {
                return back()->with('fail', "Something went wrong, try again later.");
            }
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsAccountReject(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // send email

            $username = $request->input('username');
            $email = $request->input('email');

            $getDataFromPendingTable = FacultyPendingAccounts::where('username', $username)->where('email', $email)->first();

            // Reject/Delete the data/row from the pending table
            $getDataFromPendingTable->delete();

            $items = FacultyPendingAccounts::orderBy('request_date', 'desc')->paginate(9);
            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->username,
                    'email' => $item->email,
                    'request_date_formatted' => Carbon::parse($item->request_date)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->request_date)->format('g:i A'),
                ];
            });

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            $faculty = $getDataFromPendingTable;
            $facultyUsername = $faculty->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has rejected a Faculty account (' . $facultyUsername . ').',
                'type_of_action' => 'Reject Account',
            ]);

            return response()->json($formattedTasks);
            //return back()->with('success', "Faculty account successfully registered.");
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsAccountAcceptAll(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // send email

            // Retrieve all data/rows from the pending table
            $getDataFromPendingTable = FacultyPendingAccounts::all();

            foreach ($getDataFromPendingTable as $data) {
                // Create a new data/row in the faculty table with the same data
                $insertDataToFacultyTable = new Faculty;
                $insertDataToFacultyTable->fill($data->toArray());
                $save = $insertDataToFacultyTable->save();

                // Delete the data/row from the pending table
                $data->delete();
            }

            if ($save) {
                $items = FacultyPendingAccounts::orderBy('request_date', 'desc')->paginate(9);

                $formattedTasks = $items->map(function ($item) {
                    return [
                        'username' => $item->username,
                        'email' => $item->email,
                        'request_date_formatted' => Carbon::parse($item->request_date)->format('F j, Y'),
                        'request_date_time' => Carbon::parse($item->request_date)->format('g:i A'),
                    ];
                });

                $admin = Auth::guard('admin')->user();
                $adminUsername = $admin->username;

                Logs::create([
                    'user_id' => $admin->id,
                    'user_role' => 'Admin',
                    'action_made' => '(' . $adminUsername . ') has accepted all faculty waitlist accounts.',
                    'type_of_action' => 'Accept All Account',
                ]);

                return response()->json($formattedTasks);
                //return back()->with('success', "Faculty account successfully registered.");
            } else {
                return back()->with('fail', "Something went wrong, try again later.");
            }
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsAccountRejectAll(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // send email

            // Retrieve all data/rows from the pending table
            $getDataFromPendingTable = FacultyPendingAccounts::all();

            foreach ($getDataFromPendingTable as $data) {
                // Reject/Delete the data/row from the pending table
                $data->delete();
            }

            $items = FacultyPendingAccounts::orderBy('request_date', 'desc')->paginate(9);
            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->username,
                    'email' => $item->email,
                    'request_date_formatted' => Carbon::parse($item->request_date)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->request_date)->format('g:i A'),
                ];
            });

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has rejected all faculty waitlist accounts.',
                'type_of_action' => 'Reject All Account',
            ]);

            return response()->json($formattedTasks);
            //return back()->with('success', "Faculty account successfully registered.");
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminRequestsDepartment()
    {
        if (Auth::guard('admin')->check()) {
            $items = DepartmentPendingJoins::with('getFacultyForeign')->orderBy('created_at', 'desc')->paginate(9);

            return view('admin.admin_requests_department', ['items' => $items]);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminRequestsDepartmentSearch(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            if (empty($query)) {
                $items = DepartmentPendingJoins::with('getFacultyForeign')->orderBy('created_at', 'desc')->paginate(9);
            } else {
                $queryParts = explode(' ', $query);
                $items = DepartmentPendingJoins::with('getFacultyForeign')
                    ->whereHas('getFacultyForeign', function ($q) use ($queryParts) {
                        foreach ($queryParts as $queryPart) {
                            $q->where(function ($q) use ($queryPart) {
                                $q->where('first_name', 'like', "%{$queryPart}%")
                                    ->orWhere('middle_name', 'like', "%{$queryPart}%")
                                    ->orWhere('last_name', 'like', "%{$queryPart}%");
                            });
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(9);
            }

            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->getFacultyForeign->username,
                    'fullname' => $item->getFacultyForeign->first_name . ' ' . $item->getFacultyForeign->middle_name . ' ' . $item->getFacultyForeign->last_name,
                    'departmentRequest' => $item->department_name,
                    'request_date_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json($formattedTasks);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsDepartmentAccept(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // send email

            $username = $request->input('username');
            $departmentToJoin = $request->input('departmentToJoin');

            // Increment the number of members of the specified department
            Departments::where('department_name', $departmentToJoin)->increment('number_of_members');

            // Get the department then use it to update the department_id of a specific faculty
            $department = Departments::where('department_name', $departmentToJoin)->first();

            Faculty::where('username', $username)->update([
                'department' => $departmentToJoin,
                'department_id' => $department->id,
                'department_join_date' => Carbon::now()
            ]);

            // Delete the row in the department pending joins
            $faculty = Faculty::where('username', $username)->first();
            $facultyID = $faculty->id;
            DepartmentPendingJoins::where('faculty_id', $facultyID)->delete();

            $items = DepartmentPendingJoins::with('getFacultyForeign')->orderBy('created_at', 'desc')->paginate(9);
            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->getFacultyForeign->username,
                    'fullname' => $item->getFacultyForeign->first_name . ' ' . $item->getFacultyForeign->middle_name . ' ' . $item->getFacultyForeign->last_name,
                    'departmentRequest' => $item->department_name,
                    'request_date_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has accepted a (' . $departmentToJoin . ') department join request from (' . $facultyUsername . ') ' . $facultyFullName . '.',
                'type_of_action' => 'Accept Department',
            ]);

            return response()->json($formattedTasks);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsDepartmentAcceptAll(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $allData = $request->input('allData');

            foreach ($allData as $data) {
                $username = $data['username'];
                $department = $data['departmentToJoin'];
                $department_id = Departments::where('department_name', $department)->first()->id;

                // Increment the specified department 
                Departments::where('department_name', $department)->increment('number_of_members');

                // Update a specified faculty user to corresponding department name and department id
                Faculty::where('username', $username)->update([
                    'department' => $department,
                    'department_id' => $department_id,
                    'department_join_date' => Carbon::now()
                ]);
            }

            // Delete all rows in department pending requests
            DepartmentPendingJoins::truncate();

            $items = DepartmentPendingJoins::with('getFacultyForeign')->orderBy('created_at', 'desc')->paginate(9);
            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->getFacultyForeign->username,
                    'fullname' => $item->getFacultyForeign->first_name . ' ' . $item->getFacultyForeign->middle_name . ' ' . $item->getFacultyForeign->last_name,
                    'departmentRequest' => $item->department_name,
                    'request_date_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has accepted all department join requests.',
                'type_of_action' => 'Accept All Department',
            ]);

            return response()->json($formattedTasks);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsDepartmentReject(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // send email

            $username = $request->input('username');
            $departmentToJoin = $request->input('departmentToJoin');

            // Since rejected, make department column from pending to null
            Faculty::where('username', $username)->update([
                'department' => null
            ]);

            // Delete the row in the department pending joins
            $faculty = Faculty::where('username', $username)->first();
            $facultyID = $faculty->id;
            DepartmentPendingJoins::where('faculty_id', $facultyID)->delete();

            $items = DepartmentPendingJoins::with('getFacultyForeign')->orderBy('created_at', 'desc')->paginate(9);
            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->getFacultyForeign->username,
                    'fullname' => $item->getFacultyForeign->first_name . ' ' . $item->getFacultyForeign->middle_name . ' ' . $item->getFacultyForeign->last_name,
                    'departmentRequest' => $item->department_name,
                    'request_date_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            $facultyUsername = $faculty->username;
            $facultyFullName = ($faculty->first_name ? $faculty->first_name . ' ' : '') . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . ($faculty->last_name ? $faculty->last_name : '');

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has rejected a (' . $departmentToJoin . ') department join request from (' . $facultyUsername . ') ' . $facultyFullName . '.',
                'type_of_action' => 'Reject Department',
            ]);

            return response()->json($formattedTasks);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function adminRequestsDepartmentRejectAll(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $allData = $request->input('allData');

            foreach ($allData as $data) {
                $username = $data['username'];

                // Since rejected, make department column from pending to null
                Faculty::where('username', $username)->update([
                    'department' => null
                ]);
            }

            // Delete all rows in department pending requests
            DepartmentPendingJoins::truncate();

            $items = DepartmentPendingJoins::with('getFacultyForeign')->orderBy('created_at', 'desc')->paginate(9);
            $formattedTasks = $items->map(function ($item) {
                return [
                    'username' => $item->getFacultyForeign->username,
                    'fullname' => $item->getFacultyForeign->first_name . ' ' . $item->getFacultyForeign->middle_name . ' ' . $item->getFacultyForeign->last_name,
                    'departmentRequest' => $item->department_name,
                    'request_date_formatted' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'request_date_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            $admin = Auth::guard('admin')->user();
            $adminUsername = $admin->username;

            Logs::create([
                'user_id' => $admin->id,
                'user_role' => 'Admin',
                'action_made' => '(' . $adminUsername . ') has rejected all department join requests.',
                'type_of_action' => 'Reject All Department',
            ]);

            return response()->json($formattedTasks);
        } else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDashboardDepartmentTaskStatistics(Request $request) {
        if (Auth::guard('admin')->check()) {
            $departments = Departments::all();
           
            return view('admin.admin_dashboard_department_tasks', 
                        (['departments' => $departments]));
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDashboardDepartmentTaskGetStatistics(Request $request) {
        if (Auth::guard('admin')->check()) {
            $department = $request->input('department');
            $member = $request->input('member');

            $adminTaskTable = AdminTasks::where('faculty_name', $department)->first();
            if (!$adminTaskTable) {
                return response(['data' => 'null']);
            }

            $adminTasks = AdminTasks::where('faculty_name', $department)->pluck('id');
            if (!$adminTasks) {
                return response(['data' => 'null']);
            }

            if ($member === 'All Members') {
                $assigned = FacultyTasks::whereIn('task_id', $adminTasks)->count();
                
                $completed = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('status', 'Completed')
                            ->count();

                $late_completed = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('status', 'Late Completed')
                            ->count();

                $ongoing = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('status', 'Ongoing')
                            ->count();

                $missing = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('status', 'Missing')
                            ->count();
            }
            else {
                $assigned = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('submitted_by', $member)
                            ->count();
                
                $completed = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('submitted_by', $member)
                            ->where('status', 'Completed')
                            ->count();

                $late_completed = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('submitted_by', $member)
                            ->where('status', 'Late Completed')
                            ->count();

                $ongoing = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('submitted_by', $member)
                            ->where('status', 'Ongoing')
                            ->count();

                $missing = FacultyTasks::whereIn('task_id', $adminTasks)
                            ->where('submitted_by', $member)
                            ->where('status', 'Missing')
                            ->count();
            }
            $data = [$completed, $late_completed, $ongoing, $missing];
           
            return response([
                'data' => json_encode($data),
                'assigned' => $assigned,
                'completed' => $completed,
                'late_completed' => $late_completed,
                'ongoing' => $ongoing,
                'missing' => $missing,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDashboardDepartmentTaskTimeline(Request $request){
        if (Auth::guard('admin')->check()) {
            $departments = Departments::all();

            return view('admin.admin_dashboard_assigned_task_timeline', ['departments' => $departments]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminDashboardDepartmentTaskTimelineGetStatistics(Request $request){
        if (Auth::guard('admin')->check()) {
            $department = $request->input('department');
            $member = $request->input('member');

            $adminTasks = AdminTasks::where('faculty_name', $department)->pluck('id');
            if (!$adminTasks) {
                return response(['data' => json_encode([0, 0, 0, 0, 0, 0])]); // 0 for 6 months
            }

            $now = Carbon::now();
            $months = [];

            if ($department === 'All Departments') {
                for ($i = 5; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i);
                    $count = FacultyTasks::whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->count();
                    array_push($months, $count);
                }

                $total = array_sum($months);
           
                return response([
                    'data' => json_encode($months),
                    'total' => $total,
                ]);
            }

            /////////////////////////

            if ($member === 'All Members') {
                for ($i = 5; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i);
                    $count = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->count();
                    array_push($months, $count);
                }
            }
            else {
                for ($i = 5; $i >= 0; $i--) {
                    $month = $now->copy()->subMonths($i);
                    $count = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('submitted_by', $member)
                        ->whereMonth('created_at', $month->month)
                        ->whereYear('created_at', $month->year)
                        ->count();
                    array_push($months, $count);
                }
            }
            $total = array_sum($months);
           
            return response([
                'data' => json_encode($months),
                'total' => $total,
            ]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminLogs(Request $request){
        if (Auth::guard('admin')->check()) {
            $departments = Departments::all();

            $tasks = Logs::orderBy('created_at', 'desc')
                        ->paginate(20);

            return view('admin.admin_logs', ['tasks' => $tasks, 'departments' => $departments]);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminLogsSearch(Request $request){
        if (Auth::guard('admin')->check()) {
            $query = $request->input('query');

            if (empty($query)) {
                $items = Logs::orderBy('created_at', 'desc')
                        ->paginate(20);
            } 
            else {
                $items = Logs::where('action_made', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            }

            $formattedTasks = $items->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_role' => $item->user_role,
                    'action_made' => $item->action_made,
                    'type_of_action' => $item->type_of_action,
                    'date' => Carbon::parse($item->created_at)->format('F j, Y'),
                    'date_time' => Carbon::parse($item->created_at)->format('g:i A'),
                ];
            });

            return response()->json($formattedTasks);
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }

    function showAdminRanks(){
        if (Auth::guard('admin')->check()) {
            $faculties = FacultyTasks::all();
            $facultyNames = FacultyTasks::select('submitted_by')->distinct()->get();

            
        } 
        else if (Auth::guard('faculty')->check()) {
            return redirect('faculty-home');
        } 
        else {
            return redirect('login-admin')->with('fail', 'You must be logged in');
        }
    }
}
