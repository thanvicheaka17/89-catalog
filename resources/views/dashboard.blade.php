@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner" style="margin-bottom: 40px;">
    <div class="welcome-content">
        <h2 class="welcome-title">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! 🎉</h2>
        <p class="welcome-subtitle">Here's what's happening with your catalog today. Take a look at the key metrics and recent activity.</p>
    </div>
</div>

<!-- Dashboard Stats -->
<div class="stats-grid" style="margin-bottom: 40px; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
    <div class="stat-card amber">
        <div class="stat-header">
            <div class="stat-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['new_user_registers_today']) }}</div>
        <div class="stat-label">New Register Today</div>
    </div>
    
    <div class="stat-card emerald">
        <div class="stat-header">
            <div class="stat-icon emerald">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['active_users_today']) }}</div>
        <div class="stat-label">Active Today</div>
    </div>
    
    <div class="stat-card sky">
        <div class="stat-header">
            <div class="stat-icon sky">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['active_promotions']) }}</div>
        <div class="stat-label">Active Promotions</div>
    </div>
    
    <div class="stat-card indigo">
        <div class="stat-header">
            <div class="stat-icon indigo" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(129, 140, 248, 0.1)); color: #6366f1;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ number_format($stats['active_events']) }}</div>
        <div class="stat-label">Active Events</div>
    </div>
</div>

<!-- Dashboard Content Grid -->
<div class="content-grid" style="gap: 24px;">
    <!-- Recent Testimonials - Main Content -->
    <div class="card" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 4px 20px rgba(0, 0, 0, 0.04);">
        <div class="card-header" style="border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 class="card-title" style="font-size: 20px; margin-bottom: 4px;">Recent Testimonials</h3>
                    <p class="card-subtitle" style="margin-bottom: 0; font-size: 13px;">Latest {{ $recentTestimonials->count() }} testimonials from your users</p>
                </div>
                <a href="{{ route('testimonials.index') }}" class="btn btn-sm" style="text-decoration: none; padding: 8px 16px;">
                    View All
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-left: 6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
        <div class="card-body" style="padding: 24px;">
            @if($recentTestimonials->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($recentTestimonials as $testimonial)
                        <div style="padding: 18px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; transition: all 0.2s ease; position: relative; overflow: hidden; hover:border-color: #cbd5e1;">
                            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #8b5cf6, #a78bfa);"></div>
                            
                            <div style="display: flex; align-items: start; gap: 14px; margin-bottom: 14px;">
                                <div style="position: relative; flex-shrink: 0;">
                                    <img src="{{ $testimonial->getAvatarUrl() }}" alt="{{ $testimonial->user_name }}" style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid #f1f5f9;">
                                    @if($testimonial->is_featured)
                                        <div style="position: absolute; bottom: -2px; right: -2px; width: 22px; height: 22px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white" style="width: 11px; height: 11px;">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 3px 0; line-height: 1.3;">
                                                {{ $testimonial->user_name }}
                                            </h4>
                                            @if($testimonial->user_role)
                                                <p style="font-size: 12px; color: #64748b; margin: 0; font-weight: 500;">
                                                    {{ $testimonial->user_role }}
                                                </p>
                                            @endif
                                        </div>
                                        @if($testimonial->rating)
                                            <div style="display: flex; align-items: center; gap: 5px; background: #fef3c7; padding: 5px 10px; border-radius: 16px; border: 1px solid #fde68a;">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#f59e0b" style="width: 14px; height: 14px;">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span style="font-size: 13px; font-weight: 700; color: #92400e;">{{ $testimonial->rating }}.0</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($testimonial->message)
                                <div style="position: relative; padding-left: 20px; margin-bottom: 12px;">
                                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: linear-gradient(180deg, #8b5cf6, #a78bfa); border-radius: 2px;"></div>
                                    <p style="font-size: 13px; color: #334155; margin: 0; line-height: 1.6; font-style: italic;">
                                        "{{ Str::limit($testimonial->message, 180) }}"
                                    </p>
                                </div>
                            @endif
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #94a3b8;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 13px; height: 13px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $testimonial->created_at->diffForHumans() }}</span>
                                </div>
                                @if($testimonial->is_featured)
                                    <span class="badge" style="background: #fef3c7; color: #d97706; border-color: #fde68a; font-size: 10px; font-weight: 600; padding: 3px 8px;">
                                        ⭐ Featured
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 60px 32px; color: #94a3b8;">
                    <div style="width: 72px; height: 72px; margin: 0 auto 20px; background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(167, 139, 250, 0.08)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 36px; height: 36px; color: #8b5cf6; opacity: 0.6;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                        </svg>
                    </div>
                    <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 0 0 6px 0;">No testimonials yet</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 0 0 20px 0;">Start collecting feedback from your users</p>
                    <a href="{{ route('testimonials.create') }}" class="btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create Testimonial
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Quick Stats Card -->
        <div class="card" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 4px 20px rgba(0, 0, 0, 0.04);">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9;">
                <h3 class="card-title" style="margin-bottom: 0; font-size: 18px;">Quick Stats</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f0fdf4; border-radius: 10px; border: 1px solid #dcfce7;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #34d399); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($stats['featured_testimonials']) }}</div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px;">Featured</div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #fff1f2; border-radius: 10px; border: 1px solid #ffe4e6;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f43f5e, #fb7185); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($stats['active_promotions']) }}</div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px;">Promotions</div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #eef2ff; border-radius: 10px; border: 1px solid #e0e7ff;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #818cf8); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($stats['active_events']) }}</div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px;">Events</div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #ecfeff; border-radius: 10px; border: 1px solid #cffafe;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #06b6d4, #22d3ee); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v4.5H6v-4.5z" />
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($stats['published_blog_posts']) }}</div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 500; margin-top: 2px;">Blog Posts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 4px 20px rgba(0, 0, 0, 0.04);">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9;">
                <h3 class="card-title" style="margin-bottom: 0; font-size: 18px;">Quick Actions</h3>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('testimonials.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #faf5ff; border-radius: 10px; text-decoration: none; transition: all 0.2s ease; border: 1px solid #f3e8ff; hover:background: #f3e8ff; hover:border-color: #e9d5ff;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8b5cf6, #a78bfa); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 18px; height: 18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #0f172a;">Create Testimonial</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">Add a new testimonial</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; color: #94a3b8;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    
                    <a href="{{ route('testimonials.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #eff6ff; border-radius: 10px; text-decoration: none; transition: all 0.2s ease; border: 1px solid #dbeafe; hover:background: #dbeafe; hover:border-color: #bfdbfe;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0ea5e9, #38bdf8); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width: 18px; height: 18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #0f172a;">Manage All</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">View and edit testimonials</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; color: #94a3b8;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
