<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('category')->select('posts.*');

        if ($request->filled('search')) {
            $query->where('posts.title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('sort_by')) {
            $direction = $request->input('sort_direction', 'asc') === 'desc' ? 'desc' : 'asc';
            
            if ($request->sort_by === 'category') {
                $query->join('categories', 'posts.category_id', '=', 'categories.id')
                      ->orderBy('categories.name', $direction);
            } elseif (in_array($request->sort_by, ['title', 'created_at'])) {
                $query->orderBy('posts.' . $request->sort_by, $direction);
            }
        } else {
            $query->latest('posts.created_at');
        }

        $posts = $query->paginate(15)->appends($request->all());

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $post = Post::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category_id' => $validated['category_id'],
                'slug' => Str::slug($validated['title'])
            ]);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('posts/thumbnails', 'public');
                $post->update(['thumbnail_path' => $path]);
            }

            if ($request->hasFile('images')) {
                $imagesData = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('posts', 'public');
                    $imagesData[] = [
                        'image_path' => $path,
                        'post_id' => $post->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                \App\Models\Image::insert($imagesData);
            }
        });

        return redirect()->route('admin.posts.index')
            ->with('success', 'Příspěvek byl úspěšně vytvořen.');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $post) {
            $post->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category_id' => $validated['category_id'],
                'slug' => Str::slug($validated['title'])
            ]);

            if ($request->hasFile('thumbnail')) {
                if ($post->thumbnail_path) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }
                $path = $request->file('thumbnail')->store('posts/thumbnails', 'public');
                $post->update(['thumbnail_path' => $path]);
            }

            if ($request->hasFile('images')) {
                $imagesData = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('posts', 'public');
                    $imagesData[] = [
                        'image_path' => $path,
                        'post_id' => $post->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                \App\Models\Image::insert($imagesData);
            }
        });

        return redirect()->route('admin.posts.index')->with('success', 'Příspěvek byl úspěšně upraven.');
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail_path) {
            Storage::disk('public')->delete($post->thumbnail_path);
        }
        
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Příspěvek byl úspěšně smazán.');
    }
}