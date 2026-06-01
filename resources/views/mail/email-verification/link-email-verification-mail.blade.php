<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email Address</title>
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>Verify Your Email Address</h2>

        <p>Please click the button below to verify your email address:</p>

        <button>
            <a href="{{ $url }}"
                style="display: inline-block; padding: 10px 20px; background-color: #333; color: #fff; text-decoration: none; border-radius: 5px;">
                Verify Email
            </a>
        </button>

        <p>This verification link will expire in {{ $expiresAfter }} minutes.</p>

        <p>If you did not request this verification, please ignore this email.</p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p style="color: #666; font-size: 12px;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>

</html>