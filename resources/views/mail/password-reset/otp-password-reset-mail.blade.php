<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Code</title>
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Password Reset Code</h2>

        <p>You have requested to reset your password. Use the following code to verify your identity:</p>

        <div style="background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;">
            <h1 style="color: #333; font-size: 32px; letter-spacing: 5px; margin: 0;">{{ $otpCode }}</h1>
        </div>

        <p>This code will expire in 2 minutes for security reasons.</p>

        <p>If you did not request this password reset, please ignore this email.</p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p style="color: #666; font-size: 12px;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>

</html>