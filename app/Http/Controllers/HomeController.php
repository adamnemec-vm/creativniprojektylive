<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $posts = Post::published()
            ->with(['category', 'images'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(fn ($q) => $q
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%"));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('home', compact('posts', 'search'));
    }
}
