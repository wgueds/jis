<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserLoginRequest;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(StoreUserRequest $request)
    {
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->save();

            return $this->sucess(['success'], null, 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, null, $e);
        }
    }

    public function login(UserLoginRequest $request)
    {
        try {
            if (!auth()->attempt($request->all()))
                return $this->error('Credentials not match', 401);

            /** @var User $user */
            $user = auth()->user();

            // Revoke the token that was used to authenticate
            $currentAccessToken = $request->user()->currentAccessToken();

            if ($currentAccessToken) {
                $currentAccessToken->delete();
            }

            return $this->sucess([
                'user' => $user,
                'token' => $user->createToken("CLIENT-{$user->id}")->plainTextToken
            ]);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, null, $e);
        }
    }
}
