<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PythonController;
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
        Route::put('/{categoryId}', 'updateCategoryName')->name('category.update.name');
        Route::delete('/{categoryId}', 'destroy')->name('category.destroy');
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
        Route::delete('/{cardId}', 'destroy')->name('card.destroy');
});

Route::prefix('user')
    ->controller(AuthController::class)
    ->group(function(){
        Route::post('/register', 'register')->name('user.register');
        Route::post('/login', 'login')->name('user.login');
});

Route::prefix('user')
    ->middleware('auth:sanctum')
    ->controller(AuthController::class)
    ->group(function(){
        Route::post('/logout', 'logout')->name('user.logout');
        Route::get('/profile', 'getProfile')->name('user.get.profile');
        Route::put('/profile', 'updateProfile')->name('user.update.profile');
});

Route::get('/countries', [CountryController::class, 'index']);

Route::post('/ocr', [PythonController::class, 'ocr']);
Route::post('/audio', [PythonController::class, 'audio']);