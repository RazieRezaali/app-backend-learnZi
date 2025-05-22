<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CharacterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('user/register',[AuthController::class,'register']);
Route::post('user/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->get('user/profile',[AuthController::class,'getProfile']);

Route::get('countries', [CountryController::class, 'index']);

Route::post('/card', [CardController::class, 'storeCard']);
Route::get('/card/{cardId}', [CardController::class, 'show']);
Route::delete('/card/{card}', [CardController::class, 'destroy']);
Route::get('/user-categories-cards/{userId}', [CategoryController::class, 'getUserCategoryTree']);
Route::post('/card-description/{card}', [CardController::class, 'description']);

Route::post('/category', [CategoryController::class, 'store']);
Route::get('/user-category/{userId}', [CategoryController::class, 'getUserCategories']);
Route::get('/root-categories/{userId}', [CategoryController::class, 'getRootCategories'])->name('get.root.categories');
Route::get('/cards-and-categories/{categoryId}', [CategoryController::class, 'getCardsAndCategories'])->name('get.cards.categories');
Route::middleware('auth:sanctum')->get('/test-categories', [CategoryController::class, 'getTestCategories']);
Route::middleware('auth:sanctum')->get('/test-characters/{categoryId}', [CategoryController::class, 'getTestCharacters']);

Route::get('/characters', [CharacterController::class, 'index']);
Route::get('/character/{character}', [CharacterController::class, 'getCharacterDetail']);
Route::get('/characters/search', [CharacterController::class, 'getCharactersBySearch']);
Route::get('/characters/{level}', [CharacterController::class, 'getCharactersByLevel']);