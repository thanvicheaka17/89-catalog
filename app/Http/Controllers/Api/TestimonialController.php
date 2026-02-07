<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Testimonial; 
class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $testimonials = Testimonial::orderBy('created_at', 'desc')->paginate($perPage);

        $data = $testimonials->map(function ($testimonial) {
            return [
                'id' => $testimonial->id,
                'user_name' => $testimonial->user_name,
                'user_role' => $testimonial->user_role,
                'avatar' => $testimonial->getAvatarUrl(),
                'message' => $testimonial->message,
                'rating' => $testimonial->rating,
                'is_featured' => $testimonial->is_featured,
                'is_active' => $testimonial->is_active,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $testimonials->total(),
            'current_page' => $testimonials->currentPage(),
            'last_page' => $testimonials->lastPage(),
            'per_page' => $testimonials->perPage(),
        ]);
    }
}
