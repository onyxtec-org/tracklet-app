<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 8px;">
        <h1 style="color: #4a5568; margin-bottom: 20px;">You've been invited to join {{ $organization->name }}</h1>
        
        <p style="margin-bottom: 20px;">
            You have been invited to set up and manage your organization on Tracklet.
        </p>
        
        <p style="margin-bottom: 30px;">
            Click the button below to accept the invitation and create your account:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $acceptUrl }}" 
               style="background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Accept Invitation
            </a>
        </div>
        
        <p style="color: #718096; font-size: 14px; margin-top: 30px;">
            This invitation will expire on {{ $invitation->expires_at->format('F d, Y') }}.
        </p>
        
        <p style="color: #718096; font-size: 14px; margin-top: 10px;">
            If you did not expect this invitation, you can safely ignore this email.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <p style="color: #a0aec0; font-size: 12px; text-align: center;">
            If the button doesn't work, copy and paste this link into your browser:<br>
            <a href="{{ $acceptUrl }}" style="color: #667eea; word-break: break-all;">{{ $acceptUrl }}</a>
        </p>
    </div>
</body>
</html>


