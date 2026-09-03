<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\CoreValueController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventRegistrationController;
use App\Http\Controllers\Api\ExecutiveMemberController;
use App\Http\Controllers\Api\GalleryImageController;
use App\Http\Controllers\Api\MemberSpotlightController;
use App\Http\Controllers\Api\NewsPostController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\SiteSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SiteSettingController::class, 'show']);
Route::get('/core-values', [CoreValueController::class, 'index']);
Route::get('/executives', [ExecutiveMemberController::class, 'index']);

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::post('/events/{event}/register', [EventRegistrationController::class, 'store']);

Route::get('/news', [NewsPostController::class, 'index']);
Route::get('/news/{newsPost}', [NewsPostController::class, 'show']);

Route::get('/gallery', [GalleryImageController::class, 'index']);
Route::get('/spotlights', [MemberSpotlightController::class, 'index']);
Route::get('/partners', [PartnerController::class, 'index']);
Route::get('/publications', [PublicationController::class, 'index']);

Route::post('/contact', [ContactMessageController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);
    Route::get('/me/registrations', [AuthController::class, 'registrations']);
});
