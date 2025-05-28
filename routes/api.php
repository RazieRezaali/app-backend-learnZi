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
        Route::get('/search', 'getCharactersBySearch')->name('characters.get.by.search');
        Route::get('/{characterId}', 'getCharacterDetail')->name('characters.get.details');
        Route::get('/id/{character}', 'getCharacterId')->name('characters.get.id');
        Route::get('/level/{hskLevel}', 'getCharactersByLevel')->name('characters.get.by.level');
});

Route::prefix('categories')
    ->middleware('auth:sanctum')
    ->controller(CategoryController::class)
    ->group(function(){
        Route::post('/', 'store')->name('category.store');
        Route::get('/', 'index')->name('get.categories');
        Route::get('/tree', 'getCategoryTree')->name('category.get.tree');
        Route::get('/root', 'getCategoryRoot')->name('category.get.root');
        Route::get('/cards/{categoryId}', 'getCategoryCards')->name('category.get.cards');
        Route::get('/quiz/{categoryId}', 'getCategoryQuizCharacters')->name('category.get.quiz.characters');
});

Route::prefix('cards')
    ->middleware('auth:sanctum')
    ->controller(CardController::class)
    ->group(function(){
        Route::post('/', 'store')->name('card.store');
        Route::get('/{cardId}', 'show')->name('card.show');
        Route::post('/description/{cardId}', 'storeDescription')->name('card.store.description');
        // Route::delete('/{card}', 'destroy')->name('card.destroy');
});

Route::post('user/register',[AuthController::class,'register']);
Route::post('user/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->post('user/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('user/profile',[AuthController::class,'getProfile']);

Route::get('/countries', [CountryController::class, 'index']);
