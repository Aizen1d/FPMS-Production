<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdminTasksResearchesCompleted;

class AdminTasksResearchesPresented extends Model
{
    use HasFactory;

    protected $table = 'admin_tasks_researches_presented';

    public function completedResearch()
    {
        return $this->belongsTo(AdminTasksResearchesCompleted::class, 'research_completed_id');
    }
}
