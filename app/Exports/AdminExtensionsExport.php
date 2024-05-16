<?php

namespace App\Exports;

use App\Models\Extension;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AdminExtensionsExport implements FromCollection, WithCustomStartCell, WithStrictNullComparison
{
    protected $extensionCount;
    protected $extensionLevelCount;
    protected $extensionTypeCount;
    protected $extensionFundingTypeCount;
    protected $extensionTotalHours;
    protected $extensionStatusCount;

    public function collection()
    {
        // Extension categories
        $extensionProgramCount = Extension::where('title_of_extension_program', '!=', '')->count();
        $extensionProjectCount = Extension::where('title_of_extension_project', '!=', '')->count();
        $extensionActivityCount = Extension::where('title_of_extension_activity', '!=', '')->count();
        $extensionCount = [$extensionProgramCount, $extensionProjectCount, $extensionActivityCount];

        // Extension levels
        $extensionLevelInternational = Extension::where('level', 'International')->count();
        $extensionLevelNational = Extension::where('level', 'National')->count();
        $extensionLevelRegional = Extension::where('level', 'Regional')->count();
        $extensionLevelProvincial = Extension::where('level', 'Provincial/City/Municipal')->count();
        $extensionLevelLocal = Extension::where('level', 'Local-PUP')->count();
        $extensionLevelCount = [$extensionLevelInternational, $extensionLevelNational, $extensionLevelRegional, $extensionLevelProvincial, $extensionLevelLocal];

        // Extension types
        $extensionTypeTraining = Extension::where('type', 'Training')->count();
        $extensionTypeTechnical = Extension::where('type', 'Technical/Advisory Services')->count();
        $extensionTypeOutreach = Extension::where('type', 'Outreach')->count();
        $extensionTypeCount = [$extensionTypeTraining, $extensionTypeTechnical, $extensionTypeOutreach];

        // Extension funding types
        $extensionFundingTypeUniversityFunded = Extension::where('type_of_funding', 'University Funded')->count();
        $extensionFundingTypeSelfFunded = Extension::where('type_of_funding', 'Self Funded')->count();
        $extensionFundingTypeExternallyFunded = Extension::where('type_of_funding', 'Externally Funded')->count();
        $extensionFundingTypeCount = [$extensionFundingTypeUniversityFunded, $extensionFundingTypeSelfFunded, $extensionFundingTypeExternallyFunded];

        // Identify, 1-10, 11-20, 21-30, 31-40, 41-50, 51 and above
        $extensionTotalHours0_10 = Extension::whereBetween('total_no_of_hours', [1, 10])->count();
        $extensionTotalHours11_20 = Extension::whereBetween('total_no_of_hours', [11, 20])->count();
        $extensionTotalHours21_30 = Extension::whereBetween('total_no_of_hours', [21, 30])->count();
        $extensionTotalHours31_40 = Extension::whereBetween('total_no_of_hours', [31, 40])->count();
        $extensionTotalHours41_50 = Extension::whereBetween('total_no_of_hours', [41, 50])->count();
        $extensionTotalHours51Above = Extension::where('total_no_of_hours', '>', 50)->count();
        $extensionTotalHours = [$extensionTotalHours0_10, $extensionTotalHours11_20, $extensionTotalHours21_30, $extensionTotalHours31_40, $extensionTotalHours41_50, $extensionTotalHours51Above];

        // Extension status
        $extensionStatusOngoing= Extension::where('status', 'Ongoing')->count();
        $extensionStatusCompleted = Extension::where('status', 'Completed')->count();
        $extensionStatusCount = [$extensionStatusOngoing, $extensionStatusCompleted];

        $this->extensionCount = [$extensionProgramCount, $extensionProjectCount, $extensionActivityCount];
        $this->extensionLevelCount = [$extensionLevelInternational, $extensionLevelNational, $extensionLevelRegional, $extensionLevelProvincial, $extensionLevelLocal];
        $this->extensionTypeCount = [$extensionTypeTraining, $extensionTypeTechnical, $extensionTypeOutreach];
        $this->extensionFundingTypeCount = [$extensionFundingTypeUniversityFunded, $extensionFundingTypeSelfFunded, $extensionFundingTypeExternallyFunded];
        $this->extensionTotalHours = [$extensionTotalHours0_10, $extensionTotalHours11_20, $extensionTotalHours21_30, $extensionTotalHours31_40, $extensionTotalHours41_50, $extensionTotalHours51Above];
        $this->extensionStatusCount = [$extensionStatusOngoing, $extensionStatusCompleted];

        return collect([
            ['Extension'],
            [''],
            ['Categories'],
            ['Program', 'Project', 'Activity'],
            $this->extensionCount,
            [''],
            ['Levels'],
            ['International', 'National', 'Regional', 'Provincial/City/Municipal', 'Local-PUP'],
            $this->extensionLevelCount,
            [''],
            ['Types'],
            ['Training', 'Technical/Advisory Services', 'Outreach'],
            $this->extensionTypeCount,
            [''],
            ['Funding Types'],
            ['University Funded', 'Self Funded', 'Externally Funded'],
            $this->extensionFundingTypeCount,
            [''],
            ['Total Hours'],
            ['1-10', '11-20', '21-30', '31-40', '41-50', '51 and above'],
            $this->extensionTotalHours,
            [''],
            ['Status'],
            ['Ongoing', 'Completed'],
            $this->extensionStatusCount,
        ]);
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
