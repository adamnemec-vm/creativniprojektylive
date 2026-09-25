<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('posts', 'slug')->ignore($post?->id),
            ],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:6144'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:6144'],
            'image_order' => ['nullable', 'array'],
            'image_order.*' => ['integer'],
            'image_alt' => ['nullable', 'array'],
            'image_alt.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'název',
            'slug' => 'adresa (slug)',
            'content' => 'obsah',
            'category_id' => 'kategorie',
            'published_at' => 'datum publikace',
            'thumbnail' => 'náhledový obrázek',
            'images.*' => 'obrázek galerie',
            'image_alt.*' => 'popis obrázku',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'Adresa smí obsahovat jen malá písmena bez diakritiky, číslice a pomlčky.',
        ];
    }

    public function publishedAt(?Post $existing = null): ?Carbon
    {
        if ($this->input('status') !== 'published') {
            return null;
        }

        if ($this->filled('published_at')) {
            return Carbon::parse($this->input('published_at'));
        }

        return $existing?->published_at ?? now();
    }
}
