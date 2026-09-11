<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommitteeController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\CoreValueController;
use App\Http\Controllers\Api\DirectoryMemberController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventRegistrationController;
use App\Http\Controllers\Api\ExecutiveMemberController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FirmController;
use App\Http\Controllers\Api\GalleryImageController;
use App\Http\Controllers\Api\JobListingController;
use App\Http\Controllers\Api\MemberCpdRecordController;
use App\Http\Controllers\Api\MembershipLevelController;
use App\Http\Controllers\Api\MemberSpotlightController;
use App\Http\Controllers\Api\MemberSubscriptionController;
use App\Http\Controllers\Api\NewsPostController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProgrammeEntryController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\ResourceItemController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\TrainingController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SiteSettingController::class, 'show']);
Route::get('/core-values', [CoreValueController::class, 'index']);
Route::get('/executives', [ExecutiveMemberController::class, 'index']);
Route::get('/past-chairpersons', [ExecutiveMemberController::class, 'pastChairpersons']);
Route::get('/sliders', [SliderController::class, 'index']);
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::get('/programme', [ProgrammeEntryController::class, 'index']);
Route::get('/committees', [CommitteeController::class, 'index']);
Route::get('/committees/{committee}', [CommitteeController::class, 'show']);
Route::get('/trainings', [TrainingController::class, 'index']);
Route::get('/directory/members', [DirectoryMemberController::class, 'index']);
Route::get('/directory/firms', [FirmController::class, 'index']);
Route::get('/jobs', [JobListingController::class, 'index']);
Route::get('/resources', [ResourceItemController::class, 'index']);
Route::get('/membership-levels', [MembershipLevelController::class, 'index']);

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

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

Route::get('/payments/verify/{reference}', [PaymentController::class, 'verify']);
Route::post('/payments/webhooks/paystack', [PaymentController::class, 'webhookPaystack']);
Route::post('/payments/webhooks/flutterwave', [PaymentController::class, 'webhookFlutterwave']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1');
    Route::get('/me/registrations', [AuthController::class, 'registrations']);
    Route::get('/me/cpd-records', [MemberCpdRecordController::class, 'index']);
    Route::get('/me/subscriptions', [MemberSubscriptionController::class, 'index']);
    Route::post('/me/subscriptions/{year}/pay', [PaymentController::class, 'paySubscriptionDues']);
    Route::post('/me/subscriptions/{year}/bank-transfer', [PaymentController::class, 'submitBankTransfer']);
});
