<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IpGeolocationService
{
    /**
     * Get location information from IP address.
     *
     * @param string $ip
     * @return string
     */
    public function getLocation(string $ip): string
    {
        // Handle localhost and private IPs
        if ($this->isLocalOrPrivateIp($ip)) {
            return 'Local Development';
        }

        // Cache the result for 24 hours to avoid excessive API calls
        $cacheKey = "ip_location_{$ip}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($ip) {
            return $this->fetchLocationFromApi($ip);
        });
    }

    /**
     * Check if IP is localhost or private IP.
     *
     * @param string $ip
     * @return bool
     */
    private function isLocalOrPrivateIp(string $ip): bool
    {
        // Check for localhost
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'localhost') {
            return true;
        }

        // Check for private IP ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        return false;
    }

    /**
     * Fetch location from IP geolocation API.
     *
     * @param string $ip
     * @return string
     */
    private function fetchLocationFromApi(string $ip): string
    {
        try {
            // Using ip-api.com (free tier: 45 requests/minute, no API key required)
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,message,country,regionName,city,zip,lat,lon,timezone,isp'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'success') {
                    return $this->formatLocation($data);
                }

                // If API returns an error, log it
                if (isset($data['message'])) {
                    Log::warning("IP geolocation API error for IP {$ip}: {$data['message']}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch IP geolocation for IP {$ip}: " . $e->getMessage());
        }

        // Fallback to unknown location
        return 'Unknown Location';
    }

    /**
     * Format location data into a readable string.
     *
     * @param array $data
     * @return string
     */
    private function formatLocation(array $data): string
    {
        $parts = [];

        if (!empty($data['city'])) {
            $parts[] = $data['city'];
        }

        if (!empty($data['regionName'])) {
            $parts[] = $data['regionName'];
        }

        if (!empty($data['country'])) {
            $parts[] = $data['country'];
        }

        // If we have location parts, join them
        if (!empty($parts)) {
            $location = implode(', ', $parts);
            
            // Optionally add ISP info if available
            if (!empty($data['isp'])) {
                $location .= ' (' . $data['isp'] . ')';
            }
            
            return $location;
        }

        return 'Unknown Location';
    }

    /**
     * Get detailed location information as an array.
     *
     * @param string $ip
     * @return array
     */
    public function getDetailedLocation(string $ip): array
    {
        // Handle localhost and private IPs
        if ($this->isLocalOrPrivateIp($ip)) {
            return [
                'city' => null,
                'region' => null,
                'country' => 'Local Development',
                'zip' => null,
                'latitude' => null,
                'longitude' => null,
                'timezone' => null,
                'isp' => null,
                'formatted' => 'Local Development',
            ];
        }

        $cacheKey = "ip_location_detailed_{$ip}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($ip) {
            return $this->fetchDetailedLocationFromApi($ip);
        });
    }

    /**
     * Fetch detailed location from IP geolocation API.
     *
     * @param string $ip
     * @return array
     */
    private function fetchDetailedLocationFromApi(string $ip): array
    {
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}", [
                'fields' => 'status,message,country,regionName,city,zip,lat,lon,timezone,isp'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'city' => $data['city'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                        'zip' => $data['zip'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['isp'] ?? null,
                        'formatted' => $this->formatLocation($data),
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch detailed IP geolocation for IP {$ip}: " . $e->getMessage());
        }

        return [
            'city' => null,
            'region' => null,
            'country' => null,
            'zip' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'isp' => null,
            'formatted' => 'Unknown Location',
        ];
    }
}

