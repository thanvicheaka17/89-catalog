<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SystemManagement\UsersController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PremiumToolController;
use App\Http\Controllers\Admin\GameManagement\DemoGameController;
use App\Http\Controllers\Admin\SystemManagement\ToolCategoryController;
use App\Http\Controllers\Admin\GameManagement\ToolController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\SystemManagement\SiteSettingController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SystemManagement\ProviderController;
use App\Http\Controllers\Admin\SystemManagement\RTPConfigurationController;
use App\Http\Controllers\Admin\SystemManagement\CasinoCategoryController;
use App\Http\Controllers\Admin\GameManagement\CasinoController;
use App\Http\Controllers\Admin\GameManagement\HotAndFreshController;
use App\Http\Controllers\Admin\SystemManagement\LevelConfigurationController;
use App\Http\Controllers\Admin\GameManagement\GameController;
use App\Http\Controllers\Admin\AuditLogController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', Authenticate::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // system management
    Route::resource('users', UsersController::class)->names('system-management.users');
    Route::resource('tool-categories', ToolCategoryController::class)->names('system-management.tool-categories');
    Route::resource('site-settings', SiteSettingController::class)->names('system-management.site-settings');
    Route::get('site-settings/test-avatar-upload', [SiteSettingController::class, 'testAvatarUpload'])->name('system-management.site-settings.test-avatar-upload');
    Route::resource('providers', ProviderController::class)->names('system-management.providers');
    Route::get('rtp-configurations', [RTPConfigurationController::class, 'index'])->name('system-management.rtp-configurations.index');
    Route::post('rtp-configurations/provider/{providerId}/sync', [RTPConfigurationController::class, 'syncGameData'])->name('system-management.rtp-configurations.sync-provider');
    Route::post('rtp-configurations/sync-all', [RTPConfigurationController::class, 'syncAllProviders'])->name('system-management.rtp-configurations.sync-all');
    Route::post('rtp-configurations/sync-all-progress', [RTPConfigurationController::class, 'syncAllProvidersProgress'])->name('system-management.rtp-configurations.sync-all-progress');
    Route::post('rtp-configurations/delete-all-data', [RTPConfigurationController::class, 'deleteAllData'])->name('system-management.rtp-configurations.delete-all-data');
    Route::get('rtp-configurations/global-settings', [RTPConfigurationController::class, 'getGlobalSettings'])->name('system-management.rtp-configurations.global-settings');
    Route::post('rtp-configurations/global-settings', [RTPConfigurationController::class, 'saveGlobalSettings'])->name('system-management.rtp-configurations.save-global-settings');
    Route::resource('casino-categories', CasinoCategoryController::class)->names('system-management.casino-categories');
    Route::resource('level-configurations', LevelConfigurationController::class)->names('system-management.level-configurations');
    Route::resource('audit-logs', AuditLogController::class)->names('system-management.audit-logs');


    // Promotions CRUD
    Route::resource('promotions', PromotionController::class)->names('promotions');
    Route::patch('promotions/{promotion}/toggle', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle');
    Route::post('promotions/{promotion}/duplicate', [PromotionController::class, 'duplicate'])->name('promotions.duplicate');

    // Featured Banners CRUD
    Route::resource('banners', BannerController::class)->names('banners');
    
    // Premium Tools CRUD
    Route::resource('premium-tools', PremiumToolController::class)->names('premium-tools');

    // Demo Games CRUD
    Route::resource('demo-games', DemoGameController::class)->names('game-management.demo-games');

    // Games CRUD
    Route::resource('games', GameController::class)->names('game-management.games');

    // tools CRUD
    // Place filter-settings routes BEFORE resource route to avoid route conflicts
    Route::get('tools/filter-settings', [ToolController::class, 'filterSettings'])->name('game-management.tools.filter-settings');
    Route::post('tools/filter-settings', [ToolController::class, 'saveFilterSettings'])->name('game-management.tools.save-filter-settings');
    Route::post('tools/update-order', [ToolController::class, 'updateOrder'])->name('game-management.tools.update-order');
    Route::resource('tools', ToolController::class)->names('game-management.tools');
    Route::post('tools/{tool}/move-up', [ToolController::class, 'moveUp'])->name('game-management.tools.move-up');
    Route::post('tools/{tool}/move-down', [ToolController::class, 'moveDown'])->name('game-management.tools.move-down');

    // Testimonials CRUD
    Route::resource('testimonials', TestimonialController::class)->names('testimonials');

    // Newsletter Subscribers CRUD
    Route::resource('newsletter-subscribers', NewsletterSubscriberController::class)->names('newsletter-subscribers');

    // Events CRUD
    Route::resource('events', EventController::class)->names('events');

    // RTP Games
    Route::get('rtp-games', [RTPConfigurationController::class, 'getRTPGames'])->name('rtp-games');

    // Casinos CRUD
    Route::resource('casinos', CasinoController::class)->names('game-management.casinos');

    // Hot and Fresh CRUD
    Route::resource('hot-and-fresh', HotAndFreshController::class)->names('game-management.hot-and-fresh');

    // Blog Posts CRUD
    Route::resource('blog-posts', \App\Http\Controllers\Admin\BlogPostController::class)->names('blog-posts');

    // Reviews CRUD
    // Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->names('reviews');
});
