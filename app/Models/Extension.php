<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use HasFactory;

    protected $table = 'extension';

    public function getFaculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
}
