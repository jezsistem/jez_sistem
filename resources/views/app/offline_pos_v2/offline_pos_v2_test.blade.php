<!DOCTYPE html>
<html>
<head>
    <title>Offline POS V2 - TEST</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>✅ Offline POS V2 - Route Works!</h1>
    <p>User: {{ $data['user']->u_name }}</p>
    <p>Store: {{ $data['store']->st_name }}</p>
    <p>Title: {{ $data['title'] }}</p>
</body>
</html>

