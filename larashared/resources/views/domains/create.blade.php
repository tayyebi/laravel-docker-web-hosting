@extends('layouts.app')

@section('content')
<h1>Create Domain Record</h1>
<form action="{{ route('domains.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Address:</label>
        <input type="text" name="address" required class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Create</button>
</form>
@endsection