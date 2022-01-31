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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');

    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    /**
     * Questions guarded API
     */
    Route::name('api.store.question')
        ->post('api/question/store', [\App\Http\Controllers\QuestionController::class, 'store']);

    Route::name('api.update.question')
        ->put('api/question/update/{question}', [\App\Http\Controllers\QuestionController::class, 'update']);

    Route::name('api.delete.question')
        ->delete('api/question/destroy/{question}', [\App\Http\Controllers\QuestionController::class, 'destroy']);
});


/**
 * Question Routes
 */

Route::name('api.get.questions')
    ->get('api/questions/index', [\App\Http\Controllers\QuestionController::class, 'index']);
