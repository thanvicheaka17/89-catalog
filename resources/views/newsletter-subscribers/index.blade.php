@extends('layouts.app')

@section('title', 'Newsletter Subscribers')

@section('breadcrumb')
    <a href="{{ route('newsletter-subscribers.index') }}" class="breadcrumb-item">Newsletter Subscribers</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Newsletter Subscribers</span>
@endsection

@section('content')
<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success" data-auto-dismiss="5000">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
        <span class="alert-content">{{ session('success') }}</span>
        <button type="button" class="alert-close" onclick="closeAlert(this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" data-auto-dismiss="5000">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span class="alert-content">{{ session('error') }}</span>
        <button type="button" class="alert-close" onclick="closeAlert(this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Newsletter Subscribers</h2>
        <p>Manage newsletter subscribers</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('newsletter-subscribers.create') }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add
        </a>
    </div>
</div>

<!-- Categories Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-header-left">
            <form action="{{ route('newsletter-subscribers.index') }}" method="GET" class="per-page-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <label for="per_page">Per page</label>
                <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                </select>
            </form>
        </div>
        <div class="table-header-right">
            <form action="{{ route('newsletter-subscribers.index') }}" method="GET" class="table-search-form">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <div class="table-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" placeholder="Search newsletter subscribers..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>
    

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="th-number">#</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Subscribed At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $index => $subscriber)
                    <tr>
                        <td class="td-number">{{ $subscribers->firstItem() + $index }}</td>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $subscriber->email }}</div>
                            </div>
                        </td>
                        <td>
                            @if($subscriber->is_active)
                                <span class="badge text-green bg-green">Active</span>
                            @else
                                <span class="badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span class="date-cell">{{ $subscriber->subscribed_at->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('newsletter-subscribers.show', $subscriber) }}" class="btn-icon btn-icon-view" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="{{ route('newsletter-subscribers.edit', $subscriber) }}" class="btn-icon btn-icon-edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('newsletter-subscribers.destroy', $subscriber) }}" method="POST" class="inline-form" data-confirm-delete="Are you sure you want to delete this newsletter subscriber?" data-delete-title="Delete Newsletter Subscriber">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="25">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                </svg>
                                <h3>No newsletter subscribers found</h3>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($subscribers->hasPages())
        <div class="table-footer">
            <div class="table-info">
                Showing {{ number_format($subscribers->firstItem()) }} to {{ number_format($subscribers->lastItem()) }} of {{ number_format($subscribers->total()) }} newsletter subscribers
            </div>
            <div class="pagination-wrapper">
                {{ $subscribers->withQueryString()->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif
</div>
@endsection

