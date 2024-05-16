<?php

namespace App\Exports;

use App\Models\AdminTasks;
use App\Models\FacultyTasks;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AdminTasksExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStrictNullComparison
{
    protected $department;
    protected $member;

    public function __construct($department, $member)
    {
        $this->department = $department;
        $this->member = $member;
    }

    public function collection()
    {
        $adminTasks = AdminTasks::where('faculty_name', $this->department)->pluck('id');

        if ($this->member === 'All Members') {
            $assigned = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->count();

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
                        ->where('submitted_by', $this->member)
                        ->count();

            $completed = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('submitted_by', $this->member)
                        ->where('status', 'Completed')
                        ->count();

            $late_completed = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('submitted_by', $this->member)
                        ->where('status', 'Late Completed')
                        ->count();

            $ongoing = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('submitted_by', $this->member)
                        ->where('status', 'Ongoing')
                        ->count();

            $missing = FacultyTasks::whereIn('task_id', $adminTasks)
                        ->where('submitted_by', $this->member)
                        ->where('status', 'Missing')
                        ->count();
        }

        return collect([
            [
                'assigned' => $assigned,
                'completed' => $completed,
                'late_completed' => $late_completed,
                'ongoing' => $ongoing,
                'missing' => $missing
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            ['Department', $this->department],
            ['Faculty', $this->member],
            [],
            ['Assigned Memo', 'Completed Memo', 'Late Completed Memo', 'Ongoing Memo', 'Missing Memo']
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }
}