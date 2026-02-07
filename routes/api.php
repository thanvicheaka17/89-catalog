<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\UserGamePlayController;
use App\Http\Controllers\Api\UserToolController;
use App\Http\Controllers\Api\UserAchievementController;
use App\Http\Controllers\Api\UserFriendController;
use App\Http\Controllers\Api\GamingStatsController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserDeviceController;
use App\Http\Controllers\Api\TwoFactorController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\TopToolController;
use App\Http\Controllers\Api\DemoGameController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\ToolRatingController;
use App\Http\Controllers\Api\RTPGameController;
use App\Http\Controllers\Api\CasinoController;
use App\Http\Controllers\Api\CasinoCategoryController;
use App\Http\Controllers\Api\ZonaPromaxHubController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotAndFreshController;
use App\Http\Controllers\Api\GlobalSearchController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\GameController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/


// Public registration endpoint



Route::middleware('api.key')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-login-2fa', [TwoFactorController::class, 'verifyLogin2FA']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/promo', [PromotionController::class, 'index']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/providers', [ProviderController::class, 'index']);
    Route::get('/rtp-promax-providers', [ProviderController::class, 'rtpProMaxProvider']);
    Route::get('/rtp-promax-plus-providers', [ProviderController::class, 'rtpProMaxPlusProvider']);
    Route::get('/demo-games', [DemoGameController::class, 'index']);
    Route::get('/demo-games/{slug}', [DemoGameController::class, 'show']);
    Route::get('/tool-categories', [TopToolController::class, 'toolCategories']);
    Route::get('/top-tools', [TopToolController::class, 'index']);
    Route::get('/top-tools/{slug}', [TopToolController::class, 'show']);
    Route::get('/top-tools/{slug}/ratings', [ToolRatingController::class, 'getToolRatings']);
    Route::get('/hot-and-fresh', [HotAndFreshController::class, 'index']);
    Route::get('/hot-and-fresh/{slug}', [HotAndFreshController::class, 'show']);
    Route::get('/testimonials', [TestimonialController::class, 'index']);
    Route::get('/rtp-games', [RTPGameController::class, 'index']);
    Route::get('/rtp-promax-plus-games', [RTPGameController::class, 'rtpPromaxPlusGames']);
    Route::get('/rtp-promax-games', [RTPGameController::class, 'rtpPromaxGames']);
    Route::get('/casino-categories', [CasinoCategoryController::class, 'index']);
    Route::get('/casinos', [CasinoController::class, 'index']);
    Route::post('/global-search', [GlobalSearchController::class, 'index']);
    Route::post('/newsletter-subscribers', [NewsletterSubscriberController::class, 'subscribe']);

    Route::get('/games', [GameController::class, 'index']);
});

Route::middleware(['auth:api', 'check.status'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('/update-account', [AuthController::class, 'updateAccount']);
    Route::get('/available-avatars', [AuthController::class, 'getAvailableAvatars']);
    Route::get('/login-notifications', [AuthController::class, 'loginNotifications']);

    // Trusted Devices
    Route::get('/trusted-devices', [UserDeviceController::class, 'index']);
    Route::post('/trusted-devices/revoke/{id}', [UserDeviceController::class, 'revoke']);
    Route::post('/trusted-devices/revoke-all', [UserDeviceController::class, 'revokeAllTrustedDevices']);

    // Two Factor and notification preferences
    Route::post('/user/2fa/enable', [TwoFactorController::class, 'enable2FA']);
    Route::post('/user/2fa/complete-setup', [TwoFactorController::class, 'complete2FASetup']);
    Route::post('/user/2fa/cancel-setup', [TwoFactorController::class, 'cancel2FASetup']);
    Route::post('/user/2fa/disable', [TwoFactorController::class, 'disable2FA']);
    Route::post('/user/notification-preferences', [AuthController::class, 'notificationPreferences']);

    // User Game Play
    Route::post('/game-play', [UserGamePlayController::class, 'store']);
    Route::get('/game-play', [UserGamePlayController::class, 'index']);

    // User Tools/Boosters
    Route::get('/user-tools', [UserToolController::class, 'index']);
    Route::post('/user-tools', [UserToolController::class, 'store']);
    Route::get('/user-tools/{id}', [UserToolController::class, 'show']);
    Route::put('/user-tools/{id}', [UserToolController::class, 'update']);
    Route::post('/user-tools/{id}/use', [UserToolController::class, 'useTool']);
    Route::delete('/user-tools/{id}', [UserToolController::class, 'destroy']);
    Route::get('/user-tools-stats', [UserToolController::class, 'stats']);

    // User Achievement
    Route::get('/achievement', [UserAchievementController::class, 'index']);
    Route::post('/achievement', [UserAchievementController::class, 'store']);

    // User Friend
    Route::get('/friend', [UserFriendController::class, 'index']);
    Route::post('/friend', [UserFriendController::class, 'store']);
    Route::post('/friend/accept', [UserFriendController::class, 'accept']);
    Route::post('/friend/reject', [UserFriendController::class, 'reject']);

    // Gaming Stats
    Route::get('/gaming-stats', [GamingStatsController::class, 'index']);

    // Events
    Route::get('/events', [EventController::class, 'index']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read', [NotificationController::class, 'read']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);


    // Tool Ratings
    Route::post('/top-tools/rating', [ToolRatingController::class, 'store']);
    Route::get('/my-ratings', [ToolRatingController::class, 'getUserRatings']);
    Route::get('/ratings/stats', [ToolRatingController::class, 'getRatingStats']);

    // Tools Boosters by Account Balance
    Route::get('/tools-boosters-by-account-balance', [TopToolController::class, 'allToolsBoostersByAccountBalance']);


    // ZONA PROMAX HUB Analytics
    Route::prefix('zona-promax-hub')->group(function () {
        Route::get('/stats', [ZonaPromaxHubController::class, 'getToolStats']);
        Route::get('/rtp-live-chart', [ZonaPromaxHubController::class, 'getRtpLiveChart']);
        Route::get('/pattern-analysis', [ZonaPromaxHubController::class, 'getPatternAnalysis']);
        Route::get('/provider-performance', [ZonaPromaxHubController::class, 'getProviderPerformance']);
        Route::get('/hot-times-schedule', [ZonaPromaxHubController::class, 'getHotTimesSchedule']);
        Route::get('/live-player-data', [ZonaPromaxHubController::class, 'getLivePlayerData']);
        Route::get('/providers', [ZonaPromaxHubController::class, 'getProviders']);
        Route::post('/refresh', [ZonaPromaxHubController::class, 'refreshData']);
    });
});

