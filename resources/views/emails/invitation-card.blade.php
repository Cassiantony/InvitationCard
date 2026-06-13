<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333;">
    <p>Hello {{ $invitee->name }},</p>

    <p>You are invited to <strong>{{ $event->title }}</strong>.</p>

    <p>
        <strong>Date:</strong> {{ $event->date?->format('l, F j, Y g:i A') ?? 'TBA' }}<br>
        <strong>Location:</strong> {{ $event->location ?? 'TBA' }}
    </p>

    @if($event->description)
        <p>{{ $event->description }}</p>
    @endif

    <p>Your personal invitation card is attached as an image (PNG). Present the QR code on the card at the venue for check-in.</p>

    <p>Thank you!</p>
</body>
</html>
