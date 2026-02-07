<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Provider;
class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $providers = Provider::orderBy('created_at', 'desc')->paginate($perPage);
        $data = $providers->map(function ($provider) {
            return [
                'id' => $provider->id,
                'name' => $provider->name,
                'slug' => $provider->slug,
                'description' => $provider->description,
                'logo' => $provider->getLogoUrl(),
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $providers->total(),
            'current_page' => $providers->currentPage(),
            'last_page' => $providers->lastPage(),
            'per_page' => $providers->perPage(),
        ]);
    }

    public function rtpProMaxProvider(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $providers = Provider::where('is_rtp_promax', true)->orderBy('created_at', 'desc')->paginate($perPage);
        $data = $providers->map(function ($provider) {
            return [
                'id' => $provider->id,
                'slug' => $provider->slug,
                'description' => $provider->description,
                'rtp_promax_name' => $provider->rtp_promax_name,
                'rtp_promax_logo' => $provider->getRTPPromaxLogoUrl(),
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $providers->total(),
            'current_page' => $providers->currentPage(),
            'last_page' => $providers->lastPage(),
            'per_page' => $providers->perPage(),
        ]);
    }

    public function rtpProMaxPlusProvider(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $providers = Provider::where('is_rtp_promax_plus', true)->orderBy('created_at', 'desc')->paginate($perPage);
        $data = $providers->map(function ($provider) {
            return [
                'id' => $provider->id,
                'slug' => $provider->slug,
                'description' => $provider->description,
                'rtp_promax_plus_name' => $provider->rtp_promax_plus_name,
                'rtp_promax_plus_logo' => $provider->getRTPPromaxPlusLogoUrl(),
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $providers->total(),
            'current_page' => $providers->currentPage(),
            'last_page' => $providers->lastPage(),
            'per_page' => $providers->perPage(),
        ]);
    }
}