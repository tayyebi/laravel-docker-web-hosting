@extends('layouts.public')

@section('content')
<div class="container">
    <h1>Blog Posts</h1>
    @foreach ($posts as $post)
        <div>
            <h2><a href="{{ route('post.show', $post->id) }}">{{ $post->title }}</a></h2>
            <p>By {{ $post->user->name }}</p>
        </div>
    @endforeach
@endsection