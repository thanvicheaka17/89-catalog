<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class IpHelper
{
    /**
     * Get the real client IP address from the request.
     * Handles proxies and load balancers correctly.
     */
    public static function getClientIp(Request $request): string
    {
        try {
            // Priority order: X-Forwarded-For -> X-Real-IP -> CF-Connecting-IP -> Laravel's ip() -> REMOTE_ADDR

            // 1. Check X-Forwarded-For header first (most reliable for proxy chains)
            $forwardedFor = $request->header('X-Forwarded-For');
            if ($forwardedFor && is_string($forwardedFor)) {
                // X-Forwarded-For can contain multiple IPs: "client, proxy1, proxy2"
                // The first IP is usually the original client IP
                $ips = array_map('trim', explode(',', $forwardedFor));
                foreach ($ips as $candidateIp) {
                    if ($candidateIp && filter_var($candidateIp, FILTER_VALIDATE_IP) !== false) {
                        // Prefer public IPs, but accept any valid IP if we can't find a public one
                        if (!self::isPrivateIp($candidateIp)) {
                            return $candidateIp;
                        }
                        // Store first valid IP as fallback
                        if (!isset($fallbackIp)) {
                            $fallbackIp = $candidateIp;
                        }
                    }
                }
                // If we found a valid IP (even if private), use it
                if (isset($fallbackIp)) {
                    $ip = $fallbackIp;
                }
            }

            // 2. Check X-Real-IP header (set by some proxies)
            if (!isset($ip) || self::isPrivateIp($ip)) {
                $realIp = $request->header('X-Real-IP');
                if ($realIp && is_string($realIp)) {
                    $realIp = trim($realIp);
                    if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP) !== false) {
                        if (!self::isPrivateIp($realIp)) {
                            return $realIp;
                        }
                        // Use as fallback if we don't have a better option
                        if (!isset($ip) || self::isPrivateIp($ip)) {
                            $ip = $realIp;
                        }
                    }
                }
            }

            // 3. Check CF-Connecting-IP (Cloudflare)
            if (!isset($ip) || self::isPrivateIp($ip)) {
                $cfIp = $request->header('CF-Connecting-IP');
                if ($cfIp && is_string($cfIp)) {
                    $cfIp = trim($cfIp);
                    if ($cfIp && filter_var($cfIp, FILTER_VALIDATE_IP) !== false && !self::isPrivateIp($cfIp)) {
                        return $cfIp;
                    }
                }
            }

            // 4. Use Laravel's ip() method (should work with TrustProxies middleware)
            if (!isset($ip)) {
                $ip = $request->ip();
            }

            // 5. If we still have a private IP, try to get from headers again
            if ($ip && self::isPrivateIp($ip)) {
                if ($forwardedFor && is_string($forwardedFor)) {
                    $ips = array_map('trim', explode(',', $forwardedFor));
                    $ips = array_reverse($ips);
                    foreach ($ips as $candidateIp) {
                        if ($candidateIp && filter_var($candidateIp, FILTER_VALIDATE_IP) !== false) {
                            if (!self::isPrivateIp($candidateIp)) {
                                return $candidateIp;
                            }
                        }
                    }
                }
            }

            // 6. Final validation - return the IP if valid
            if (isset($ip) && filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                return $ip;
            }

        } catch (\Exception $e) {
            \Log::warning('Error getting client IP: ' . $e->getMessage(), [
                'headers' => [
                    'X-Forwarded-For' => $request->header('X-Forwarded-For'),
                    'X-Real-IP' => $request->header('X-Real-IP'),
                    'CF-Connecting-IP' => $request->header('CF-Connecting-IP'),
                ],
                'remote_addr' => $request->server('REMOTE_ADDR'),
            ]);
        }

        // 7. Fallback to REMOTE_ADDR if all else fails
        $remoteAddr = $request->server('REMOTE_ADDR');
        if ($remoteAddr && filter_var($remoteAddr, FILTER_VALIDATE_IP) !== false) {
            return $remoteAddr;
        }

        return '0.0.0.0';
    }

    /**
     * Check if an IP address is private/internal.
     */
    public static function isPrivateIp(?string $ip): bool
    {
        if (empty($ip) || !is_string($ip) || $ip === '0.0.0.0') {
            return true;
        }

        // First validate it's a valid IP
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return true; // Treat invalid IPs as private
        }

        // Check if it's a private IP range
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
