<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TokenEncryptionService
{
    /**
     * Encrypt a token for use in URLs.
     *
     * @param string $token
     * @return string
     */
    public function encrypt(string $token): string
    {
        try {
            return Crypt::encryptString($token);
        } catch (\Exception $e) {
            Log::error('Failed to encrypt token: ' . $e->getMessage());
            // Fallback: return base64 encoded token if encryption fails
            return base64_encode($token);
        }
    }

    /**
     * Decrypt a token from URL.
     *
     * @param string $encryptedToken
     * @return string|null
     */
    public function decrypt(string $encryptedToken): ?string
    {
        try {
            return Crypt::decryptString($encryptedToken);
        } catch (\Exception $e) {
            Log::warning('Failed to decrypt token, trying base64 fallback: ' . $e->getMessage());
            
            // Fallback: try base64 decode for backward compatibility
            try {
                $decoded = base64_decode($encryptedToken, true);
                if ($decoded !== false) {
                    return $decoded;
                }
            } catch (\Exception $e2) {
                Log::error('Failed to decode token with base64: ' . $e2->getMessage());
            }
            
            return null;
        }
    }

    /**
     * Encrypt token and encode for URL safety.
     *
     * @param string $token
     * @return string
     */
    public function encryptForUrl(string $token): string
    {
        $encrypted = $this->encrypt($token);
        // URL encode to ensure it's safe for URLs
        return urlencode($encrypted);
    }

    /**
     * Decrypt token from URL.
     *
     * @param string $encryptedToken
     * @return string|null
     */
    public function decryptFromUrl(string $encryptedToken): ?string
    {
        // URL decode first
        $decoded = urldecode($encryptedToken);
        return $this->decrypt($decoded);
    }
}

