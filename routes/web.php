<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/terms', function () {
    return view('welcome');
});

Route::get('/privacy', function () {
    return view('welcome');
});
// Reset password.
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::get('/auth-login/facebook', [App\Http\Controllers\SocialController::class, 'facebook'])->name('social.facebook');
Route::get('/authentication/facebook/', [App\Http\Controllers\SocialController::class, 'facebookAuthenticate'])->name('social.facebook.authenticate');
Route::get('/auth-login/twitter', [App\Http\Controllers\SocialController::class, 'twitter'])->name('social.twitter');
Route::get('/authenticate/twitter', [App\Http\Controllers\SocialController::class, 'twitterAuthenticate'])->name('social.twitter.authenticate');

Auth::routes();

Route::get('/otp-verify',  [App\Http\Controllers\VerifyController::class, 'showOtpForm'])->name('show.otp');
Route::post('/verify-otp',  [App\Http\Controllers\VerifyController::class, 'otp'])->name('otp');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('TwoFactorAuth');;
