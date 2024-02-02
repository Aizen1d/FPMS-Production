<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminTasks extends Model
{
    use HasFactory;

    protected $table = 'admin_tasks';

    public function FacultyTasks()
    {
        return $this->hasMany(FacultyTasks::class, 'task_id');
    }

    public function getFacultyTask()
    {
        return $this->belongsTo(FacultyTasks::class, 'task_id', 'task_id')
                    ->where('submitted_by_id', $this->submitted_by_id);
    }
}
