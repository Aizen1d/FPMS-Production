<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'user_role', 'action_made', 'type_of_action'];
    protected $table = 'logs';
}
