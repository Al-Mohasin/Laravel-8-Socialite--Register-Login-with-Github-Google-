<?php
#========   Socialite for Register & Login   ============
#Using account here::  Github, Google

/*******************************************************************************
            # At first we need API information & you have to make app
            # Then we take user data from Social Media
*******************************************************************************/
//==============================================================================
# Installation -- Command ( CMD )
# To get started with Socialite, use Composer to add the package to your project's dependencies:
composer require laravel/socialite

//==============================================================================
# Configaration
/* Before using Socialite, you will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your config/services.php configuration file, and should use the key facebook, twitter, linkedin, google, github, gitlab or bitbucket, depending on the providers your application requires. For example: */
//GO--> "config/services.php" & put it inside "return[]" bellow --->

#for Github
'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    // 'redirect' => 'http://your-callback-url',
    'redirect' => 'http://127.0.0.1:8000/login/github/callback',
],

#for Google
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    // 'redirect' => 'http://your-callback-url',
    'redirect' => 'http://127.0.0.1:8000/login/google/callback',
],

//==============================================================================
#GO--> ".env" file & Write Code like this

#for Github
GITHUB_CLIENT_ID=     //given from Github
GITHUB_CLIENT_SECRET= //given from Github

#for Google
GITHUB_CLIENT_ID=     //given from Google
GITHUB_CLIENT_SECRET= //given from Google

//==============================================================================
# make url link like this where you want to click
<a href="{{ url('login/github') }}"> Login or Register with Github</a>
<a href="{{ url('login/google') }}"> Login or Register with Google</a>

# where you want to show Session data
@if(Session::has("socialite_data"))
    <b>Register Success.</b> <br>
    Your password is: {{ Session::get("socialite_data")["generated_password"] }}
    <br> For Login, now you use this password.
@endif

//==============================================================================
# GO--> "routs/web.php" & put this route link

# for Github
Route::get('login/github', [App\Http\Controllers\GithubController::class, "redirectToProvider"]);
Route::get('/auth/callback', [App\Http\Controllers\GithubController::class, "handleProviderCallback"]);

# for Google
Route::get('login/google', [App\Http\Controllers\GoogleController::class, "redirectToProvider"]);
Route::get('/login/google/callback', [App\Http\Controllers\GoogleController::class, "handleProviderCallback"]);

//==============================================================================
# Controller Example...

# Controller Example here for Github
# (for Google-> all "github" name replace "google" use file GoogleController)
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Auth;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class GithubController extends Controller
{
    #------------------------------------------
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }
    #------------------------------------------
    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();

        $check_email = User::where("email", $user->getEmail())->count();

        if ($check_email != 1) {
            $generated_password = rand(100000, 999990);
            $insert = User::insert([
                "name"=>$user->getNickname(),
              # "name"=>$user->getName(),    //may for Google
                "email"=>$user->getEmail(),
                "role_id"=>5,
                "password"=>Hash::make($generated_password),
                "email_verified_at"=>Carbon::now(),
                "created_at"=>Carbon::now(),
            ]);

            if ($insert) {
                Session::put("socialite_data", [
                    "generated_password"=>$generated_password,
                    "taken_email"=>$user->getEmail(),
                ]);
                return redirect("login_register");
            } else {
                return back()->withUnsuccess("Register Failed ! Try again !");
            };
        } else {
            return redirect("login_register")->withUnsuccess("Your Social Email already been used ! You have to use another Email.");
        };
    }
}

# for Google Controller
# (for Google-> all "github" name replace "google" use file GoogleController)

#======  END  ======
