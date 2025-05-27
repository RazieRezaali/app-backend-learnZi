<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CharacterController;


Route::prefix('characters')
    ->controller(CharacterController::class)
    ->group(function(){
        Route::get('/', 'index')->name('get.characters');
        Route::get('/search', 'getCharactersBySearch')->name('get.characters.search');
        Route::get('/{character}', 'getCharacterDetail')->name('get.character.details');
        Route::get('/id/{character}', 'getCharacterId')->name('get.character.id');
        Route::get('/level/{level}', 'getCharactersByLevel')->name('get.characters.level');
});

Route::prefix('categories')
    ->middleware('auth:sanctum')
    ->controller(CategoryController::class)
    ->group(function(){
        Route::post('/', 'store')->name('store.category');
        Route::get('/', 'index')->name('get.categories');
        Route::get('/tree', 'getCategoryTree')->name('get.category.tree');
        Route::get('/root', 'getCategoryRoot')->name('get.category.root');
        Route::get('/cards/{categoryId}', 'getCategoryCards')->name('get.category.cards');
        Route::get('/quiz/{categoryId}', 'getCategoryQuizCharacters')->name('get.category.quiz.characters');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('user/register',[AuthController::class,'register']);
Route::post('user/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->post('user/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('user/profile',[AuthController::class,'getProfile']);

Route::get('countries', [CountryController::class, 'index']);

Route::post('/card', [CardController::class, 'storeCard']);
Route::get('/card/{cardId}', [CardController::class, 'show']);
Route::delete('/card/{card}', [CardController::class, 'destroy']);
Route::post('/card-description/{card}', [CardController::class, 'description']);
