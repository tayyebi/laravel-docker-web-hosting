<!DOCTYPE html>
<html>
<head>
    <title>{{ $post->title }}</title>
</head>
<body>
    <h1>{{ $post->title }}</h1>
    <p>By {{ $post->user->name }}</p>
    <p>{{ $post->content }}</p>
    <a href="{{ route('home') }}">Back to Home</a>
</body>
</html>