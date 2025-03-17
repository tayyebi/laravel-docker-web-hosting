@extends('layouts.app')

@section('content')
    <h1>Edit Domain</h1>
    <form action="{{ route('domains.update', $domain) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label>Address:</label>
            <input type="text" name="address" value="{{ $domain->address }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection