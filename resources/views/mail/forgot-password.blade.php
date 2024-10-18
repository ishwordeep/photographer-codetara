<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>Hi {{ $user->name }},</p>
    <p>We received a request to reset your password. Click the link below to reset it:</p>
    <a href="{{ url('reset-password?token=' . $token . '&email=' . $user->email) }}">Reset Password</a>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you!</p>
</body>
</html>
