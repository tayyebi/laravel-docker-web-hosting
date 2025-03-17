<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function packages()
    {
        return view('packages');
    }

    public function emails()
    {
        return view('emails');
    }

    public function databases()
    {
        return view('databases');
    }
}
