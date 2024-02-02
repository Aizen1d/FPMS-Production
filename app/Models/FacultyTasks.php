<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyTasks extends Model
{
    use HasFactory;

    protected $table = 'faculty_tasks';

    public function AdminTasks()
    {
        return $this->hasMany(AdminTasks::class, 'id');
    }
}
