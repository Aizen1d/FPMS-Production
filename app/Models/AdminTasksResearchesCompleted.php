<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTasksResearchesCompleted extends Model
{
    use HasFactory;

    protected $table = 'admin_tasks_researches_completed';

    public function presentedResearch()
    {
        return $this->hasOne(AdminTasksResearchesPresented::class, 'research_completed_id');
    }
}
