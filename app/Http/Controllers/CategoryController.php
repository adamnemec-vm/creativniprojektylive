<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category)
    {
        $posts = $category->posts()
            ->published()
            ->listing($request->query())
            ->with(['category', 'images'])
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('categories.show', compact('category', 'posts'));
    }
}
