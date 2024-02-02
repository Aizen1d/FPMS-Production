<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentPendingJoins extends Model
{
    use HasFactory;
    
    protected $table = 'department_pending_joins';

    public function getFacultyForeign() {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
