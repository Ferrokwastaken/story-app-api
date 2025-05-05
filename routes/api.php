<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ModeratorController;
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

// All the routes protected by middleware, which belong to the moderators
Route::middleware(['api', 'auth:sanctum'])->prefix('moderator')->group(function () {
  Route::get('/home', [ModeratorController::class, 'home'])->name('api.moderator.home')->middleware('role:moderator');
  Route::get('/stories', [ModeratorController::class, 'indexStories'])->name('api.moderator.stories.index')->middleware('role:moderator');
  Route::put('/stories/{story}', [ModeratorController::class, 'updateStory'])->name('api.moderator.stories.update')->middleware('role:moderator');
  Route::delete('/stories/{story}', [ModeratorController::class, 'destroyStory'])->name('api.moderator.stories.destroy')->middleware('role:moderator');
  Route::get('/stories/{story}/pending-tags', [ModeratorController::class, 'indexPendingTags'])->name('api.moderator.stories.pending-tags')->middleware('role:moderator');
  Route::post('/stories/{story}/tags/{tag}/approve', [ModeratorController::class, 'approveTag'])->name('api.moderator.stories.tags.approve')->middleware('role:moderator');
  Route::delete('/stories/{story}/tags/{tag}/reject', [ModeratorController::class, 'rejectTag'])->name('api.moderator.stories.tags.reject')->middleware('role:moderator');
});

// These next 3 routes create the set of standard RESTful routes for stories, categories and tags.
// as in GET, POST, PUT and DELETE.
Route::apiResource('stories', StoryController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('tags', TagController::class);

// The next route manages comments related to a specific story. By using the
// shallow() method, it simplifies the URL by not requiring the story ID in the URLs for
// comment actions like store, update and destroy.
Route::resource('stories/{story}/comments', CommentController::class)->shallow();

// These routes handle reporting both a comment and a story
Route::post('comments/{comment}/report', [CommentController::class, 'report'])->name('comments.report');
Route::post('stories/{story}/report', [StoryController::class, 'report'])->name('stories.report');
Route::apiResource('reports', ReportController::class);

// Route for adding tags to stories
Route::post('/stories/{story}/tags', [StoryController::class, 'addTag']);

// Routes for both the logging in and out for moderators.
Route::post('/moderator/login', [AuthController::class, 'moderatorLogin'])->name('api.moderator.login');
Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout'])->name('api.logout');