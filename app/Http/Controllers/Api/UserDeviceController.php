<?php

namespace App\Http\Controllers\Api;

use App\Helpers\IpHelper;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Http\Controllers\Controller;
class UserDeviceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 25);

        $devices = UserDevice::where('user_id', $user->id)
            ->orderBy('last_active_at', 'desc')
            ->paginate($perPage);

        // Get the real IP address of the current request
        $currentRealIp = IpHelper::getClientIp($request);

        $data = $devices->map(function ($device) {
            return [
                'id' => $device->id,
                'user_id' => $device->user_id,
                'device_name' => $device->device_name,
                'ip_address' => $device->ip_address,
                'user_agent' => $device->user_agent,
                'last_active_at' => $device->last_active_at,
                'revoked' => $device->revoked,
                'device_fingerprint' => $device->device_fingerprint,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'current_ip_address' => $currentRealIp, // Include current real IP in response
            'total' => $devices->total(),
            'current_page' => $devices->currentPage(),
            'last_page' => $devices->lastPage(),
            'per_page' => $devices->perPage(),
        ]);
    }

    public function revoke(Request $request, $id)
    {
        $user = auth('api')->user();

        $device = UserDevice::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $device->update([
            'revoked' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device access revoked successfully'
        ]);
    }

    public function revokeAllTrustedDevices(Request $request)
    {
        $user = auth('api')->user();
        UserDevice::where('user_id', $user->id)->update(['revoked' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'All trusted devices revoked successfully'
        ]);
    }
}
