<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Testimonial;
use App\Models\Promotion;
use App\Models\Event;
use App\Models\Casino;
use App\Models\Tool;
use App\Models\BlogPost;
use App\Models\NewsletterSubscriber;
use App\Models\DemoGame;
use App\Models\Banner;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $stats = [
            // User Stats
            'total_users' => User::count(),
            'new_user_registers_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_users_month' => User::whereMonth('created_at', now()->month)->count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            
            // Content Stats
            'total_testimonials' => Testimonial::count(),
            'featured_testimonials' => Testimonial::where('is_featured', true)->count(),
            'active_promotions' => Promotion::where('is_active', true)->count(),
            'total_promotions' => Promotion::count(),
            'active_events' => Event::where('is_active', true)->count(),
            'total_events' => Event::count(),
            'total_casinos' => Casino::count(),
            'total_tools' => Tool::count(),
            'total_blog_posts' => BlogPost::count(),
            'published_blog_posts' => BlogPost::where('is_published', true)->count(),
            'total_newsletter_subscribers' => NewsletterSubscriber::count(),
            'total_demo_games' => DemoGame::count(),
            'total_banners' => Banner::count(),
        ];

        // Get recent testimonials
        $recentTestimonials = Testimonial::orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard', compact(
            'stats',
            'recentTestimonials',
        ));
    }
}

