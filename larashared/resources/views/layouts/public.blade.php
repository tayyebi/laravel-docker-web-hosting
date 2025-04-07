<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            /* Light background for contrast */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .navbar {
            background-color: #001f3f;
            /* Navy blue */
        }

        .navbar .navbar-brand {
            color: #ffffff;
            /* White text for logo */
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar .nav-link {
            color: #bbd4ef;
            /* Soft complementary blue */
            transition: color 0.3s;
        }

        .navbar .nav-link:hover {
            color: #ffffff;
            /* White text on hover */
        }

        .content {
            padding: 30px;
            text-align: center;
            flex-grow: 1;
            /* Fills vertical space */
        }

        .content h1 {
            color: #001f3f;
            /* Navy blue heading */
        }

        .card {
            border: 1px solid #dde3ea;
            /* Subtle border */
            border-radius: 4px;
            /* Reduced border radius */
            background-color: #ffffff;
            /* White card background */
        }

        footer {
            background-color: #001f3f;
            /* Navy blue footer */
            color: #ffffff;
            text-align: center;
            padding: 10px 0;
        }

        footer a {
            color: #bbd4ef;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">{{ config('app.name') }}</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    @if (Route::currentRouteName() === 'login')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @elseif (Route::currentRouteName() === 'register')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @elseif (Route::currentRouteName() === 'dashboard')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="content">
        <div class="card">
            <div class="card-body">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Bottom Footer -->
    <footer>
        Copyright © {{ date('Y') }} |
        <a href="/privacy-policy">Privacy Policy</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>