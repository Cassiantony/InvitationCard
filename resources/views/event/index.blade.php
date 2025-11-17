@extends('layouts.app')

@section('title', 'My Events - Invitation System')

@section('page-title', 'My Events')

@section('header-actions')
    <a href="{{ route('event.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Create Event
    </a>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="info-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="info-number">{{ $totalEvents ?? 0 }}</div>
                    <div class="info-label">Total Events</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-number">{{ $upcomingEvents ?? 0 }}</div>
                    <div class="info-label">Upcoming</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="info-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info-number">{{ $pastEvents ?? 0 }}</div>
                    <div class="info-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <div class="info-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="info-number">{{ $todayEvents ?? 0 }}</div>
                    <div class="info-label">Today</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @if($events && count($events) > 0)
            @foreach($events as $event)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card event-card h-100">
                        <div class="position-relative">
                            <span class="event-category-badge category-{{ $event->category }}">
                                {{ ucfirst($event->category) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-text event-description">{{ Str::limit($event->description, 100) }}</p>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                <span class="event-date">{{ \Carbon\Carbon::parse($event->date)->format('M j, Y g:i A') }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span class="event-location">{{ $event->location }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user text-muted me-2"></i>
                                <span class="event-organizer">{{ $event->organizer_name }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('event.show', $event->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <a href="{{ route('event.edit', $event->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <form action="{{ route('event.destroy', $event->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Events Found</h4>
                        <p class="text-muted mb-4">You haven't created any events yet.</p>
                        <a href="{{ route('event.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Create Your First Event
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($events && $events->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $events->links() }}
        </div>
    @endif
@endsection

@push('styles')
<style>
    .event-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .event-description {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .info-card {
        text-align: center;
        padding: 1.5rem;
        transition: transform 0.3s;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
    }
    
    .info-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }
    
    .info-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: var(--dark-color);
    }
</style>
@endpush