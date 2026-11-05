<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()
            ->listing($request->query())
            ->with(['category', 'images'])
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categoriesByName = Category::all()->keyBy('name');

        return view('home', compact('posts', 'categoriesByName'));
    }
}
