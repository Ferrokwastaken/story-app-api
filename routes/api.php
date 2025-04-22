<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// User authentication using Laravel Sanctum. Useful when eventually implementing user identification
// to moderate the site
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

// These next 3 routes create the set of standard RESTful routes for stories, categories and tags.
// as in GET, POST, PUT and DELETE.
Route::apiResource('stories', StoryController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('tags', TagController::class);

// The next route manages comments related to a specific story. By using the
// shallow() method, it simplifies the URL but not requiring the story ID in the URLs for
// comment actions like store, update and destroy.
Route::resource('stories/{story}/comments', CommentController::class)->shallow();

// These routes handle reporting both a comment and a story
Route::post('comments/{comment}/report', [CommentController::class, 'report'])->name('comments.report');
Route::post('stories/{story}/report', [StoryController::class, 'report'])->name('stories.report');
Route::apiResource('reports', ReportController::class);