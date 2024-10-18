<!-- resources/views/emails/user_registered.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Welcome!</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Thank you for registering with us. Here are your details:</p>
    <ul>
        <li>Email: {{ $user->email }}</li>
        <li>Role: {{ $user->role }}</li>
    </ul>
    <p>We are glad to have you on board!</p>
</body>
</html>
