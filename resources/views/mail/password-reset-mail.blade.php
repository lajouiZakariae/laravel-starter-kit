<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password Notification</title>
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Reset Password Notification</h2>

        <p>You have requested to reset your password. Use the following code to verify your identity:</p>

        <button>
            <a href="{{ $url }}"
                style="display: inline-block; padding: 10px 20px; background-color: #333; color: #fff; text-decoration: none; border-radius: 5px;">
                Reset Password
            </a>
        </button>

        <p>This password reset link will expire in {{ $expiresAfter }} minutes.</p>

        <p>If you did not request this password reset, please ignore this email.</p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p style="color: #666; font-size: 12px;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>

</html>