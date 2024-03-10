<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\SocialiteService;
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
        return $this->respondOk([
            "url" => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
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
        return $this->respondOk([
            "url" => Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl(),
        ]);
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
    
