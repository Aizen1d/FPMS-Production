<?php

namespace App\Exports;

use App\Models\Faculty;
use App\Models\AdminTasks;
use App\Models\FacultyTasks;
use App\Models\AdminTasksResearchesCompleted;
use App\Models\AdminTasksResearchesPresented;
use App\Models\AdminTasksResearchesPublished;
use App\Models\Attendance;
use App\Models\Functions;
use App\Models\Extension;
use App\Models\Seminars;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AdminFacultyPerformanceExport implements FromCollection, WithCustomStartCell, WithStrictNullComparison, WithEvents
{
    protected $memberId;
    protected $memberFullName;

    protected $facultyMemosCountRow;
    protected $facultyAttendanceCountRow;
    protected $facultyResearchesCompletedCountRow;
    protected $facultyResearchesPresentedCountRow;
    protected $facultyResearchesPublishedCountRow;
    protected $facultyResearchesCompletedTallyCountRow;
    protected $facultyResearchesPresentedTallyCountRow;
    protected $facultyResearchesPublishedTallyCountRow;
    protected $facultyExtensionsCountRow;
    protected $facultyExtensionsTallyCountRow;
    protected $facultySeminarsCountRow;
    protected $facultySeminarsTallyCountRow;

    protected $facultyMemosCountColumn;
    protected $facultyAttendanceCountColumn;
    protected $facultyResearchesCompletedCountColumn;
    protected $facultyResearchesPresentedCountColumn;
    protected $facultyResearchesPublishedCountColumn;
    protected $facultyResearchesCompletedTallyCountColumn;
    protected $facultyResearchesPresentedTallyCountColumn;
    protected $facultyResearchesPublishedTallyCountColumn;
    protected $facultyExtensionsCountColumn;
    protected $facultyExtensionsTallyCountColumn;
    protected $facultySeminarsCountColumn;
    protected $facultySeminarsTallyCountColumn;

    public function __construct($memberId, $memberFullName)
    {
        $this->memberId = $memberId;
        $this->memberFullName = $memberFullName;
    }

    public function collection()
    {
        $id = $this->memberId;
        $member = $this->memberFullName;

        $all_faculty = Faculty::all();
        $allMemo = AdminTasks::all();
        $allFunctions = Functions::all();
        $allCompletedResearches = AdminTasksResearchesCompleted::all();
        $allPresentedResearches = AdminTasksResearchesPresented::all();
        $allPublishedResearches = AdminTasksResearchesPublished::all();
        $allExtensions = Extension::all();
        $allSeminars = Seminars::all();
        
        if ($member == 'All Faculty') {
            $allFacultyMemos = $all_faculty->map(function ($faculty) use ($allMemo) {
                $faculty_memos = FacultyTasks::where('submitted_by', $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name)->get();
                $faculty_memos = $faculty_memos->map(function ($item) use ($allMemo) {
                    $task_name = $allMemo->where('id', $item->task_id)->first()->task_name;
                    $item['task_name'] = $task_name;
                    return $item;
                });
    
                // append full name of faculty
                $faculty['full_name'] = $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name;
    
                // Calculate totals
                $totalCompleted = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Completed'; })->count();
                $totalLateCompleted = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Late Completed'; })->count();
                $totalOngoing = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Ongoing'; })->count();
                $totalMissing = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Missing'; })->count();
                $overallTotal = $faculty_memos->count();
    
                // Create a row for the Excel file
                $row = [$faculty['full_name']];
                foreach ($allMemo as $memo) {
                    $facultyMemo = $faculty_memos->firstWhere('task_name', $memo->task_name);
                    $row[] = $facultyMemo ? $facultyMemo['status'] : 'Not Assigned';
                }
                $row = array_merge($row, [$totalCompleted, $totalLateCompleted, $totalOngoing, $totalMissing, $overallTotal]);
    
                return $row;
            });
    
            // Sort by full name
            $allFacultyMemos = $allFacultyMemos->sortBy(function ($facultyMemo) {
                return $facultyMemo[0]; // The first element of each row is the faculty's full name
            });
    
            // Convert the sorted collection back to an array
            $allFacultyMemos = $allFacultyMemos->values()->all();
    
            // Create the header row
            $headerRow = ['Faculty'];
            foreach ($allMemo as $memo) {
                $headerRow[] = $memo->task_name;
            }
            $headerRow = array_merge($headerRow, ['Completed', 'Late Completed', 'Ongoing', 'Missing', 'Overall']);
    
            // Add the header row to the beginning of the array
            array_unshift($allFacultyMemos, $headerRow);

            /*
            * Faculty Attendance
            */

            $allFacultyAttendance = $all_faculty->map(function ($faculty) use ($allFunctions) {
                $faculty_functions = Attendance::where('faculty_id', $faculty->id)->get();
            
                // Get function via function_id in the attendance table
                $faculty_functions = $faculty_functions->map(function ($item) use ($allFunctions) {
                    $function = Functions::where('id', $item->function_id)->first();
                    $item['brief_description'] = $function->brief_description;
                    return $item;
                });
            
                // append full name of faculty
                $faculty['full_name'] = $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name;
            
                // Calculate totals
                $totalAttended = $faculty_functions->filter(function ($func) { return $func['status_of_attendace'] === 'Attended' && $func['status'] === 'Approved'; })->count();
                $totalOnLeave = $faculty_functions->filter(function ($func) { return $func['status_of_attendace'] === 'On Leave' && $func['status'] === 'Approved'; })->count();
                $totalPending = $faculty_functions->filter(function ($func) { return $func['status'] === 'Pending'; })->count();
                $overallTotal = $allFunctions->count();
                $totalNotAttended = $overallTotal - ($totalAttended + $totalOnLeave + $totalPending);
            
                // Create a row for the Excel file
                $row = [$faculty['full_name']];
                foreach ($allFunctions as $func) {
                    $facultyFunction = $faculty_functions->firstWhere('brief_description', $func['brief_description']);
                    $status = 'Not Attended';
                    if ($facultyFunction) {
                        if ($facultyFunction['status_of_attendace'] === 'Attended' && $facultyFunction['status'] === 'Approved') {
                            $status = 'Attended';
                        } else if ($facultyFunction['status_of_attendace'] === 'On Leave' && $facultyFunction['status'] === 'Approved') {
                            $status = 'On Leave';
                        } else if ($facultyFunction['status'] === 'Pending') {
                            $status = 'Pending';
                        }
                    }
                    $row[] = $status;
                }
            
                // Add totals to row
                $row = array_merge($row, [$totalAttended, $totalOnLeave, $totalPending, $totalNotAttended, $overallTotal]);
            
                return $row;
            });

            // Sort by full name
            $allFacultyAttendance = $allFacultyAttendance->sortBy(function ($facultyAttendance) {
                return $facultyAttendance[0]; // The first element of each row is the faculty's full name
            });

            // Convert the sorted collection back to an array
            $allFacultyAttendance = $allFacultyAttendance->values()->all();
            
            // Create the header row
            $headerRow = ['Faculty'];
            foreach ($allFunctions as $func) {
                $headerRow[] = $func['brief_description'];
            }
            $headerRow = array_merge($headerRow, ['Attended', 'On Leave', 'Pending', 'Not Attended', 'Overall']);
            
            // Add the header row to the beginning of the array
            array_unshift($allFacultyAttendance, $headerRow);

            /*
            * Faculty Researches
            */

            // Completed researches
            $completedResearches = $allCompletedResearches->map(function ($research) {
                return [$research['title'], $research['authors']];
            })->toArray();

            if (count($completedResearches) === 0) {
                $completedResearches = [['No data', 'No data']];
            }
            array_unshift($completedResearches, ['Title', 'Authors']);

            // Presented researches
            $presentedResearches = $allPresentedResearches->map(function ($research) {
                // Get the title of the research from the completed researches table
                $completedResearch = AdminTasksResearchesCompleted::where('id', $research->research_completed_id)->first();
                return [$completedResearch->title, $completedResearch->authors];
            })->toArray();

            if (count($presentedResearches) === 0) {
                $presentedResearches = [['No data', 'No data']];
            }
            array_unshift($presentedResearches, ['Title', 'Authors']);

            // Published researches
            $publishedResearches = $allPublishedResearches->map(function ($research) {
                // Get the title of the research from the completed researches table
                $completedResearch = AdminTasksResearchesCompleted::where('id', $research->research_completed_id)->first();
                return [$completedResearch->title, $completedResearch->authors];
            })->toArray();

            if (count($publishedResearches) === 0) {
                $publishedResearches = [['No data', 'No data']];
            }
            array_unshift($publishedResearches, ['Title', 'Authors']);

            /*
            * Faculty Researches Tallies
            */

            // Completed researches tally
            $completedResearchesTally = $all_faculty->map(function ($faculty) use ($allCompletedResearches) {
                $faculty_researches = $allCompletedResearches->filter(function ($research) use ($faculty) {
                    return strpos($research->authors, $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name) !== false;
                });
                return [$faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name, $faculty_researches->count()];
            })->toArray();

            if (count($completedResearchesTally) === 0) {
                $completedResearchesTally = [['No data', 'No data']];
            }

            // Sort by full name
            $completedResearchesTally = collect($completedResearchesTally)->sortBy(function ($facultyResearch) {
                return $facultyResearch[0]; // The first element of each row is the faculty's full name
            })->values()->all();

            array_unshift($completedResearchesTally, ['Faculty', 'Total Completed Research']);

            // Presented researches tally
            $presentedResearchesTally = $all_faculty->map(function ($faculty) use ($allPresentedResearches) {
                $faculty_researches = $allPresentedResearches->filter(function ($research) use ($faculty) {
                    $completedResearch = AdminTasksResearchesCompleted::where('id', $research->research_completed_id)->first();
                    return strpos($completedResearch->authors, $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name) !== false;
                });
                return [$faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name, $faculty_researches->count()];
            })->toArray();

            if (count($presentedResearchesTally) === 0) {
                $presentedResearchesTally = [['No data', 'No data']];
            }

            // Sort by full name
            $presentedResearchesTally = collect($presentedResearchesTally)->sortBy(function ($facultyResearch) {
                return $facultyResearch[0]; // The first element of each row is the faculty's full name
            })->values()->all();
            
            array_unshift($presentedResearchesTally, ['Faculty', 'Total Presented Research']);

            // Published researches tally
            $publishedResearchesTally = $all_faculty->map(function ($faculty) use ($allPublishedResearches) {
                $faculty_researches = $allPublishedResearches->filter(function ($research) use ($faculty) {
                    $completedResearch = AdminTasksResearchesCompleted::where('id', $research->research_completed_id)->first();
                    return strpos($completedResearch->authors, $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name) !== false;
                });
                return [$faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name, $faculty_researches->count()];
            })->toArray();

            if (count($publishedResearchesTally) === 0) {
                $publishedResearchesTally = [['No data', 'No data']];
            }

            // Sort by full name
            $publishedResearchesTally = collect($publishedResearchesTally)->sortBy(function ($facultyResearch) {
                return $facultyResearch[0]; // The first element of each row is the faculty's full name
            })->values()->all();

            array_unshift($publishedResearchesTally, ['Faculty', 'Total Published Research']);

            /*
            All Extensions
            */

            // Return array of all extensions, headers are (Title, Type, Total no of hours, Faculty)
            $allExtensions = $allExtensions->map(function ($extension) use ($all_faculty) {
                $faculty = $all_faculty->where('id', $extension->faculty_id)->first();
                
                // Title could be (title_of_extension_activity, title_of_extension_program, title_of_extension_project) in the extension table
                $title = $extension->title_of_extension_activity ? $extension->title_of_extension_activity : ($extension->title_of_extension_program ? $extension->title_of_extension_program : $extension->title_of_extension_project);

                // Set type based on the title
                $type = $extension->title_of_extension_activity ? 'Activity' : ($extension->title_of_extension_program ? 'Program' : 'Project');

                return [$title, $type, $extension->total_no_of_hours, $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name];
            })->toArray();

            if (count($allExtensions) === 0) {
                $allExtensions = [['No data', 'No data', 'No data', 'No data']];
            }

            array_unshift($allExtensions, ['Title', 'Type', 'Total no of hours', 'Faculty']);

            /*
            Faculty Extension Tallies
            */

            // Get the faculty, then total extension count
            $getAllExtensions = Extension::all();

            $facultyExtensions = $all_faculty->map(function ($faculty) use ($getAllExtensions) {
                $faculty_extensions = $getAllExtensions->filter(function ($extension) use ($faculty) {
                    return $extension->faculty_id === $faculty->id;
                });
                return [$faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name, $faculty_extensions->count()];
            })->toArray();

            if (count($facultyExtensions) === 0) {
                $facultyExtensions = [['No data', 'No data']];
            }

            // Sort by full name
            $facultyExtensions = collect($facultyExtensions)->sortBy(function ($facultyExtension) {
                return $facultyExtension[0]; // The first element of each row is the faculty's full name
            })->values()->all();

            array_unshift($facultyExtensions, ['Faculty', 'Total Extension Created']);

            /*
            All Seminars
            */

            // Return array of all seminars, headers are (Title, Classification, Total no of hours, Faculty)
            $allSeminars = $allSeminars->map(function ($seminar) use ($all_faculty) {
                $faculty = $all_faculty->where('id', $seminar->faculty_id)->first();
                return [$seminar->title, $seminar->classification, $seminar->total_no_hours, $faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name];
            })->toArray();

            if (count($allSeminars) === 0) {
                $allSeminars = [['No data', 'No data', 'No data', 'No data']];
            }
            
            array_unshift($allSeminars, ['Title', 'Classification', 'Total no of hours', 'Faculty']);

            /*
            Faculty Seminar Tallies
            */

            // Get the faculty, then total seminar count
            $getAllSeminars = Seminars::all();

            $facultySeminars = $all_faculty->map(function ($faculty) use ($getAllSeminars) {
                $faculty_seminars = $getAllSeminars->filter(function ($seminar) use ($faculty) {
                    return $seminar->faculty_id === $faculty->id;
                });
                return [$faculty->first_name . ' ' . ($faculty->middle_name ? $faculty->middle_name . ' ' : '') . $faculty->last_name, $faculty_seminars->count()];
            })->toArray();

            if (count($facultySeminars) === 0) {
                $facultySeminars = [['No data', 'No data']];
            }

            // Sort by full name
            $facultySeminars = collect($facultySeminars)->sortBy(function ($facultySeminar) {
                return $facultySeminar[0]; // The first element of each row is the faculty's full name
            })->values()->all();

            array_unshift($facultySeminars, ['Faculty', 'Total Seminar Created']);

            $this->facultyMemosCountRow = $allFacultyMemos;
            $this->facultyAttendanceCountRow = $allFacultyAttendance;
            $this->facultyResearchesCompletedCountRow = $completedResearches;
            $this->facultyResearchesPresentedCountRow = $presentedResearches;
            $this->facultyResearchesPublishedCountRow = $publishedResearches;
            $this->facultyResearchesCompletedTallyCountRow = $completedResearchesTally;
            $this->facultyResearchesPresentedTallyCountRow = $presentedResearchesTally;
            $this->facultyResearchesPublishedTallyCountRow = $publishedResearchesTally;
            $this->facultyExtensionsCountRow = $allExtensions;
            $this->facultyExtensionsTallyCountRow = $facultyExtensions;
            $this->facultySeminarsCountRow = $allSeminars;
            $this->facultySeminarsTallyCountRow = $facultySeminars;

            $this->facultyMemosCountColumn = $allMemo->count() + 5; // 5 is the totals like Completed, Late Completed, Ongoing, Missing, Overall
            $this->facultyAttendanceCountColumn = $allFunctions->count() + 5;
            $this->facultyResearchesCompletedCountColumn = 1; // 2 is the title and authors
            $this->facultyResearchesPresentedCountColumn = 1; // 2 is the title and authors
            $this->facultyResearchesPublishedCountColumn = 1; // 2 is the title and authors
            $this->facultyResearchesCompletedTallyCountColumn = 1; // 2 is the faculty and total completed research
            $this->facultyResearchesPresentedTallyCountColumn = 1; // 2 is the faculty and total presented research
            $this->facultyResearchesPublishedTallyCountColumn = 1; // 2 is the faculty and total published research
            $this->facultyExtensionsCountColumn = 3; // 3 is the title, type, total no of hours
            $this->facultyExtensionsTallyCountColumn = 1; // 2 is the faculty and total extension created
            $this->facultySeminarsCountColumn = 3; // 3 is the title, classification, total no of hours
            $this->facultySeminarsTallyCountColumn = 1; // 2 is the faculty and total seminar created

            return collect([
                ['All Faculty Performance'],
                [''],
                ['Memo'],
                $allFacultyMemos,
                [''],
                [''],
                ['Attendance'],
                $allFacultyAttendance,
                [''],
                [''],
                ['Researches'],
                [''],
                ['Completed Researches'],
                $completedResearches,
                [''],
                ['Presented Researches'],
                $presentedResearches,
                [''],
                ['Published Researches'],
                $publishedResearches,
                [''],
                [''],
                ['Researches Tallies'],
                [''],
                ['Completed Tally'],
                $completedResearchesTally,
                [''],
                ['Presented Tally'],
                $presentedResearchesTally,
                [''],
                ['Published Tally'],
                $publishedResearchesTally,
                [''],
                [''],
                ['Extensions'],
                $allExtensions,
                [''],
                [''],
                ['Extension Tallies'],
                $facultyExtensions,
                [''],
                [''],
                ['Seminars'],
                $allSeminars,
                [''],
                [''],
                ['Seminar Tallies'],
                $facultySeminars,
            ]);
        }
        else {
            // Get the selected faculty
            $selectedFaculty = $member;
        
            // Get memos submitted by the selected faculty
            $faculty_memos = FacultyTasks::where('submitted_by', $selectedFaculty)->get();
            $faculty_memos = $faculty_memos->map(function ($item) use ($allMemo) {
                $task_name = $allMemo->where('id', $item->task_id)->first()->task_name;
                $item['task_name'] = $task_name;
                return $item;
            });
        
            // Calculate totals
            $totalCompleted = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Completed'; })->count();
            $totalLateCompleted = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Late Completed'; })->count();
            $totalOngoing = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Ongoing'; })->count();
            $totalMissing = $faculty_memos->filter(function ($memo) { return $memo['status'] === 'Missing'; })->count();
            $overallTotal = $faculty_memos->count();
        
            // Create a row for the Excel file
            $row = [$selectedFaculty];
            foreach ($allMemo as $memo) {
                $facultyMemo = $faculty_memos->firstWhere('task_name', $memo->task_name);
                $row[] = $facultyMemo ? $facultyMemo['status'] : 'Not Assigned';
            }
            $row = array_merge($row, [$totalCompleted, $totalLateCompleted, $totalOngoing, $totalMissing, $overallTotal]);
    
            // Create the header row
            $headerRow = ['Faculty'];
            foreach ($allMemo as $memo) {
                $headerRow[] = $memo->task_name;
            }
            $headerRow = array_merge($headerRow, ['Completed', 'Late Completed', 'Ongoing', 'Missing', 'Overall']);
    
            // Add the header row to the beginning of the array
            $facultyMemos = [$headerRow, $row];
    
            /*
            * Faculty Attendance
            */

            $faculty_functions = Attendance::where('faculty_id', $id)->get();

            // Get function via function_id in the attendance table
            $faculty_functions = $faculty_functions->map(function ($item) use ($allFunctions) {
                $function = Functions::where('id', $item->function_id)->first();
                $item['brief_description'] = $function->brief_description;
                return $item;
            });

            // Calculate totals
            $totalAttended = $faculty_functions->filter(function ($func) { return $func['status_of_attendace'] === 'Attended' && $func['status'] === 'Approved'; })->count();
            $totalOnLeave = $faculty_functions->filter(function ($func) { return $func['status_of_attendace'] === 'On Leave' && $func['status'] === 'Approved'; })->count();
            $totalPending = $faculty_functions->filter(function ($func) { return $func['status'] === 'Pending'; })->count();
            $overallTotal = $allFunctions->count();
            $totalNotAttended = $overallTotal - ($totalAttended + $totalOnLeave + $totalPending);

            // Create a row for the Excel file
            $row = [$selectedFaculty];
            foreach ($allFunctions as $func) {
                $facultyFunction = $faculty_functions->firstWhere('brief_description', $func['brief_description']);
                $status = 'Not Attended';
                if ($facultyFunction) {
                    if ($facultyFunction['status_of_attendace'] === 'Attended' && $facultyFunction['status'] === 'Approved') {
                        $status = 'Attended';
                    } else if ($facultyFunction['status_of_attendace'] === 'On Leave' && $facultyFunction['status'] === 'Approved') {
                        $status = 'On Leave';
                    } else if ($facultyFunction['status'] === 'Pending') {
                        $status = 'Pending';
                    }
                }
                $row[] = $status;
            }

            // Add totals to row
            $row = array_merge($row, [$totalAttended, $totalOnLeave, $totalPending, $totalNotAttended, $overallTotal]);

            // Create the header row
            $headerRow = ['Faculty'];
            foreach ($allFunctions as $func) {
                $headerRow[] = $func['brief_description'];
            }
            $headerRow = array_merge($headerRow, ['Attended', 'On Leave', 'Pending', 'Not Attended', 'Overall']);

            // Add the header row to the beginning of the array
            $facultyAttendance = [$headerRow, $row];

            /*
            * Faculty Researches
            */

            // Completed researches
            $completedResearches = $allCompletedResearches->map(function ($research) use ($member) {
                // check for the authors if %member% first before returning
                if (strpos($research->authors, $member) !== false) {
                    return [$research['title'], $research['authors']];
                }
            })->toArray();            

            if (count($completedResearches) === 0 || $completedResearches[0] === null) {
                $completedResearches = [['No data', 'No data']];
            }
            array_unshift($completedResearches, ['Title', 'Authors']);

            // Presented researches
            $presentedResearches = $allPresentedResearches->map(function ($research ) use ($member) {
                // check for the authors if %member% in the completed researches table before returning
                $completedResearch = AdminTasksResearchesCompleted::where('id', $research->research_completed_id)->first();
                if (strpos($completedResearch->authors, $member) !== false) {
                    return [$completedResearch->title, $completedResearch->authors];
                }
            })->toArray();

            if (count($presentedResearches) === 0 || $presentedResearches[0] === null) {
                $presentedResearches = [['No data', 'No data']];
            }
            array_unshift($presentedResearches, ['Title', 'Authors']);

            // Published researches
            $publishedResearches = $allPublishedResearches->map(function ($research) use ($member) {
                // check for the authors if %member% in the completed researches table before returning
                $completedResearch = AdminTasksResearchesCompleted::where('id', $research->research_completed_id)->first();
                if (strpos($completedResearch->authors, $member) !== false) {
                    return [$completedResearch->title, $completedResearch->authors];
                }
            })->toArray();

            if (count($publishedResearches) === 0 || $publishedResearches[0] === null) {
                $publishedResearches = [['No data', 'No data']];
            }
            array_unshift($publishedResearches, ['Title', 'Authors']);

            /*
            * Faculty Researches Tallies
            */

            // Completed researches tally for the selected faculty
            $completedResearchesTally = $allCompletedResearches->map(function ($research) use ($member) {
                $selectedFacultyResearches = AdminTasksResearchesCompleted::where('authors', 'like', '%' . $member . '%')->get();

                return [$member, $selectedFacultyResearches->count()];
            })->toArray();

            if (count($completedResearchesTally) === 0) {
                $completedResearchesTally = [[$member, 0]];
            }

            array_unshift($completedResearchesTally, ['Faculty', 'Total Completed Research']);

            // Presented researches tally for the selected faculty
            $presentedResearchesTally = $allPresentedResearches->map(function ($research) use ($member) {
                $publishedResearches = AdminTasksResearchesPresented::with('completedResearch')->whereHas('completedResearch', function ($query) use ($member) {
                    $query->where('authors', 'like', '%' . $member . '%');
                })->get();

                return [$member, $publishedResearches->count()];
            })->toArray();

            if (count($presentedResearchesTally) === 0) {
                $presentedResearchesTally = [[$member, 0]];
            }

            array_unshift($presentedResearchesTally, ['Faculty', 'Total Presented Research']);

            // Published researches tally for the selected faculty
            $publishedResearchesTally = $allPublishedResearches->map(function ($research) use ($member) {
                $publishedResearches = AdminTasksResearchesPublished::with('completedResearch')->whereHas('completedResearch', function ($query) use ($member) {
                    $query->where('authors', 'like', '%' . $member . '%');
                })->get();

                return [$member, $publishedResearches->count()];
            })->toArray();

            if (count($publishedResearchesTally) === 0) {
                $publishedResearchesTally = [[$member, 0]];
            }

            array_unshift($publishedResearchesTally, ['Faculty', 'Total Published Research']);

            /*
            Faculty Extensions
            */

            // Selected faculty's extensions
            $facultyExtensions = Extension::where('faculty_id', $id)->get();

            // Return array of selected faculty's extensions, headers are (Title, Type, Total no of hours, Faculty)
            $facultyExtensions = $facultyExtensions->map(function ($extension) use ($selectedFaculty) {
                // Title could be (title_of_extension_activity, title_of_extension_program, title_of_extension_project) in the extension table
                $title = $extension->title_of_extension_activity ? $extension->title_of_extension_activity : ($extension->title_of_extension_program ? $extension->title_of_extension_program : $extension->title_of_extension_project);

                // Set type based on the title
                $type = $extension->title_of_extension_activity ? 'Activity' : ($extension->title_of_extension_program ? 'Program' : 'Project');

                return [$title, $type, $extension->total_no_of_hours, $selectedFaculty];
            })->toArray();

            if (count($facultyExtensions) === 0 || $facultyExtensions[0] === null) {
                $facultyExtensions = [['No data', 'No data', 'No data', 'No data']];
            }

            array_unshift($facultyExtensions, ['Title', 'Type', 'Total no of hours', 'Faculty']);

            /*
            Faculty Extension Tallies
            */

            // Get the selected faculty's extensions
            $getFacultyExtensionsTally = Extension::where('faculty_id', $id)->get();
            $getFacultyExtensionsTally = [[$member, $getFacultyExtensionsTally->count()]]; 

            array_unshift($getFacultyExtensionsTally, ['Faculty', 'Total Extension Created']);

            /*
            Faculty Seminars
            */

            // Selected faculty's seminars
            $facultySeminars = Seminars::where('faculty_id', $id)->get();

            // Return array of selected faculty's seminars, headers are (Title, Classification, Total no of hours, Faculty)
            $facultySeminars = $facultySeminars->map(function ($seminar) use ($selectedFaculty) {
                return [$seminar->title, $seminar->classification, $seminar->total_no_hours, $selectedFaculty];
            })->toArray();

            if (count($facultySeminars) === 0 || $facultySeminars[0] === null) {
                $facultySeminars = [['No data', 'No data', 'No data', 'No data']];
            }

            array_unshift($facultySeminars, ['Title', 'Classification', 'Total no of hours', 'Faculty']);

            /*
            Faculty Seminar Tallies
            */

            // Get the selected faculty's seminars
            $getFacultySeminarsTally = Seminars::where('faculty_id', $id)->get();
            $getFacultySeminarsTally = [[$member, $getFacultySeminarsTally->count()]];

            array_unshift($getFacultySeminarsTally, ['Faculty', 'Total Seminar Created']);

            $this->facultyMemosCountRow = $faculty_memos;
            $this->facultyAttendanceCountRow = $faculty_functions;
            $this->facultyResearchesCompletedCountRow = $completedResearches;
            $this->facultyResearchesPresentedCountRow = $presentedResearches;
            $this->facultyResearchesPublishedCountRow = $publishedResearches;
            $this->facultyResearchesCompletedTallyCountRow = $completedResearchesTally;
            $this->facultyResearchesPresentedTallyCountRow = $presentedResearchesTally;
            $this->facultyResearchesPublishedTallyCountRow = $publishedResearchesTally;
            $this->facultyExtensionsCountRow = $facultyExtensions;
            $this->facultyExtensionsTallyCountRow = $getFacultyExtensionsTally;
            $this->facultySeminarsCountRow = $facultySeminars;
            $this->facultySeminarsTallyCountRow = $getFacultySeminarsTally;

            $this->facultyMemosCountColumn = $allMemo->count() + 5; // 5 is the totals like Completed, Late Completed, Ongoing, Missing, Overall
            $this->facultyAttendanceCountColumn = $allFunctions->count() + 5;
            $this->facultyResearchesCompletedCountColumn = 1; // 2 is the title and authors
            $this->facultyResearchesPresentedCountColumn = 1; // 2 is the title and authors
            $this->facultyResearchesPublishedCountColumn = 1; // 2 is the title and authors
            $this->facultyResearchesCompletedTallyCountColumn = 1; // 2 is the faculty and total completed research
            $this->facultyResearchesPresentedTallyCountColumn = 1; // 2 is the faculty and total presented research
            $this->facultyResearchesPublishedTallyCountColumn = 1; // 2 is the faculty and total published research
            $this->facultyExtensionsCountColumn = 3; // 3 is the title, type, total no of hours
            $this->facultyExtensionsTallyCountColumn = 1; // 2 is the faculty and total extension created
            $this->facultySeminarsCountColumn = 3; // 3 is the title, classification, total no of hours
            $this->facultySeminarsTallyCountColumn = 1; // 2 is the faculty and total seminar created

            return collect([
                [$selectedFaculty . ' Performance'],
                [''],
                ['Memo'],
                $facultyMemos,
                [''],
                [''],
                ['Attendance'],
                $facultyAttendance,
                [''],
                [''],
                ['Researches'],
                [''],
                ['Completed Researches'],
                $completedResearches,
                [''],
                ['Presented Researches'],
                $presentedResearches,
                [''],
                ['Published Researches'],
                $publishedResearches,
                [''],
                [''],
                ['Researches Tallies'],
                [''],
                ['Completed Tally'],
                $completedResearchesTally,
                [''],
                ['Presented Tally'],
                $presentedResearchesTally,
                [''],
                ['Published Tally'],
                $publishedResearchesTally,
                [''],
                [''],
                ['Extensions'],
                $facultyExtensions,
                [''],
                [''],
                ['Extension Tallies'],
                $getFacultyExtensionsTally,
                [''],
                [''],
                ['Seminars'],
                $facultySeminars,
                [''],
                [''],
                ['Seminar Tallies'],
                $getFacultySeminarsTally,
            ]);
        }
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $titleCell = 'A1';
                $event->sheet->getDelegate()->getStyle($titleCell)->getFont()->setBold(true)->setSize(16);

                // Auto size all columns
                foreach (range('A', 'Z') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }

                // Define the style
                $boldStyle = [
                    'font' => [
                        'bold' => true,
                    ],
                ];

                $increaseFontAndBoldStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ];

                $addBorderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ],
                ];

                // Calculate the start and end rows for each section
                $memoStartRow = 3;
                $memoEndRow = $memoStartRow + count($this->facultyMemosCountRow);
                $memoEndColumnLetter = chr(65 + $this->facultyMemosCountColumn);

                $attendanceStartRow = $memoEndRow + 3;
                $attendanceEndRow = $attendanceStartRow + count($this->facultyMemosCountRow);
                $attendanceEndColumnLetter = chr(65 + $this->facultyAttendanceCountColumn);

                $researchSectionTitleRow = $attendanceEndRow + 3; // Research

                $completedResearchTitleRow = $researchSectionTitleRow + 2; // Completed Research
                $completedResearcStartRow = $completedResearchTitleRow + 1; 
                $completedResearchEndRow = $completedResearcStartRow + count($this->facultyResearchesCompletedCountRow) - 1;
                $completedResearchEndColumnLetter = chr(65 + $this->facultyResearchesCompletedCountColumn);

                $presentedResearchTitleRow = $completedResearchEndRow + 2; // Presented Research
                $presentedResearchStartRow = $presentedResearchTitleRow + 1;
                $presentedResearchEndRow = $presentedResearchStartRow + count($this->facultyResearchesPresentedCountRow) - 1;
                $presentedResearchEndColumnLetter = chr(65 + $this->facultyResearchesPresentedCountColumn);

                $publishedResearchTitleRow = $presentedResearchEndRow + 2; // Published Research
                $publishedResearchStartRow = $publishedResearchTitleRow + 1;
                $publishedResearchEndRow = $publishedResearchStartRow + count($this->facultyResearchesPublishedCountRow) - 1;
                $publishedResearchEndColumnLetter = chr(65 + $this->facultyResearchesPublishedCountColumn);

                $researchesTalliesTitleRow = $publishedResearchEndRow + 3; // Researches Tallies

                $completedResearchesTallyTitleRow = $researchesTalliesTitleRow + 2; // Completed Tally
                $completedResearchesTallyStartRow = $completedResearchesTallyTitleRow + 1;
                $completedResearchesTallyEndRow = $completedResearchesTallyStartRow + count($this->facultyResearchesCompletedTallyCountRow) - 1;
                $completedResearchesTallyEndColumnLetter = chr(65 + $this->facultyResearchesCompletedTallyCountColumn);

                $presentedResearchesTallyTitleRow = $completedResearchesTallyEndRow + 2; // Presented Tally
                $presentedResearchesTallyStartRow = $presentedResearchesTallyTitleRow + 1;
                $presentedResearchesTallyEndRow = $presentedResearchesTallyStartRow + count($this->facultyResearchesPresentedTallyCountRow) - 1;
                $presentedResearchesTallyEndColumnLetter = chr(65 + $this->facultyResearchesPresentedTallyCountColumn);

                $publishedResearchesTallyTitleRow = $presentedResearchesTallyEndRow + 2; // Published Tally
                $publishedResearchesTallyStartRow = $publishedResearchesTallyTitleRow + 1;
                $publishedResearchesTallyEndRow = $publishedResearchesTallyStartRow + count($this->facultyResearchesPublishedTallyCountRow) - 1;
                $publishedResearchesTallyEndColumnLetter = chr(65 + $this->facultyResearchesPublishedTallyCountColumn);

                $extensionsSectionTitleRow = $publishedResearchesTallyEndRow + 3; // Extensions

                $extensionsStartRow = $extensionsSectionTitleRow + 1; // Extensions
                $extensionsEndRow = $extensionsStartRow + count($this->facultyExtensionsCountRow) - 1;
                $extensionsEndColumnLetter = chr(65 + $this->facultyExtensionsCountColumn);

                $extensionsTalliesTitleRow = $extensionsEndRow + 3; // Extensions Tallies

                $extensionsTalliesStartRow = $extensionsTalliesTitleRow + 1; // Extensions
                $extensionsTalliesEndRow = $extensionsTalliesStartRow + count($this->facultyExtensionsTallyCountRow) - 1;
                $extensionsTalliesEndColumnLetter = chr(65 + $this->facultyExtensionsTallyCountColumn);

                $seminarsSectionTitleRow = $extensionsTalliesEndRow + 3; // Seminars

                $seminarsStartRow = $seminarsSectionTitleRow + 1; // Seminars
                $seminarsEndRow = $seminarsStartRow + count($this->facultySeminarsCountRow) - 1;
                $seminarsEndColumnLetter = chr(65 + $this->facultySeminarsCountColumn);

                $seminarsTalliesTitleRow = $seminarsEndRow + 3; // Seminars Tallies
                
                $seminarsTalliesStartRow = $seminarsTalliesTitleRow + 1; // Seminars
                $seminarsTalliesEndRow = $seminarsTalliesStartRow + count($this->facultySeminarsTallyCountRow) - 1;
                $seminarsTalliesEndColumnLetter = chr(65 + $this->facultySeminarsTallyCountColumn);

                /*
                Apply bold and font increase to the section titles
                */
                $event->sheet->getDelegate()->getStyle('A' . $memoStartRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $attendanceStartRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $researchSectionTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $completedResearchTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $presentedResearchTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $publishedResearchTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $researchesTalliesTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $completedResearchesTallyTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $presentedResearchesTallyTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $publishedResearchesTallyTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $extensionsSectionTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $extensionsTalliesTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $seminarsSectionTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                $event->sheet->getDelegate()->getStyle('A' . $seminarsTalliesTitleRow)->applyFromArray($increaseFontAndBoldStyle);
                
                /*
                Apply bold to the headers
                */
                $event->sheet->getDelegate()->getStyle('A' . ($memoStartRow + 1) . ':Z' . ($memoStartRow + 1))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($attendanceStartRow + 1) . ':Z' . ($attendanceStartRow + 1))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($completedResearcStartRow) . ':Z' . ($completedResearcStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($presentedResearchStartRow) . ':Z' . ($presentedResearchStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($publishedResearchStartRow) . ':Z' . ($publishedResearchStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($completedResearchesTallyStartRow) . ':Z' . ($completedResearchesTallyStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($presentedResearchesTallyStartRow) . ':Z' . ($presentedResearchesTallyStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($publishedResearchesTallyStartRow) . ':Z' . ($publishedResearchesTallyStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($extensionsStartRow) . ':Z' . ($extensionsStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($extensionsTalliesStartRow) . ':Z' . ($extensionsTalliesStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($seminarsStartRow) . ':Z' . ($seminarsStartRow))->applyFromArray($boldStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($seminarsTalliesStartRow) . ':Z' . ($seminarsTalliesStartRow))->applyFromArray($boldStyle);
                
                /*
                Apply borders to per section tables
                */
                $event->sheet->getDelegate()->getStyle('A' . ($memoStartRow + 1) . ':' . $memoEndColumnLetter . $memoEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($attendanceStartRow + 1) . ':' . $attendanceEndColumnLetter . $attendanceEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($completedResearcStartRow) . ':' . $completedResearchEndColumnLetter . $completedResearchEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($presentedResearchStartRow) . ':' . $presentedResearchEndColumnLetter . $presentedResearchEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($publishedResearchStartRow) . ':' . $publishedResearchEndColumnLetter . $publishedResearchEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($completedResearchesTallyStartRow) . ':' . $completedResearchesTallyEndColumnLetter . $completedResearchesTallyEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($presentedResearchesTallyStartRow) . ':' . $presentedResearchesTallyEndColumnLetter . $presentedResearchesTallyEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($publishedResearchesTallyStartRow) . ':' . $publishedResearchesTallyEndColumnLetter . $publishedResearchesTallyEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($extensionsStartRow) . ':' . $extensionsEndColumnLetter . $extensionsEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($extensionsTalliesStartRow) . ':' . $extensionsTalliesEndColumnLetter . $extensionsTalliesEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($seminarsStartRow) . ':' . $seminarsEndColumnLetter . $seminarsEndRow)->applyFromArray($addBorderStyle);
                $event->sheet->getDelegate()->getStyle('A' . ($seminarsTalliesStartRow) . ':' . $seminarsTalliesEndColumnLetter . $seminarsTalliesEndRow)->applyFromArray($addBorderStyle);
            },
        ];
    }
}
