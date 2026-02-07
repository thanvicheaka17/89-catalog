<?php

namespace App\Helpers;

class UserAgentParser
{
    protected string $userAgent;
    protected ?string $browser = null;
    protected ?string $browserVersion = null;
    protected ?string $os = null;
    protected ?string $osVersion = null;
    protected ?string $device = null;

    public function __construct(string $userAgent)
    {
        $this->userAgent = $userAgent;
        $this->doParse();
    }

    public static function parse(string $userAgent): self
    {
        return new self($userAgent);
    }

    protected function doParse(): void
    {
        $this->parseBrowser();
        $this->parseOS();
        $this->parseDevice();
    }

    protected function parseBrowser(): void
    {
        $browsers = [
            'Edge' => '/Edg(?:e|A|iOS)?\/(\d+[\.\d]*)/',
            'Opera' => '/(?:Opera|OPR)[\/ ](\d+[\.\d]*)/',
            'Chrome' => '/Chrome\/(\d+[\.\d]*)/',
            'Firefox' => '/Firefox\/(\d+[\.\d]*)/',
            'Safari' => '/Version\/(\d+[\.\d]*).*Safari/',
            'IE' => '/(?:MSIE |rv:)(\d+[\.\d]*)/',
            'Brave' => '/Brave\/(\d+[\.\d]*)/',
            'Vivaldi' => '/Vivaldi\/(\d+[\.\d]*)/',
            'Samsung Browser' => '/SamsungBrowser\/(\d+[\.\d]*)/',
            'UC Browser' => '/UCBrowser\/(\d+[\.\d]*)/',
            'Postman' => '/PostmanRuntime\/(\d+[\.\d]*)/',
            'Insomnia' => '/insomnia\/(\d+[\.\d]*)/',
            'curl' => '/curl\/(\d+[\.\d]*)/',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $this->userAgent, $matches)) {
                $this->browser = $name;
                $this->browserVersion = $matches[1] ?? null;
                return;
            }
        }

        // Generic Safari without version
        if (str_contains($this->userAgent, 'Safari') && !str_contains($this->userAgent, 'Chrome')) {
            $this->browser = 'Safari';
        }
    }

    protected function parseOS(): void
    {
        $osPatterns = [
            'Windows' => '/Windows NT 10\.0/',
            'Windows 8.1' => '/Windows NT 6\.3/',
            'Windows 8' => '/Windows NT 6\.2/',
            'Windows 7' => '/Windows NT 6\.1/',
            'Windows Vista' => '/Windows NT 6\.0/',
            'Windows XP' => '/Windows NT 5\.1/',
            'macOS' => '/Mac OS X (\d+[._]\d+[._]?\d*)/',
            'iOS' => '/(?:iPhone|iPad|iPod).*OS (\d+[._]\d+)/',
            'Android' => '/Android (\d+[\.\d]*)/',
            'Linux' => '/Linux/',
            'Chrome OS' => '/CrOS/',
            'Ubuntu' => '/Ubuntu/',
            'Fedora' => '/Fedora/',
            'FreeBSD' => '/FreeBSD/',
        ];

        foreach ($osPatterns as $name => $pattern) {
            if (preg_match($pattern, $this->userAgent, $matches)) {
                $this->os = $name;
                if (isset($matches[1])) {
                    $this->osVersion = str_replace('_', '.', $matches[1]);
                }
                return;
            }
        }
    }

    protected function parseDevice(): void
    {
        // Check for mobile devices
        if (preg_match('/iPhone/', $this->userAgent)) {
            $this->device = 'iPhone';
        } elseif (preg_match('/iPad/', $this->userAgent)) {
            $this->device = 'iPad';
        } elseif (preg_match('/Android.*Mobile/', $this->userAgent)) {
            $this->device = 'Mobile';
        } elseif (preg_match('/Android/', $this->userAgent)) {
            $this->device = 'Tablet';
        } elseif (preg_match('/Macintosh/', $this->userAgent)) {
            $this->device = 'Desktop';
        } elseif (preg_match('/Windows/', $this->userAgent)) {
            $this->device = 'Desktop';
        } elseif (preg_match('/Linux/', $this->userAgent)) {
            $this->device = 'Desktop';
        } elseif (preg_match('/PostmanRuntime|insomnia|curl/i', $this->userAgent)) {
            $this->device = 'API Client';
        } else {
            $this->device = 'Unknown';
        }
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function getBrowserVersion(): ?string
    {
        return $this->browserVersion;
    }

    public function getBrowserWithVersion(): string
    {
        if (!$this->browser) {
            return 'Unknown';
        }
        return $this->browserVersion 
            ? "{$this->browser} {$this->browserVersion}" 
            : $this->browser;
    }

    public function getOS(): ?string
    {
        return $this->os;
    }

    public function getOSVersion(): ?string
    {
        return $this->osVersion;
    }

    public function getOSWithVersion(): string
    {
        if (!$this->os) {
            return 'Unknown';
        }
        return $this->osVersion 
            ? "{$this->os} {$this->osVersion}" 
            : $this->os;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function getBrowserIcon(): string
    {
        return match($this->browser) {
            'Chrome' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" fill="#4285F4"/><circle cx="12" cy="12" r="4" fill="white"/><path d="M12 8 L21 8 A10 10 0 0 0 12 2 Z" fill="#EA4335"/><path d="M8 12 L3 21 A10 10 0 0 0 21 12 Z" fill="#34A853" transform="rotate(120 12 12)"/><path d="M8 12 L3 3 A10 10 0 0 0 3 21 Z" fill="#FBBC05"/></svg>',
            'Firefox' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#FF7139"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>',
            'Safari' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#0FB5EE"><circle cx="12" cy="12" r="10"/><path d="M12 2v10l7-7" fill="white"/></svg>',
            'Edge' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#0078D7"><circle cx="12" cy="12" r="10"/></svg>',
            'Opera' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#FF1B2D"><circle cx="12" cy="12" r="10"/></svg>',
            'IE' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#00A4EF"><circle cx="12" cy="12" r="10"/></svg>',
            'Brave' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#FB542B"><circle cx="12" cy="12" r="10"/></svg>',
            'Postman' => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="#FF6C37"><circle cx="12" cy="12" r="10"/></svg>',
            default => '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><text x="12" y="16" text-anchor="middle" font-size="10">?</text></svg>',
        };
    }

    public function getBrowserIconClass(): string
    {
        return match($this->browser) {
            'Chrome' => 'text-blue-500',
            'Firefox' => 'text-orange-500',
            'Safari' => 'text-sky-500',
            'Edge' => 'text-blue-600',
            'Opera' => 'text-red-500',
            'IE' => 'text-blue-400',
            'Brave' => 'text-orange-600',
            'Postman' => 'text-orange-500',
            'curl' => 'text-zinc-500',
            default => 'text-zinc-400',
        };
    }

    public function getOSIconClass(): string
    {
        return match(true) {
            str_contains($this->os ?? '', 'Windows') => 'text-blue-500',
            str_contains($this->os ?? '', 'macOS') => 'text-zinc-600 dark:text-zinc-300',
            str_contains($this->os ?? '', 'iOS') => 'text-zinc-600 dark:text-zinc-300',
            str_contains($this->os ?? '', 'Android') => 'text-green-500',
            str_contains($this->os ?? '', 'Linux') => 'text-yellow-600',
            str_contains($this->os ?? '', 'Ubuntu') => 'text-orange-500',
            default => 'text-zinc-400',
        };
    }

    public function getDeviceIconClass(): string
    {
        return match($this->device) {
            'iPhone', 'Mobile' => 'text-zinc-600 dark:text-zinc-300',
            'iPad', 'Tablet' => 'text-zinc-600 dark:text-zinc-300',
            'Desktop' => 'text-blue-500',
            'API Client' => 'text-purple-500',
            default => 'text-zinc-400',
        };
    }

    public function toArray(): array
    {
        return [
            'browser' => $this->browser,
            'browser_version' => $this->browserVersion,
            'browser_full' => $this->getBrowserWithVersion(),
            'browser_icon_class' => $this->getBrowserIconClass(),
            'os' => $this->os,
            'os_version' => $this->osVersion,
            'os_full' => $this->getOSWithVersion(),
            'os_icon_class' => $this->getOSIconClass(),
            'device' => $this->device,
            'device_icon_class' => $this->getDeviceIconClass(),
        ];
    }
}

