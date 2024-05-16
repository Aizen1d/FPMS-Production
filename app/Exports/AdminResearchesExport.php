<?php

namespace App\Exports;

use App\Models\AdminTasksResearchesCompleted;
use App\Models\AdminTasksResearchesPublished;
use App\Models\AdminTasksResearchesPresented;
use App\Models\Faculty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AdminResearchesExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStrictNullComparison
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
        $member = Faculty::find($this->memberId);

        // make a query to get the researches of the faculty member if in authors column
        $researchesCompleted = AdminTasksResearchesCompleted::where('authors', 'like', '%' . $member . '%')->count();
        
        // make a query to get the researches of the faculty member if in authors column, check in completed since it is the parent table
        $researchesPublished = AdminTasksResearchesPublished::with('completedResearch')->whereHas('completedResearch', function ($query) use ($member) {
            $query->where('authors', 'like', '%' . $member . '%');
        })->count();

        // make a query to get the researches of the faculty member if in authors column, check in presented since it is the parent table
        $researchesPresented = AdminTasksResearchesPresented::with('completedResearch')->whereHas('completedResearch', function ($query) use ($member) {
            $query->where('authors', 'like', '%' . $member . '%');
        })->count();
        
        $totalResearches = $researchesPresented + $researchesCompleted + $researchesPublished;

        return collect([
            [
                'Presented' => $researchesPresented,
                'Completed' => $researchesCompleted,
                'Published' => $researchesPublished,
                'Total' => $totalResearches,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            ['Faculty', $this->memberFullName],
            [],
            ['Researches'],
            ['Presented', 'Completed', 'Published', 'Total'],
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
