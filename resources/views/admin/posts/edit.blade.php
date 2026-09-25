@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center gap-4">
        <h2 class="text-2xl font-bold text-brand">Upravit příspěvek</h2>
        <a href="{{ route('posts.show', $post) }}" target="_blank" class="text-gray-300 hover:text-white">
            {{ $post->isPublished() ? 'Zobrazit na webu' : 'Náhled' }} ↗
        </a>
    </div>

    @include('admin.posts._form')
@endsection
