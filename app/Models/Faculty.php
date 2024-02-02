<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Faculty extends Model implements AuthenticatableContract
{
    use HasFactory, 
    Authenticatable;

    protected $fillable = [
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
    ];
}
