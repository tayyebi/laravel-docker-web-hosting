@extends('layouts.app')

@section('content')
@if (session('success'))
<p style="color: green;">{{ session('success') }}</p>
@endif

@if ($errors->any())
<ul style="color: red;">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

<form action="{{ route('images.build') }}" method="POST">
    @csrf
    <label for="dockerfile">Select Dockerfile:</label>
    <select name="dockerfile" id="dockerfile" required>
        <option value="">-- Select Dockerfile --</option>
        @foreach ($dockerfiles as $dockerfile)
        <option value="{{ $dockerfile }}">{{ $dockerfile }}</option>
        @endforeach
    </select>
    <button type="submit">Build Image</button>
</form>
@endsection