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

class AdminSummaryExport implements FromCollection, WithCustomStartCell, WithStrictNullComparison
{
    protected $memberId;
    protected $memberFullName;

    protected $seminarCount;
    protected $seminarNatureCount;
    protected $seminarTypeCount;
    protected $seminarFundCount;
    protected $seminarLevelCount;

    public function __construct($memberId, $memberFullName)
    {
        $this->memberId = $memberId;
        $this->memberFullName = $memberFullName;
    }

    public function collection()
    {
        $id = $this->memberId;
        $member = $this->memberFullName;

        // Memo stuffs
        // Get All Tasks Data for the member, not count
        $allMemo = FacultyTasks::where('submitted_by', $member)
        ->get();

        // append in allMemo query the task_name from adminTasks table
        $allMemo = $allMemo->map(function ($item) {
        $task_name = AdminTasks::where('id', $item->task_id)->first()->task_name;
        $item['task_name'] = $task_name;
        return $item;
        });

        $completed = FacultyTasks::where('submitted_by', $member)
                ->where('status', 'Completed')
                ->count();

        $late_completed = FacultyTasks::where('submitted_by', $member)
                ->where('status', 'Late Completed')
                ->count();

        $ongoing = FacultyTasks::where('submitted_by', $member)
                ->where('status', 'Ongoing')
                ->count();

        $missing = FacultyTasks::where('submitted_by', $member)
                ->where('status', 'Missing')
                ->count();

        $Memodata = [$completed, $late_completed, $ongoing, $missing];

        // Researches stuffs

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

        // Attendance stuffs
        $attendance = Attendance::where('faculty_id', $this->memberId)->get();

        $approvedAttendance = $attendance->where('status', 'Approved')->count();
        $rejectedAttendance = $attendance->where('status', 'Rejected')->count();
        $pendingAttendance = $attendance->where('status', 'Pending')->count();
        $totalAttendance = $attendance->count();
            
        // Get the count of faculty classification where is seminar/webinar
        $seminarSeminarWebinarCount = Seminars::where('classification', 'Seminar/Webinar')->where('faculty_id', $id)->count();
        $seminarForaCount = Seminars::where('classification', 'Fora')->where('faculty_id', $id)->count();
        $seminarConference = Seminars::where('classification', 'Conference')->where('faculty_id', $id)->count();
        $seminarPlanning = Seminars::where('classification', 'Planning')->where('faculty_id', $id)->count();
        $seminarWorkshop = Seminars::where('classification', 'Workshop')->where('faculty_id', $id)->count();  
        $seminarProfessional = Seminars::where('classification', 'Professional/Continuing Professional Development')->where('faculty_id', $id)->count();
        $seminarShortTerm = Seminars::where('classification', 'Short Term Courses')->where('faculty_id', $id)->count();
        $seminarExecutive = Seminars::where('classification', 'Executive/Managerial')->where('faculty_id', $id)->count();
        //$seminarCount = [$seminarSeminarWebinarCount, $seminarForaCount, $seminarConference, $seminarPlanning, $seminarWorkshop, $seminarProfessional, $seminarShortTerm, $seminarExecutive];

        $seminarNatureGad = Seminars::where('nature', 'GAD-Related')->where('faculty_id', $id)->count();
        $seminarNatureInclusivity = Seminars::where('nature', 'Inclusivity and Diversity')->where('faculty_id', $id)->count();
        $seminarNatureProfessional = Seminars::where('nature', 'Professional')->where('faculty_id', $id)->count();
        $seminarNatureSkills = Seminars::where('nature', 'Skills/Technical')->where('faculty_id', $id)->count();
        //$seminarNatureCount = [$seminarNatureGad, $seminarNatureInclusivity, $seminarNatureProfessional, $seminarNatureSkills];

        $seminarTypeExecutive = Seminars::where('type', 'Executive/Managerial')->where('faculty_id', $id)->count();
        $seminarTypeFoundation = Seminars::where('type', 'Foundation')->where('faculty_id', $id)->count();
        $seminarTypeSupervisory = Seminars::where('type', 'Supervisory')->where('faculty_id', $id)->count();
        $seminarTypeTechnical = Seminars::where('type', 'Technical')->where('faculty_id', $id)->count();
        //$seminarTypeCount = [$seminarTypeExecutive, $seminarTypeFoundation, $seminarTypeSupervisory, $seminarTypeTechnical];

        $seminarFundUniversity = Seminars::where('source_of_fund', 'University Funded')->where('faculty_id', $id)->count();
        $seminarFundSelfFunded = Seminars::where('source_of_fund', 'Self-Funded')->where('faculty_id', $id)->count();
        $seminarFundExternally = Seminars::where('source_of_fund', 'Externally-Funded')->where('faculty_id', $id)->count();
        $seminarFundNot = Seminars::where('source_of_fund', 'Not a Paid Seminar/Training')->where('faculty_id', $id)->count();
        //$seminarFundCount = [$seminarFundUniversity, $seminarFundSelfFunded, $seminarFundExternally, $seminarFundNot];

        $seminarLevelInternational = Seminars::where('level', 'International')->where('faculty_id', $id)->count();
        $seminarLevelNational = Seminars::where('level', 'National')->where('faculty_id', $id)->count();
        $seminarLevelLocal = Seminars::where('level', 'Local')->where('faculty_id', $id)->count();
        //$seminarLevelCount = [$seminarLevelInternational, $seminarLevelNational, $seminarLevelLocal];

        $this->seminarCount = [$seminarSeminarWebinarCount, $seminarForaCount, $seminarConference, $seminarPlanning, $seminarWorkshop, $seminarProfessional, $seminarShortTerm, $seminarExecutive];
        $this->seminarNatureCount = [$seminarNatureGad, $seminarNatureInclusivity, $seminarNatureProfessional, $seminarNatureSkills];
        $this->seminarTypeCount = [$seminarTypeExecutive, $seminarTypeFoundation, $seminarTypeSupervisory, $seminarTypeTechnical];
        $this->seminarFundCount = [$seminarFundUniversity, $seminarFundSelfFunded, $seminarFundExternally, $seminarFundNot];
        $this->seminarLevelCount = [$seminarLevelInternational, $seminarLevelNational, $seminarLevelLocal];

        return collect([
            ['Accomplishments Summary'],
            ['Faculty', $this->memberFullName],
            [''],
            ['Memo'],
            [''],
            ['Completed', 'Late Completed', 'Ongoing', 'Missing'],
            $Memodata,
            [''],
            [''],
            [''],
            ['Researches'],
            [''],
            ['Presented', 'Completed', 'Published', 'Total'],
            [$researchesPresented, $researchesCompleted, $researchesPublished, $totalResearches],
            [''],
            [''],
            [''],
            ['Attendance'],
            [''],
            ['Approved Attendance', 'Rejected Attendance', 'Pending Attendance', 'Total Attendance'],
            [$approvedAttendance, $rejectedAttendance, $pendingAttendance, $totalAttendance],
            [''],
            [''],
            [''],
            ['Training & Seminars'],
            [''],
            ['Classification'],
            ['Seminar/Webinar', 'Fora', 'Conference', 'Planning', 'Workshop', 'Professional/Continuing Professional Development', 'Short Term Courses', 'Executive/Managerial'],
            $this->seminarCount,
            [''],
            ['Nature'],
            ['GAD-Related', 'Inclusivity and Diversity', 'Professional', 'Skills/Technical'],
            $this->seminarNatureCount,
            [''],
            ['Type'],
            ['Executive/Managerial', 'Foundation', 'Supervisory', 'Technical'],
            $this->seminarTypeCount,
            [''],
            ['Source of Fund'],
            ['University Funded', 'Self-Funded', 'Externally-Funded', 'Not a Paid Seminar/Training'],
            $this->seminarFundCount,
            [''],
            ['Level'],
            ['International', 'National', 'Local'],
            $this->seminarLevelCount,
        ]); 
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
