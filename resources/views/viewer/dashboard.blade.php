<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewer — InvitationCard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="mx-auto" style="max-width: 640px;">
        <h1 class="h4 mb-2">Viewer portal</h1>
        <p class="text-muted mb-3">
            You can <strong>scan invitation QR codes</strong> for events your organizer assigned you to.
            You cannot create events, edit invitees, or use other management tools.
        </p>
        @php $assignedEvents = auth()->user()->viewerEvents()->orderByDesc('date')->get(); @endphp
        @if ($assignedEvents->isNotEmpty())
            <div class="card mb-3">
                <div class="card-body py-3">
                    <h2 class="h6">Your assigned events</h2>
                    <ul class="list-unstyled mb-0 small">
                        @foreach ($assignedEvents as $assigned)
                            <li class="mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> {{ $assigned->title }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <div class="d-flex flex-wrap gap-2 mb-3">
            @if (auth()->user()->viewer_for_user_id || $assignedEvents->isNotEmpty())
                <a class="btn btn-primary" href="{{ route('event.invitation.verify') }}">
                    Open QR scanner
                </a>
            @else
                <button type="button" class="btn btn-secondary" disabled title="Not linked to an organizer">
                    QR scanner unavailable
                </button>
            @endif
            <a class="btn btn-outline-secondary" href="{{ route('profile.edit') }}">Profile</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">Sign out</button>
            </form>
        </div>
        @unless (auth()->user()->viewer_for_user_id)
            <p class="small text-muted mb-0">If you need access, ask your organizer to add you again from their user management screen.</p>
        @endunless
    </div>
</div>
</body>
</html>
