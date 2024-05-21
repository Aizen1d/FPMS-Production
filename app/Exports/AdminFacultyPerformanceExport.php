<?php

namespace App\Exports;

use App\Models\Faculty;
use App\Models\AdminTasks;
use App\Models\FacultyTasks;
use App\Models\AdminTasksResearchesCompleted;
use App\Models\AdminTasksResearchesPresented;
use App\Models\AdminTasksResearchesPublished;
use App\Models\Attendance;
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
    
            return collect($allFacultyMemos);
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
    
            return collect($facultyMemos);
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
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);

                foreach (range('A', 'W') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
            },
        ];
    }
}
