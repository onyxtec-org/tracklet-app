<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 8px;">
        <h1 style="color: #4a5568; margin-bottom: 20px;">Password Reset OTP</h1>
        
        <p style="margin-bottom: 20px;">
            Hello {{ $user->name }},
        </p>
        
        <p style="margin-bottom: 20px;">
            You have requested to reset your password. Use the following OTP (One-Time Password) to reset your password:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <div style="background-color: #667eea; color: white; padding: 20px; border-radius: 8px; display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 8px;">
                {{ $otp }}
            </div>
        </div>
        
        <p style="color: #718096; font-size: 14px; margin-top: 30px;">
            This OTP is valid for 10 minutes. Please do not share this OTP with anyone.
        </p>
        
        <p style="color: #718096; font-size: 14px; margin-top: 10px;">
            If you did not request a password reset, please ignore this email or contact support if you have concerns.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <p style="color: #a0aec0; font-size: 12px; text-align: center;">
            This is an automated message from Tracklet. Please do not reply to this email.
        </p>
    </div>
</body>
</html>

