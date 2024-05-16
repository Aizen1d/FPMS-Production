<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AdminAttendanceExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStrictNullComparison
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
        $attendance = Attendance::where('faculty_id', $this->memberId)->get();

        $approvedAttendance = $attendance->where('status', 'Approved')->count();
        $rejectedAttendance = $attendance->where('status', 'Rejected')->count();
        $pendingAttendance = $attendance->where('status', 'Pending')->count();
        $totalAttendance = $attendance->count();

        return collect([
            [
                'Approved' => $approvedAttendance,
                'Rejected' => $rejectedAttendance,
                'Pending' => $pendingAttendance,
                'Total' => $totalAttendance,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            ['Faculty', $this->memberFullName],
            [],
            ['Attendance'],
            ['Approved Attendance', 'Rejected Attendance', 'Pending Attendance', 'Total Attendance'],
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
