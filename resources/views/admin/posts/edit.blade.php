@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-yellow-600">Upravit příspěvek</h2>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="title" class="block text-yellow-600 mb-2">Název příspěvku</label>
                <input type="text" name="title" id="title" 
                       class="w-full px-3 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400"
                       value="{{ old('title', $post->title) }}" required>
            </div>

            <div class="mb-4">
                <label for="category_id" class="block text-yellow-600 mb-2">Kategorie</label>
                <select name="category_id" id="category_id" 
                        class="w-full px-3 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400"
                        required>
                    <option value="">Vyberte kategorii</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="content" class="block text-yellow-600 mb-2">Obsah příspěvku</label>
                <textarea name="content" id="content" rows="10"
                          class="w-full px-3 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400"
                          required>{{ old('content', $post->content) }}</textarea>
            </div>

            @if($post->images->isNotEmpty())
                <div class="mb-4">
                    <label class="block text-yellow-600 mb-2">Současné obrázky</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($post->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="Obrázek příspěvku"
                                     class="w-full h-32 object-cover rounded-lg">
                                <form action="{{ route('admin.images.destroy', $image) }}" 
                                      method="POST"
                                      class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-400"
                                            onclick="return confirm('Opravdu chcete smazat tento obrázek?')">
                                        Smazat
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <label for="images" class="block text-yellow-600 mb-2">Přidat další obrázky</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                       class="w-full px-3 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400">
                <p class="text-gray-400 text-sm mt-1">Můžete nahrát více obrázků najednou</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.posts.index') }}" 
                   class="px-4 py-2 border border-yellow-600 rounded-lg text-yellow-600 hover:bg-yellow-600 hover:text-black transition-colors">
                    Zrušit
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-yellow-600 text-black rounded-lg hover:bg-yellow-500 transition-colors">
                    Uložit změny
                </button>
            </div>
        </form>
    </div>

    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/cfx7u9kked3nu8ucgl419fwvttlk2jn11nf9bw445wopi7eq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            skin: 'oxide-dark',
            content_css: 'dark',
            height: 500,
            menubar: false,
            branding: false,
            promotion: false,
            language: 'cs',
            relative_urls: false,
            remove_script_host: false,
            document_base_url: '{{ config('app.url') }}',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    </script>
@endsection 