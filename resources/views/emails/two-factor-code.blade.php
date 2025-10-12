<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111;">
    <div style="max-width: 540px; margin: 0 auto; padding: 24px; border: 1px solid #e5e7eb; border-radius: 8px;">
        <h2 style="color:#14532d;">Two-Factor Verification</h2>
        <p>Hi {{ $name }},</p>
        <p>Use the following verification code to continue logging in:</p>
        <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px; color:#065f46;">{{ $code }}</p>
        <p style="font-size: 12px; color: #6b7280;">This code will expire in 10 minutes. If you did not request this, you can safely ignore this email.</p>
        <p style="font-size: 12px; color: #6b7280; margin-top: 16px;">Thank you,</p>
        <p style="font-size: 12px; color: #6b7280;">Administrative Portal</p>
    </div>
</body>
</html>
