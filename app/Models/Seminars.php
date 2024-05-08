<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seminars extends Model
{
    use HasFactory;

    protected $table = 'seminars';

    public function getFaculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
