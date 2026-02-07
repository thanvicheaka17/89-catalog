<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Password Reset</title>
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

        /* Call to Action */
        .cta-container {
            margin: 32px 0;
            text-align: center;
        }

        .button {
            background-color: #0f172a;
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg,#f59e0b1a,#fbbf241a);
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border-left: 4px solid #f59e0b;
        }

        .info-text {
            font-size: 14px;
            color: #f59e0b;
            margin: 0;
        }

        /* Footer Troubleshooting Section */
        .troubleshooting {
            background-color: #ffffff;
            padding: 24px 48px;
            border-top: 1px solid #f1f5f9;
        }

        .url-text {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #2563eb;
            word-break: break-all;
            background: #f8fafc;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            display: block;
            margin-top: 8px;
            text-decoration: none;
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

        .social-link {
            color: #64748b;
            text-decoration: none;
            margin: 0 8px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .header, .body, .footer, .troubleshooting {
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

            <div class="header"></div>

            <div class="body">
                <h1>Reset Your Password</h1>
                <p>Hello, <strong>{{ $name }}</strong></p>
                <p>We received a request to access your account. To proceed with resetting your password, please click the button below. For your security, this request is only valid for a limited time.</p>
                
                <div class="cta-container">
                    @php
                        $frontendUrl = \App\Models\SiteSetting::where('key', 'frontend_url')->first()->value
                            ?? config('app.frontend_url')
                            ?? config('app.url');
                    @endphp
                    <a href="{{ $frontendUrl }}reset-password?token={!! $token !!}&email={{ urlencode($email) }}" class="button">Reset My Password</a>
                </div>

                <div class="info-box">
                    <p class="info-text">
                        <strong>Security Note:</strong> This link will expire in 60 minutes. If you didn't request this, no further action is required; your account remains secure.
                    </p>
                </div>

                <p style="font-size: 14px; margin-top: 40px;">
                    Best regards,<br>
                    <strong>{{ \App\Models\SiteSetting::get('site_name', 'CLICKENGINE') }}</strong>
                </p>
            </div>

            <!-- Full Link Troubleshooting Section -->
            {{-- <div class="troubleshooting">
                <p style="font-size: 12px; color: #94a3b8; margin-bottom: 8px;">If the button above doesn't work, copy and paste this URL into your browser:</p>
                <a href="{{ config('app.app_url') }}/auth/reset-password?token={{ $token }}&email={{ $email }}" class="url-text">
                    {{ config('app.app_url') }}/auth/reset-password?token={{ $token }}&email={{ $email }}
                </a>
            </div> --}}

            <div class="footer">
                <p class="footer-text">
                    Sent to <strong>{{ $email }}</strong><br>
                    You are receiving this because a password reset was initiated for your account.<br>
                    <br>
                    &copy; {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'CLICKENGINE') }}<br>
                </p>
            </div>
        </div>

    </div>
</body>
</html>