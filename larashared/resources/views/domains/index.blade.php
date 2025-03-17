@extends('layouts.app')

@section('content')
<h1>Domains</h1>
<a href="{{ route('domains.create') }}" class="btn btn-primary">Add New Record</a>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Address</th>
            <th>Registered Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->address }}</td>
            <td>
                <a href="{{ route('domains.show', $record) }}" class="btn btn-info">View</a>
                <a href="{{ route('domains.edit', $record) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('domains.destroy', $record) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection