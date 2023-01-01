<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDO;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['username'] = strstr($data['email'],'@',true);
        $user = User::create($data);
        $token = $user->createToken(User::USER_TOKEN);

        return $this->success([
            'user' => $user,
            'token' => $token->plainTextToken
        ],"User has been registered successfully");
    }

    public function login(LoginRequest $request){
        $isValid = $this->isValidateCradintional($request);
        if(!$isValid['succeed']){
            return $this->error($isValid['message'],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user = $isValid['user'];
        $token = $user->createToken(User::USER_TOKEN);
        return $this->success(['user' => $user, 'token' => $token],'login successfuly');

    }

    private function isValidateCradintional(LoginRequest $request){
        $data = $request->validated();
        $user = User::where('email' , $data['email'])->first();
        if($user === null){
            return [
                'succeed' => false,
                'message' => 'invalid credentials'
            ];
        }
        if (Hash::chack($data['password'],$user->password)) {
            return [
                'succeed' => true,
                'user' => $user
            ];
        }
        return [
            'succeed' => false,
            'message' => 'invalid credentials'
        ];
    }

    public function loginWithToken(){
        return $this->success(auth()->user,'login successfully');
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        $this->success(null,'logout successfully');
    }
}
