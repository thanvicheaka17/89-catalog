<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->get();
        $data = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'isActive' => $event->is_active,
                'description' => $event->description,
                'startAt' => $event->start_at,
                'endAt' => $event->end_at,
            ];
        });   
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
