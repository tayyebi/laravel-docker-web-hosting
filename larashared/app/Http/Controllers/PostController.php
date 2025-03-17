<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->get();
        return view('home', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        return view('post', compact('post'));
    }
}
