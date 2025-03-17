@extends('layouts.app')

@section('content')
<h1>Websites</h1>
<a href="{{ route('websites.create') }}" class="btn btn-primary">Add New Record</a>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Domain</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($websites as $website)
        <tr>
            <td>{{ $website->id }}</td>
            <td>{{ $website->domain->address }}</td>
            <td>
                <a href="{{ route('websites.show', $website) }}" class="btn btn-info">View</a>
                <a href="http://{{ $website->domain->address }}/filemanager/" class="btn btn-success">File Manager</a>
                <a href="http://{{ $website->domain->address }}/phpmyadmin/index.php" class="btn btn-primary">Database Manager</a>
                <form action="{{ route('websites.destroy', $website) }}" method="POST" style="display:inline;">
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