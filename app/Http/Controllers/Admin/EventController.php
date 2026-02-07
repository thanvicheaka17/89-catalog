<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Notification;
class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->has('status') && $request->status) {
            $status = $request->status;
            $query->where('status', $status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $events = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('events.index', compact('events', 'perPage'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'start_at' => 'required|date|before_or_equal:end_at',
            'end_at' => 'required|date|after_or_equal:start_at',
            'description' => 'nullable|string',
        ], [
            'start_at.before_or_equal' => 'The start date must be on or before the end date',
            'end_at.after_or_equal' => 'The end date must be after or equal to the start date',
        ]);

        $event = Event::create($validated);

        $event->created_by = auth()->user()->id;
        $event->save();

        // Create and broadcast notification for the new event
        $this->eventNotification($event);

        return redirect()->route('events.index')->with('success', 'Event created successfully');
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'start_at' => 'required|date|before_or_equal:end_at',
            'end_at' => 'required|date|after_or_equal:start_at',
            'description' => 'nullable|string',
        ], [
            'start_at.before_or_equal' => 'The start date must be on or before the end date',
            'end_at.after_or_equal' => 'The end date must be after or equal to the start date',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully');
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Create and broadcast a notification for the new event.
     */
    private function eventNotification(Event $event): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'event',
                'data' => [
                    'type' => 'event',
                    'event_id' => $event->id,
                    'title' => '🎉 New Event!',
                    'message' => "📢 " . $event->title . " - Check it out now!",
                    'description' => $event->description,
                    'start_at' => $event->start_at,
                    'end_at' => $event->end_at,
                    'created_at' => $event->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the event creation
            \Illuminate\Support\Facades\Log::error('Failed to create event notification', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
