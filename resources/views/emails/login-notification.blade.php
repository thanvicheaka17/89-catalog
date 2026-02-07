<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Login Notification - {{ config('app.name') }}</title>
    <style>
        /* Modern CSS Reset for Email */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        /* Layout Structure */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        /* Top Accent Bar */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
        }

        /* Content Sections */
        .header {
            padding: 10px 48px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .body {
            padding: 0 48px 40px;
            line-height: 1.6;
        }

        /* Typography */
        h1 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin: 0 0 16px;
        }

        p {
            font-size: 16px;
            color: #475569;
            margin: 0 0 20px;
        }

        /* Info Box */
        .info-box {
            background-color: #fff7ed;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border-left: 4px solid #ffe2bf;
        }

        .info-text {
            font-size: 14px;
            color: #ffa93e;
            margin: 0;
        }

        /* Login Details Table */
        .login-details {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #374151;
            min-width: 120px;
        }

        .detail-value {
            color: #6b7280;
        }

        /* Security Note */
        .security-note {
            background-color: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        /* Actions */
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 0 10px 10px 0;
        }

        .btn.secondary {
            background-color: #6b7280;
        }

        /* Footer */
        .footer {
            padding: 32px 48px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {

            .header,
            .body,
            .footer {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }

            .email-container {
                border-radius: 0;
                border: none;
            }
        }
    </style>
</head>

<body>
    <div style="background-color: #f8fafc; padding: 40px 0;">

        <div class="email-container">
            <div class="accent-bar"></div>

            <div class="header">
            </div>

            <div class="body">
                <h1>New Login Notification</h1>
                <p>Hello, <strong>{{ $user->name }}</strong></p>
                <p>We noticed a new login to your account. If this was you, you can safely ignore this email.</p>
                <p>If this was not you, please contact your administrator immediately.</p>

                <div class="login-details">
                    <h3 style="margin-top: 0; color: #1f2937;">Login Details</h3>

                    <div class="detail-row">
                        <div class="detail-label">Date & Time:</div>
                        <div class="detail-value datetime" data-iso="{{ $loginTime->toIso8601String() }}">{{ $loginTime->format('F j, Y \a\t g:i A T') }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">IP Address:</div>
                        <div class="detail-value">{{ $ip }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Location:</div>
                        <div class="detail-value">{{ $location }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Device:</div>
                        <div class="detail-value">{{ $deviceInfo['device'] }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Browser:</div>
                        <div class="detail-value">{{ $deviceInfo['browser'] }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Operating System: </div>
                        <div class="detail-value">{{ $deviceInfo['os'] }}</div>
                    </div>

                    @if ($userAgent !== 'Unknown')
                        <div class="detail-row">
                            <div class="detail-label">User Agent:</div>
                            <div class="detail-value">{{ $userAgent }}</div>
                        </div>
                    @endif
                </div>

                <div class="info-box">
                    <p class="info-text">
                        <strong>Security Note:</strong> If you did not log in at this time, your account may be
                        compromised. Please change your password immediately and contact our support team.
                    </p>
                </div>

                <p style="font-size: 14px; margin-top: 40px;">
                    Best regards,<br>
                    <strong>{{  \App\Models\SiteSetting::get('site_name', 'CLICKENGINE') }}</strong>
                </p>
            </div>

            <div class="footer">
                <p class="footer-text">
                    Sent to <strong>{{ $user->email }}</strong><br>
                    &copy; {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'CLICKENGINE') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', convertDatesToLocalTimezone);

        function convertDatesToLocalTimezone() {
            document.querySelectorAll('.datetime[data-iso]').forEach(function(el) {
                const iso = el.dataset.iso;
                if (iso && !el.dataset.converted) {
                    const d = new Date(iso);
                    if (!isNaN(d.getTime())) {
                        const date = d.toLocaleDateString('en-US', {
                            month: 'short',
                            day: '2-digit',
                            year: 'numeric'
                        });
                        const time = d.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            // second: '2-digit', 
                            hour12: true
                        });
                        el.textContent = date + ' at ' + time;
                        el.dataset.converted = 'true';
                    }
                }
            });
        }

        setTimeout(convertDatesToLocalTimezone, 100);
    </script>
</body>

</html>
