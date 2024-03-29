<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\PrivateUserResource;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    //
    public function action(RegisterRequest $request)
    {
        $user = User::create($request->only('email', 'name', 'password', 'phone_number'));

        return new PrivateUserResource($user);


    }
}
