<?php


namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function store($data) {

        $user = new User($data);
        $user->password = Hash::make($user->password);
        $user->save();

        return $user;
    }
}
