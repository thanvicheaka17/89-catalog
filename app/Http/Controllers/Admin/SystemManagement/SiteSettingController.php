<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    /**
     * Display a listing of site settings.
     */
    public function index(Request $request)
    {
        $query = SiteSetting::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%");
            });
        }

        // Filter by group
        if ($request->has('group') && $request->group) {
            $group = $request->group;
            $query->where('group', $group);
        }

        // Order by created_at descending
        $query->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $siteSettings = $query->paginate($perPage);

        return view('system-management.site-settings.index', compact('siteSettings', 'perPage'));
    }

    /**
     * Show the form for creating a new site setting.
     */
    public function create()
    {
        return view('system-management.site-settings.create');
    }

    /**
     * Store a newly created site setting.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:site_settings,key',
            'value' => 'required',
            'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other',
        ]);

        // Handle JSON values - try to decode if it's a JSON string
        $value = $request->input('value');
        
        // Check if the value is a JSON string
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            
            // If JSON decode was successful and result is an array or object, use decoded value
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                $validated['value'] = $decoded;
            } else {
                // If it's not valid JSON, treat as regular string
                // But check length to prevent extremely long strings
                if (strlen($value) > 10000) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['value' => 'Value is too long. Maximum 10000 characters allowed.']);
                }
                $validated['value'] = $value;
            }
        } else {
            // If value is already an array/object, use it directly
            $validated['value'] = $value;
        }

        SiteSetting::create($validated);

        return redirect()->route('system-management.site-settings.index')->with('success', 'Site setting created successfully');
    }

    /**
     * Display the specified site setting.
     */
    public function show(SiteSetting $siteSetting)
    {
        return view('system-management.site-settings.show', compact('siteSetting'));
    }

    /**
     * Show the form for editing a site setting.
     */
    public function edit(SiteSetting $siteSetting)
    {
        return view('system-management.site-settings.edit', compact('siteSetting'));
    }

    /**
     * Update the specified site setting.
     */
    public function update(Request $request, SiteSetting $siteSetting)
    {
        // Special validation for avatar gallery
        if ($siteSetting->key === 'available_avatars') {
            $validated = $request->validate([
                'key' => 'required|string|max:255|unique:site_settings,key,' . $siteSetting->id,
                'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other,email',
            ]);
        } else {
            $validated = $request->validate([
                'key' => 'required|string|max:255|unique:site_settings,key,' . $siteSetting->id,
                'value' => 'required',
                'group' => 'required|string|max:255|in:general,contact,social,analytics,seo,footer,global,tools,other,email',
            ]);
        }

        // Special handling for avatar gallery uploads
        if ($siteSetting->key === 'available_avatars') {
            $avatarResult = $this->handleAvatarUploads($request, $siteSetting->value ?? []);
            $validated['value'] = $avatarResult;

        } else {
            $value = $request->input('value');

            // Check if the value is a JSON string
            if (is_string($value)) {
                $decoded = json_decode($value, true);

                // If JSON decode was successful and result is an array or object, use decoded value
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                    $validated['value'] = $decoded;
                } else {
                    // If it's not valid JSON, treat as regular string
                    // But check length to prevent extremely long strings
                    if (strlen($value) > 10000) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['value' => 'Value is too long. Maximum 10000 characters allowed.']);
                    }
                    $validated['value'] = $value;
                }
            } else {
                // If value is already an array/object, use it directly
                $validated['value'] = $value;
            }
        }

        $siteSetting->update($validated);

        return redirect()->route('system-management.site-settings.index')->with('success', 'Site setting updated successfully');
    }

    /**
     * Handle avatar uploads for the gallery
     */
    private function handleAvatarUploads(Request $request, array $currentAvatars = []): array
    {
        $originalAvatars = $currentAvatars;
        $avatars = $currentAvatars;

        if ($request->hasFile('avatar_files')) {
            $files = $request->file('avatar_files');

            if (!is_array($files)) {
                $files = [$files];
            }

            $uploadPath = public_path('images/avatars/gallery');
            if (!file_exists($uploadPath)) {
                if (!mkdir($uploadPath, 0777, true)) {
                    throw new \Exception('Failed to create upload directory: ' . $uploadPath);
                }
            }

            if (!is_writable($uploadPath)) {
                if (!chmod($uploadPath, 0777)) {
                    throw new \Exception('Failed to set permissions on upload directory: ' . $uploadPath);
                }
            }

            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $filename = time(). uniqid() . '.'. $extension;
                    $path = 'images/avatars/gallery/' . $filename;

                    try {
                        $file->move($uploadPath, $filename);

                        $fullFilePath = $uploadPath . '/' . $filename;

                        if (file_exists($fullFilePath)) {
                            if (!in_array($path, $avatars)) {
                                $avatars[] = $path;
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to upload avatar file: ' . $e->getMessage(), [
                            'filename' => $filename,
                            'original_name' => $file->getClientOriginalName(),
                            'upload_path' => $uploadPath,
                            'path_writable' => is_writable($uploadPath),
                            'path_exists' => file_exists($uploadPath),
                        ]);
                        throw $e;
                    }
                }
            }
        }

        $keepAvatars = $request->input('keep_avatars', []);
        if (!is_array($keepAvatars)) {
            $keepAvatars = [$keepAvatars];
        }

        $removedAvatars = array_diff($originalAvatars, $keepAvatars);
        foreach ($removedAvatars as $removedAvatar) {
            if (str_contains($removedAvatar, 'images/avatars/gallery/')) {
                $fullPath = public_path($removedAvatar);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $avatars = array_filter($avatars, function($avatar) use ($keepAvatars, $originalAvatars) {
            if (in_array($avatar, $originalAvatars)) {
                return in_array($avatar, $keepAvatars);
            }
            return true;
        });

        $result = array_values($avatars);

        return $result;
    }

    /**
     * Remove the specified site setting.
     */
    public function destroy(SiteSetting $siteSetting)
    {
        if ($siteSetting->key === 'available_avatars' && is_array($siteSetting->value)) {
            foreach ($siteSetting->value as $avatarPath) {
                if (str_contains($avatarPath, 'images/avatars/gallery/')) {
                    $fullPath = public_path($avatarPath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
        }

        $siteSetting->delete();

        return redirect()->route('system-management.site-settings.index')->with('success', 'Site setting deleted successfully');
    }
}