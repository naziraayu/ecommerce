<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <style>
        .btn {
            padding: 10px 20px;
            margin: 10px;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <h1>Welcome</h1>

    <a href="{{ route('login') }}" class="btn">Login</a>
    <a href="{{ route('register') }}" class="btn">Register</a>
</body>
</html>
