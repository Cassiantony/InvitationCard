<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewers — {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 960px;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <a href="{{ route('event.show', $event->id) }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-1"></i> Back to event
            </a>
            <h1 class="h4 mb-0">Event viewers</h1>
            <p class="text-muted mb-0 small">{{ $event->title }}</p>
        </div>
        <span class="badge bg-primary fs-6">{{ $viewers->count() }} assigned</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h6 mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Add viewer</h2>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Creates a viewer login linked to you. They can scan QR codes only for this event.
                    </p>
                    <form method="POST" action="{{ route('event.viewers.store', $event) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Full name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-muted">(optional)</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            <div class="form-text">Required for new accounts. Leave blank if re-adding an existing viewer by email.</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i> Add to this event
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h6 mb-0"><i class="fas fa-users me-2 text-primary"></i>Assigned viewers</h2>
                </div>
                <div class="card-body p-0">
                    @forelse($viewers as $viewer)
                        <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $viewer->name }}</div>
                                <div class="small text-muted">{{ $viewer->email }}</div>
                                @if($viewer->phone && $viewer->phone !== '-')
                                    <div class="small text-muted">{{ $viewer->phone }}</div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('event.viewers.destroy', [$event, $viewer]) }}"
                                  onsubmit="return confirm('Remove this viewer from the event?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from event">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-eye fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">No viewers yet. Add someone to help scan invitations at the door.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
