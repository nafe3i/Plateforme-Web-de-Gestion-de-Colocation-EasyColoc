<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation EasyColoc</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #1f2937;">
    <h2 style="margin-bottom: 12px;">Invitation a rejoindre une colocation</h2>

    <p>
        Bonjour,
    </p>

    <p>
        <strong>{{ $invitation->inviter->name }}</strong>
        vous invite a rejoindre la colocation
        <strong>{{ $invitation->colocation->name }}</strong>.
    </p>

    @if($invitation->colocation->description)
        <p>
            Description: {{ $invitation->colocation->description }}
        </p>
    @endif

    <p style="margin-top: 20px;">
        Cliquez sur ce lien pour repondre a l'invitation:
    </p>

    <p>
        <a href="{{ $inviteLink }}" style="color: #2563eb;">{{ $inviteLink }}</a>
    </p>

    <p style="margin-top: 24px; color: #6b7280; font-size: 14px;">
        Si vous n'etes pas concerne, vous pouvez ignorer cet email.
    </p>
</body>
</html>

