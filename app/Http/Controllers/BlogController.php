<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Blog;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('is_active', true)->latest()->paginate(9);
        return view('blogs.index', compact('blogs'));
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('blogs.show', compact('blog'));
    }
}
