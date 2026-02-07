<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('is_active', 'like', "%{$search}%");
            });
        }
        
        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $subscribers = $query->orderBy('subscribed_at', 'desc')->paginate($perPage);

        return view('newsletter-subscribers.index', compact('subscribers', 'perPage'));
    }

    public function create()
    {
        return view('newsletter-subscribers.create');
    }
    
    public function show(NewsletterSubscriber $newsletterSubscriber)
    {
        return view('newsletter-subscribers.show', compact('newsletterSubscriber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers',
            'is_active' => 'required|boolean',
        ]);

        $validated['user_id'] = auth()->user()->id;
        $validated['subscribed_at'] = now();

        $subscriber = NewsletterSubscriber::create($validated);

        return redirect()->route('newsletter-subscribers.index')->with('success', 'Subscriber created successfully');
    }

    public function edit(NewsletterSubscriber $newsletterSubscriber)
    {
        return view('newsletter-subscribers.edit', compact('newsletterSubscriber'));
    }

    public function update(Request $request, NewsletterSubscriber $newsletterSubscriber)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email,' . $newsletterSubscriber->id,
            'is_active' => 'required|boolean',
        ]);

        $newsletterSubscriber->update([
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('newsletter-subscribers.index')->with('success', 'Subscriber updated successfully');
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->delete();
        return redirect()->route('newsletter-subscribers.index')->with('success', 'Subscriber deleted successfully');
    }
}
