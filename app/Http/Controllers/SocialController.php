<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookAuthenticate()
    {
        try{
            $user = Socialite::driver('facebook')->stateless()->user();

            $findUser = User::updateOrCreate([
                'provider_id' => $user->getId(),
            ], [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => encrypt('dummy'),
                'provider_id' => $user->getId(),
            ]);

            Auth::login($findUser);

            return redirect()->intended('home');


        } catch(\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function twitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function twitterAuthenticate()
    {
        try {
            $user = Socialite::driver('twitter')->user();

            $findUser = User::updateOrCreate([
                'provider_id' => $user->getId(),
            ], [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => encrypt('dummy'),
                'provider_id' => $user->getId(),
            ]);

            Auth::login($findUser);

            return redirect()->intended('home');
        }
        catch(\Exception $e) {

        }
    }
}
