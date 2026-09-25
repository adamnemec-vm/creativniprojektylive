<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('id')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(Request $request)
    {
        Category::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('success', 'Kategorie byla vytvořena.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Kategorie byla upravena.');
    }

    public function destroy(Category $category)
    {
        // Mazání kategorie by přes cizí klíč smazalo i všechny její příspěvky.
        if ($category->posts()->exists()) {
            return back()->withErrors(['Kategorii nelze smazat, obsahuje příspěvky. Nejdřív je přesuňte do jiné kategorie.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategorie byla smazána.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'.($category ? ','.$category->id : '')],
            'description' => ['required', 'string', 'max:5000'],
        ], [], [
            'name' => 'název',
            'description' => 'popis',
        ]);
    }
}
