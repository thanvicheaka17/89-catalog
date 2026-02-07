@extends('layouts.app')

@section('title', 'User Details')

@section('breadcrumb')
    <a href="{{ route('system-management.users.index') }}" class="breadcrumb-item">Users</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">User Details</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>User Details</h2>
            <p>View user details</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.users.edit', $user) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('system-management.users.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="detail-avatar-img">
            <div class="detail-header-info">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
            </div>
            {{-- <div class="detail-badges">
            <span class="badge role-{{ $user->role }}">{{ $user->getRoleDisplayName() }}</span>
            @if ($user->isActive())
                <span class="badge status-active">Active</span>
            @else
                <span class="badge status-inactive">Inactive</span>
            @endif
        </div> --}}
        </div>

        <div class="detail-body">
            <div class="detail-section">
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Full Name</label>
                        <span>{{ $user->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <span>{{ $user->email }}</span>
                    </div>

                    <div class="detail-item">
                        <label>Role</label>
                        <span class="badge role-{{ $user->role }}">{{ $user->getRoleDisplayName() }}</span>
                    </div>

                    <div class="detail-item">
                        <label>Status</label>
                        @if ($user->isActive())
                            <span class="badge status-active">Active</span>
                        @else
                            <span class="badge status-inactive">Inactive</span>
                        @endif
                    </div>

                    <div class="detail-item">
                        <label>Phone Number</label>
                        @if ($user->country_code && $user->phone_number)
                            <span>{{ $user->country_code }} {{ $user->phone_number }}</span>
                        @else
                            <span>N/A</span>
                        @endif
                    </div>

                    <div class="detail-item">
                        <label>Birth Date</label>
                        <span>{{ $user->birth_date ? $user->birth_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Protection Status</label>
                        @if ($user->isSystem())
                            <span class="badge badge-protected">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                                Protected
                            </span>
                        @else
                            <span class="badge text-blue bg-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                    </path>
                                </svg>
                                Standard user
                            </span>
                        @endif
                    </div>

                    <div class="detail-item">
                        <label>Two Factor Enabled</label>
                        <span
                            class="badge {{ $user->two_factor_enabled ? 'status-active' : 'status-inactive' }}">{{ $user->two_factor_enabled ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Login Notifications</label>
                        <span
                            class="badge {{ $user->login_notifications ? 'status-active' : 'status-inactive' }}">{{ $user->login_notifications ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Email Notifications</label>
                        <span
                            class="badge {{ $user->email_notifications ? 'status-active' : 'status-inactive' }}">{{ $user->email_notifications ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>SMS Notifications</label>
                        <span
                            class="badge {{ $user->sms_notifications ? 'status-active' : 'status-inactive' }}">{{ $user->sms_notifications ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Push Notifications</label>
                        <span
                            class="badge {{ $user->push_notifications ? 'status-active' : 'status-inactive' }}">{{ $user->push_notifications ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Language</label>
                        <span>{{ $user->language ? $user->language : 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Timezone</label>
                        <span>{{ $user->timezone ? $user->timezone : 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Location</label>
                        <span>{{ $user->location ? $user->location : 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Level</label>
                        <span>{{ $user->current_level ? $user->current_level : 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Tier</label>
                        <span class="badge tier-badge tier-bg-{{ strtolower($user->tier) }}">
                            {{ $user->tier }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Progress</label>
                        @php
                            $levelInfo = $user->getLevelInfo();
                            $isMaxLevel = $levelInfo['is_max_level'] ?? false;
                        @endphp
                        @if ($isMaxLevel)
                            <strong class="text-max-level">MAX</strong>
                        @else
                            @php
                                $progress = $levelInfo['progress_percentage'];
                                $colorClass = match (true) {
                                    $progress >= 90 => 'progress-excellent', // 90-99%
                                    $progress >= 75 => 'progress-very-good', // 75-89%
                                    $progress >= 50 => 'progress-good', // 50-74%
                                    $progress >= 25 => 'progress-fair', // 25-49%
                                    default => 'progress-poor', // 0-24%
                                };
                            @endphp
                            <strong class="progress-percentage {{ $colorClass }}">
                                {{ $progress }}%
                            </strong>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Devices -->
            @if ($devices->count() > 0)
                <div class="detail-section" style="padding-bottom:0px;">
                    <h4 class="detail-section-title">
                        Browser & Device
                    </h4>
                    @foreach ($devices as $index => $device)
                        <div class="device-card"
                            style="margin-bottom: 28px; padding: 15px; border: 1px solid var(--border); border-radius: 8px; background: linear-gradient(135deg,#f8fafc,#f1f5f9);">
                            <div class="device-header"
                                style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">
                                <h5
                                    style="font-size: 14px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Device {{ $index + 1 }}
                                    <span class="badge {{ $device->revoked ? 'status-inactive' : 'status-active' }}">
                                        {{ $device->getRevokedFormatted() }}
                                    </span>
                                </h5>
                            </div>

                            <div class="detail-grid form-grid-3">
                                <div class="detail-item">
                                    <label>Device Name</label>
                                    <span>{{ $parsedUserAgents[$index]['device'] ?? $device->device_name }}</span>
                                </div>

                                <div class="detail-item">
                                    <label>IP Address</label>
                                    <span>{{ $device->ip_address }}</span>
                                </div>

                                <div class="detail-item">
                                    <label>Browser</label>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @php
                                            $parsedUserAgent = $parsedUserAgents[$index] ?? [];
                                            $cdnBase = 'https://cdnjs.cloudflare.com/ajax/libs/browser-logos/70.1.0';
                                            $browserIcon = match ($parsedUserAgent['browser'] ?? '') {
                                                'Chrome' => "{$cdnBase}/chrome/chrome_512x512.png",
                                                'Firefox' => "{$cdnBase}/firefox/firefox_512x512.png",
                                                'Safari' => "{$cdnBase}/safari/safari_512x512.png",
                                                'Edge' => "{$cdnBase}/edge/edge_512x512.png",
                                                'Opera' => "{$cdnBase}/opera/opera_512x512.png",
                                                'Brave' => "{$cdnBase}/brave/brave_512x512.png",
                                                'Vivaldi' => "{$cdnBase}/vivaldi/vivaldi_512x512.png",
                                                'Samsung Browser'
                                                    => "{$cdnBase}/samsung-internet/samsung-internet_512x512.png",
                                                'UC Browser' => "{$cdnBase}/uc/uc_512x512.png",
                                                'IE'
                                                    => "{$cdnBase}/archive/internet-explorer_9-11/internet-explorer_9-11_512x512.png",
                                                'Postman' => 'https://cdn.simpleicons.org/postman/FF6C37',
                                                'Insomnia' => 'https://cdn.simpleicons.org/insomnia/4000BF',
                                                'curl' => 'https://cdn.simpleicons.org/curl/073551',
                                                default => null,
                                            };
                                        @endphp

                                        @if ($browserIcon)
                                            <img src="{{ $browserIcon }}"
                                                alt="{{ $parsedUserAgent['browser'] ?? 'Unknown' }}" loading="lazy"
                                                style="width: 20px; height: 20px; flex-shrink: 0;">
                                        @else
                                            <svg style="width: 16px; height: 16px; color: #9ca3af; flex-shrink: 0;"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <path
                                                    d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                            </svg>
                                        @endif

                                        <span
                                            style="font-size: 12px; font-weight: 500; color: #1e40af;">{{ $parsedUserAgent['browser_full'] ?? 'Unknown Browser' }}</span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <label>Operating System</label>
                                    <span
                                        class="badge text-blue bg-blue">{{ $parsedUserAgents[$index]['os'] ?? 'Unknown' }}
                                        {{ $parsedUserAgents[$index]['os_version'] ?? '' }}</span>
                                </div>

                                <div class="detail-item">
                                    <label>Last Active</label>
                                    <span class="datetime" data-iso="{{ $device->last_active_at->toIso8601String() }}">
                                        {{ $device->last_active_at->format('F d, Y \a\t h:i A') }}
                                    </span>
                                </div>

                                <div class="detail-item">
                                    <label>Device ID</label>
                                    <span class="uuid-cell">{{ $device->id }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Metadata
                </h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime" data-iso="{{ $user->created_at->toIso8601String() }}">
                            {{ $user->created_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $user->updated_at->toIso8601String() }}">
                            {{ $user->updated_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>User ID</label>
                        <span class="uuid-cell">{{ $user->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
