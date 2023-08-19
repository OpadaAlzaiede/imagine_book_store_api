<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request) {

        $user = $this->userService->store($request->validated());

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user->createToken(Config::get('app.api_secret_token'))->plainTextToken
        ], Config::get('app.messages.auth.register_success'));
    }

    public function login(LoginRequest $request) {

        if(!Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')]))
            return $this->error(403, Config::get('app.messages.auth.login_fail'));

        $user = auth()->user();

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user->createToken(Config::get('app.api_secret_token'))->plainTextToken
        ], Config::get('app.messages.auth.login_success'));
    }

    public function logout() {

        auth()->user()->tokens()->delete();
        return $this->success([], Config::get('app.messages.auth.logout_success'));
    }
}
