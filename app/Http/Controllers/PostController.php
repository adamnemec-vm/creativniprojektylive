<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function show(Post $post)
    {
        // Koncepty vidí jen autor a administrátor (náhled), ostatním 404.
        abort_unless(Gate::allows('view', $post), 404);

        return view('posts.show', ['post' => $post->load(['category', 'images'])]);
    }
}
