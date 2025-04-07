@extends('layouts.public')

@section('content')
<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 content">
    <div class="card">
        <div class="card-body">
            @yield('content')
        </div>
    </div>
</main>
@endsection