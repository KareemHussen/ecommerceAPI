<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\quickRegisterUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResetUserRequest;
use App\Models\User;
use App\Rules\PhoneValidation;
use App\Services\AuthService;
use App\Services\SocialiteService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{

    private SocialiteService $socialiteService;
    
    public function __construct()
    {
        $this->socialiteService = new SocialiteService();
    }


    public function loginWithGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();

        $user = $this->socialiteService->loginRegisterSocialiteUser($user->name , $user->email , $user->id , 0);

        $token = $user->createToken(env("SANCTUM_TOKEN"))->plainTextToken;

        return $this->respondOk([
            "token" => $token,
            "user" => $user
        ] , 'Login successfully');

    }


    public function loginWithFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function facebookCallback()
    {
        $user = Socialite::driver('facebook')->stateless()->user();

        $user = $this->socialiteService->loginRegisterSocialiteUser($user->name , $user->email , $user->id , 1);

        $token = $user->createToken(env("SANCTUM_TOKEN"))->plainTextToken;

        return $this->respondOk([
            "token" => $token,
            "user" => $user
        ] , 'Login successfully');

    }
}
    
