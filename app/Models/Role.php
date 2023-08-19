<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getUserRole() {

        return 'user';
    }

    public static function getAdminRole() {

        return 'admin';
    }
}
