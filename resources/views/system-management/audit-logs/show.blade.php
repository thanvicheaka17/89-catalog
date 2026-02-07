@extends('layouts.app')

@section('title', 'Audit Log Details')

@section('breadcrumb')
    <a href="{{ route('system-management.audit-logs.index') }}" class="breadcrumb-item">Audit Logs</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Details</span>
@endsection

@section('content')
    @php
        $parsedUserAgent = $auditLog->user_agent
            ? \App\Helpers\UserAgentParser::parse($auditLog->user_agent)->toArray()
            : null;

        $cdnBase = 'https://cdnjs.cloudflare.com/ajax/libs/browser-logos/70.1.0';
        $browserIcon = match ($parsedUserAgent['browser'] ?? '') {
            'Chrome' => "{$cdnBase}/chrome/chrome_512x512.png",
            'Firefox' => "{$cdnBase}/firefox/firefox_512x512.png",
            'Safari' => "{$cdnBase}/safari/safari_512x512.png",
            'Edge' => "{$cdnBase}/edge/edge_512x512.png",
            'Opera' => "{$cdnBase}/opera/opera_512x512.png",
            'Brave' => "{$cdnBase}/brave/brave_512x512.png",
            'Vivaldi' => "{$cdnBase}/vivaldi/vivaldi_512x512.png",
            'Samsung Browser' => "{$cdnBase}/samsung-internet/samsung-internet_512x512.png",
            'UC Browser' => "{$cdnBase}/uc/uc_512x512.png",
            'IE' => "{$cdnBase}/archive/internet-explorer_9-11/internet-explorer_9-11_512x512.png",
            'Postman' => 'https://cdn.simpleicons.org/postman/FF6C37',
            'Insomnia' => 'https://cdn.simpleicons.org/insomnia/4000BF',
            'curl' => 'https://cdn.simpleicons.org/curl/073551',
            default => null,
        };
    @endphp

    <div class="page-header">
        <div class="page-header-left">
            <h2>Audit Log Details</h2>
            <p>View audit log information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.audit-logs.index') }}" class="btn btn-secondary">
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
            <div class="banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor" width="24"
                    height="24">
                    <path
                        d="M168,152a8,8,0,0,1-8,8H96a8,8,0,0,1,0-16h64A8,8,0,0,1,168,152Zm-8-40H96a8,8,0,0,0,0,16h64a8,8,0,0,0,0-16Zm56-64V216a16,16,0,0,1-16,16H56a16,16,0,0,1-16-16V48A16,16,0,0,1,56,32H92.26a47.92,47.92,0,0,1,71.48,0H200A16,16,0,0,1,216,48ZM96,64h64a32,32,0,0,0-64,0ZM200,48H173.25A47.93,47.93,0,0,1,176,64v8a8,8,0,0,1-8,8H88a8,8,0,0,1-8-8V64a47.93,47.93,0,0,1,2.75-16H56V216H200Z" />
                </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ str_replace('_', ' ', ucfirst($auditLog->action_type ?? 'N/A')) }}</h3>
                <p>{{ $auditLog->new_value['user_name'] ?? 'N/A' }} - {{ $auditLog->new_value['user_email'] ?? '' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Basic Information
                </h4>
                <div class="detail-grid form-grid-4">
                    <div class="detail-item">
                        <label>Date/Time</label>
                        <span class="local-time" data-utc="{{ $auditLog->created_at->toIso8601String() }}">
                            {{ $auditLog->created_at->format('M d, Y H:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Created By</label>
                        <span>{{ $auditLog->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>To User</label>
                        <span>{{ $auditLog->new_value['user_name'] ?? 'N/A' }} -
                            {{ $auditLog->new_value['user_email'] ?? '' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Action Type</label>
                        <span
                            class="badge {{ $auditLog->action_type === 'add_funds' ? 'status-active' : 'text-blue bg-blue' }}">
                            {{ str_replace('_', ' ', ucfirst($auditLog->action_type ?? 'N/A')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Device & Browser Information -->
            @if ($parsedUserAgent)
                <div class="detail-section">
                    <h4 class="detail-section-title">
                        Device & Browser
                    </h4>
                    <div class="device-info-card">
                        <div class="detail-grid form-grid-3">
                            <div class="detail-item">
                                <label>Device</label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if ($parsedUserAgent['device'] === 'Desktop')
                                        <svg style="width: 18px; height: 18px; color: #3b82f6;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @elseif(in_array($parsedUserAgent['device'], ['iPhone', 'Mobile']))
                                        <svg style="width: 18px; height: 18px; color: #6b7280;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @elseif(in_array($parsedUserAgent['device'], ['iPad', 'Tablet']))
                                        <svg style="width: 18px; height: 18px; color: #6b7280;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @elseif($parsedUserAgent['device'] === 'API Client')
                                        <svg style="width: 18px; height: 18px; color: #8b5cf6;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg style="width: 18px; height: 18px; color: #9ca3af;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke-width="2" />
                                        </svg>
                                    @endif
                                    <span>{{ $parsedUserAgent['device'] ?? 'Unknown' }}</span>
                                </div>
                            </div>

                            <div class="detail-item">
                                <label>Browser</label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if ($browserIcon)
                                        <img src="{{ $browserIcon }}"
                                            alt="{{ $parsedUserAgent['browser'] ?? 'Unknown' }}" loading="lazy"
                                            style="width: 20px; height: 20px; flex-shrink: 0;">
                                    @else
                                        <svg style="width: 18px; height: 18px; color: #9ca3af;" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <path
                                                d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                        </svg>
                                    @endif
                                    <span>{{ $parsedUserAgent['browser_full'] ?? 'Unknown Browser' }}</span>
                                </div>
                            </div>

                            <div class="detail-item">
                                <label>Operating System</label>
                                <span class="badge text-blue bg-blue">{{ $parsedUserAgent['os_full'] ?? 'Unknown' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>IP Address</label>
                                <span class="uuid-cell">{{ $auditLog->ip_address ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <label>Location</label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px; color: #ef4444;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <span>{{ $auditLog->new_value['location'] ?? 'Unknown Location' }}</span>
                                </div>
                            </div>
                        </div>
                        @if ($auditLog->user_agent)
                            <div class="raw-user-agent">
                                <label>Raw User Agent</label>
                                <div class="user-agent-display">{{ $auditLog->user_agent }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="detail-section">
                    <h4 class="detail-section-title">
                        Device & Browser
                    </h4>
                    <div class="empty-state" style="padding: 1.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" style="width: 32px; height: 32px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3>No device information available</h3>
                    </div>
                </div>
            @endif

            <!-- Changes -->
            @if ($auditLog->old_value || $auditLog->new_value)
                @php
                    $excludeKeys = ['user_id', 'user_name', 'user_email', 'email', 'reason', 'total_accumulated_funds', 'location'];
                    $oldValues = is_array($auditLog->old_value) ? $auditLog->old_value : [];
                    $newValues = is_array($auditLog->new_value) ? $auditLog->new_value : [];
                    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                    $allKeys = array_filter($allKeys, fn($key) => !in_array(strtolower($key), $excludeKeys));
                @endphp
                <div class="detail-section">
                    <h4 class="detail-section-title">
                        Changes
                    </h4>
                    <div class="changes-comparison">
                        @foreach ($allKeys as $key)
                            <div class="comparison-row">
                                <div class="comparison-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                <div class="comparison-values">
                                    <div class="comparison-old">
                                        <div class="comparison-column-header">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span>Old</span>
                                        </div>
                                        <div class="comparison-value">
                                            @if (array_key_exists($key, $oldValues))
                                                @php $value = $oldValues[$key]; @endphp
                                                @if (is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @elseif(is_bool($value))
                                                    <span
                                                        class="badge {{ $value ? 'status-active' : 'status-inactive' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                                @elseif(is_null($value))
                                                    <span class="null-value">—</span>
                                                @elseif(is_numeric($value))
                                                    <span
                                                        class="number-value">{{ number_format($value, is_float($value) ? 2 : 0) }}</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            @else
                                                <span class="null-value">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="comparison-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" width="20" height="20">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </div>
                                    <div class="comparison-new">
                                        <div class="comparison-column-header">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span>New</span>
                                        </div>
                                        <div class="comparison-value">
                                            @if (array_key_exists($key, $newValues))
                                                @php $value = $newValues[$key]; @endphp
                                                @if (is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @elseif(is_bool($value))
                                                    <span
                                                        class="badge {{ $value ? 'status-active' : 'status-inactive' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                                @elseif(is_null($value))
                                                    <span class="null-value">—</span>
                                                @elseif(is_numeric($value))
                                                    <span
                                                        class="number-value">{{ number_format($value, is_float($value) ? 2 : 0) }}</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            @else
                                                <span class="null-value">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="detail-section">
                    <h4 class="detail-section-title">
                        Changes
                    </h4>
                    <div class="empty-state" style="padding: 2rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <h3>No changes recorded</h3>
                    </div>
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
                        <span class="datetime" data-iso="{{ $auditLog->created_at->toIso8601String() }}">
                            {{ $auditLog->created_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $auditLog->updated_at->toIso8601String() }}">
                            {{ $auditLog->updated_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Audit Log ID</label>
                        <span class="uuid-cell">{{ $auditLog->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .device-info-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
        }

        .raw-user-agent {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        .raw-user-agent label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .user-agent-display {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 0.8rem;
            color: #9ca3af;
            word-break: break-word;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            line-height: 1.5;
        }

        .changes-comparison {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .comparison-row {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-bottom: none;
            padding: 20px;
        }

        .comparison-row:first-child {
            border-radius: 8px 8px 0 0;
        }

        .comparison-row:last-child {
            border-bottom: 1px solid #e2e8f0;
            border-radius: 0 0 8px 8px;
        }

        .comparison-row:only-child {
            border-radius: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .comparison-label {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            text-transform: capitalize;
        }

        .comparison-values {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 16px;
            align-items: flex-start;
        }

        @media (max-width: 768px) {
            .comparison-values {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .comparison-arrow {
                transform: rotate(90deg);
                justify-self: center;
            }
        }

        .comparison-old,
        .comparison-new {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 16px;
            min-height: 60px;
        }

        .comparison-old {
            border-left: 3px solid #f87171;
        }

        .comparison-new {
            border-left: 3px solid #4ade80;
        }

        .comparison-column-header {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .comparison-old .comparison-column-header {
            color: #dc2626;
        }

        .comparison-new .comparison-column-header {
            color: #16a34a;
        }

        .comparison-value {
            font-size: 0.9rem;
            color: #374151;
            line-height: 1.5;
            word-break: break-word;
        }

        .comparison-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            padding-top: 28px;
        }

        .number-value {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            color: #1e40af;
            font-weight: 600;
        }

        .null-value {
            color: #9ca3af;
            font-style: italic;
        }

        .json-inline {
            margin: 0;
            padding: 8px 12px;
            background: #1f2937;
            border-radius: 4px;
            font-size: 0.75rem;
            color: #e5e7eb;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convert UTC timestamps to local time
            document.querySelectorAll('.local-time').forEach(function(element) {
                const utcDate = new Date(element.dataset.utc);
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                };
                element.textContent = utcDate.toLocaleDateString('en-US', options).replace(',', '');
            });
        });
    </script>
@endsection
