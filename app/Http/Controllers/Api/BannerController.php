<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;
class BannerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $banners = Banner::orderBy('created_at', 'desc')->paginate($perPage);

        $data = $banners->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'status' => $banner->is_active,
                'image' => $banner->getImageUrl(),
                'link' => $banner->link_url,
                'priority' => $banner->priority,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $banners->total(),
            'current_page' => $banners->currentPage(),
            'last_page' => $banners->lastPage(),
            'per_page' => $banners->perPage(),
        ]);
    }
}
