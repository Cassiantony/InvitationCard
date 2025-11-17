<!DOCTYPE html>
<html>
<head>
    <title>Invitee Details</title>
</head>
<body>
    <h1>{{ $invitee->name }}</h1>
    <p>Email: {{ $invitee->email }}</p>
    <p>Company: {{ $invitee->company }}</p>
    <p>Notes: {{ $invitee->notes }}</p>
</body>
</html>
