<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\HtmlSanitizer;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class PostController extends Controller
{
    public function __construct(
        private ImageStorage $images,
        private HtmlSanitizer $sanitizer,
    )
    {
    }

    public function index(Request $request)
    {
        $query = Post::with(['category', 'author'])->select('posts.*');

        if (! $request->user()->isAdmin()) {
            $query->where('posts.user_id', $request->user()->id);
        }

        if ($request->filled('search')) {
            $query->where('posts.title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'published' => $query->published(),
                'scheduled' => $query->where('posts.published_at', '>', now()),
                'draft' => $query->whereNull('posts.published_at'),
                default => null,
            };
        }

        $direction = $request->input('sort_direction') === 'asc' ? 'asc' : 'desc';

        match ($request->input('sort_by')) {
            'category' => $query->join('categories', 'posts.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $direction),
            'title' => $query->orderBy('posts.title', $direction),
            default => $query->orderBy('posts.created_at', $direction),
        };

        $posts = $query->paginate(15)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create', [
            'post' => new Post,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();

        $this->withUploadCleanup(function (array &$stored) use ($request, $data) {
            DB::transaction(function () use ($request, $data, &$stored) {
                $post = Post::create([
                    'title' => $data['title'],
                    'slug' => Post::uniqueSlug($data['slug'] ?? $data['title']),
                    'content' => $this->sanitizer->clean($data['content']),
                    'category_id' => $data['category_id'],
                    'user_id' => $request->user()->id,
                    'published_at' => $request->publishedAt(),
                ]);

                $this->storeUploads($request, $post, $stored);
            });
        });

        return redirect()->route('admin.posts.index')->with('success', 'Příspěvek byl úspěšně vytvořen.');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return view('admin.posts.edit', [
            'post' => $post->load('images'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(PostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->validated();
        $oldThumbnail = $post->thumbnail_path;

        $this->withUploadCleanup(function (array &$stored) use ($request, $data, $post) {
            DB::transaction(function () use ($request, $data, $post, &$stored) {
                $post->update([
                    'title' => $data['title'],
                    // Adresa se mění jen výslovně, jinak by přestaly fungovat sdílené odkazy.
                    'slug' => filled($data['slug'] ?? null) ? $data['slug'] : $post->slug,
                    'content' => $this->sanitizer->clean($data['content']),
                    'category_id' => $data['category_id'],
                    'published_at' => $request->publishedAt($post),
                ]);

                $this->updateGalleryMeta($post, $data);
                $this->storeUploads($request, $post, $stored);
            });
        });

        if ($request->hasFile('thumbnail') && $oldThumbnail) {
            $this->images->delete($oldThumbnail);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Příspěvek byl úspěšně upraven.');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $paths = $post->images->pluck('image_path')->push($post->thumbnail_path);

        $post->delete();
        $paths->each(fn ($path) => $this->images->delete($path));

        return redirect()->route('admin.posts.index')->with('success', 'Příspěvek byl úspěšně smazán.');
    }

    private function storeUploads(PostRequest $request, Post $post, array &$stored): void
    {
        if ($request->hasFile('thumbnail')) {
            $stored[] = $path = $this->images->store($request->file('thumbnail'), 'posts/thumbnails', ImageStorage::THUMBNAIL_MAX);
            $post->update(['thumbnail_path' => $path]);
        }

        $nextOrder = (int) $post->images()->max('sort_order') + 1;

        foreach ($request->file('images', []) as $file) {
            $stored[] = $path = $this->images->store($file, 'posts', ImageStorage::GALLERY_MAX);
            $post->images()->create(['image_path' => $path, 'sort_order' => $nextOrder++]);
        }
    }

    private function updateGalleryMeta(Post $post, array $data): void
    {
        $order = array_flip(array_map('intval', $data['image_order'] ?? []));
        $alts = $data['image_alt'] ?? [];

        foreach ($post->images as $image) {
            $image->update([
                'sort_order' => $order[$image->id] ?? $image->sort_order,
                'alt' => array_key_exists($image->id, $alts) ? $alts[$image->id] : $image->alt,
            ]);
        }
    }

    /** Když uložení do DB selže, smaže soubory, které se mezitím nahrály. */
    private function withUploadCleanup(callable $callback): void
    {
        $stored = [];

        try {
            $callback($stored);
        } catch (Throwable $e) {
            array_walk($stored, fn ($path) => $this->images->delete($path));
            throw $e;
        }
    }
}
