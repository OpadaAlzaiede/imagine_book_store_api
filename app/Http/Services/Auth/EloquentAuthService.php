<?php

namespace App\Http\Services\Auth;


use Illuminate\Support\Facades\Auth;

class EloquentAuthService implements AuthService {

    public function getAuthUser() {

        return Auth::user();
    }

    public function getAuthId() {

        return Auth::id();
    }
}
