<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    public function getFunction()
    {
        return $this->belongsTo(Functions::class, 'function_id');
    }

    public function getFaculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
