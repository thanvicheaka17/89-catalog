<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Get all active promotions.
     *
     * Only returns promotions if:
     * - isActive is true
     * - The current date is between startAt and expiresAt (if dates are set)
     */
    public function index(Request $request): JsonResponse
    {
        $today = now()->startOfDay();
        $perPage = $request->input('per_page', 25);

        $promos = Promotion::where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->where(function ($q) use ($today) {
                    $q->whereNotNull('start_date')
                        ->whereNotNull('end_date')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                })
                    ->orWhere(function ($q) use ($today) {
                        $q->whereNotNull('start_date')
                            ->whereNull('end_date')
                            ->whereDate('start_date', '<=', $today);
                    })
                    ->orWhere(function ($q) use ($today) {
                        $q->whereNull('start_date')
                            ->whereNotNull('end_date')
                            ->whereDate('end_date', '>=', $today);
                    })
                    ->orWhere(function ($q) {
                        $q->whereNull('start_date')
                            ->whereNull('end_date');
                    });
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $promos->map(function ($promo) {
            return [
                'id' => $promo->id,
                'title' => $promo->title,
                'message' => $promo->message,
                'buttonText' => $promo->button_text,
                'buttonUrl' => $promo->button_url,
                'imageUrl' => $promo->getImageUrl(),
                'position' => $promo->position,
                'priority' => $promo->priority,
                'isActive' => $promo->is_active,
                'startAt' => $promo->start_date?->toIso8601String(),
                'expiresAt' => $promo->end_date?->toIso8601String(),
                'styles' => [
                    'backgroundGradientType' => $promo->background_gradient_type,
                    'backgroundStyle' => $promo->getBackgroundStyle(),
                    'textColor' => $promo->text_color,
                    'buttonGradientType' => $promo->button_gradient_type,
                    'buttonStyle' => $promo->getButtonStyle(),
                    'buttonTextColor' => $promo->button_text_color,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $promos->total(),
            'current_page' => $promos->currentPage(),
            'last_page' => $promos->lastPage(),
            'per_page' => $promos->perPage(),
        ]);
    }

}

