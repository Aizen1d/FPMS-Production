<?php

namespace App\Exports;

use App\Models\Faculty;
use App\Models\Seminars;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AdminSeminarsExport implements FromCollection, WithCustomStartCell, WithStrictNullComparison
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
            ['Trainings & Seminars'],
            ['Faculty', $this->memberFullName],
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
