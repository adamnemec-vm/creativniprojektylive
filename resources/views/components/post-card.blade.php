@props(['post'])

@php($cover = $post->coverImagePath())

<a href="{{ route('posts.show', $post) }}" class="block group">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden relative h-full flex flex-col transition-transform duration-300 hover:-translate-y-2">
        <div class="absolute top-2 right-2 z-10">
            <span class="bg-black bg-opacity-50 px-3 py-1 rounded-full text-sm text-brand">
                {{ $post->category->name }}
            </span>
        </div>

        <div class="overflow-hidden h-64 flex-shrink-0">
            <img src="{{ $cover ? asset('storage/'.$cover) : asset('images/defaults/default.jpg') }}"
                 alt="{{ $post->title }}"
                 loading="lazy"
                 class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
        </div>

        <div class="p-6 flex-grow flex flex-col">
            <h2 class="text-xl font-bold mb-2 text-brand">{{ $post->title }}</h2>
            <p class="text-gray-600 mb-4 flex-grow">{{ $post->excerpt() }}</p>
            <div class="flex justify-end mt-auto">
                <span class="px-4 py-2 rounded-lg transition-colors inline-flex items-center bg-brand text-black">
                    Číst více
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>
    </div>
</a>
