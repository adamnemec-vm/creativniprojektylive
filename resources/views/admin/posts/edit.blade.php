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

            <!-- Náhledový obrázek -->
            <div class="mb-6">
                <label class="block text-yellow-600 mb-2">Náhledový obrázek</label>
                
                @if($post->thumbnail_path)
                    <div class="mb-4 relative group" style="max-width: 300px;" id="current-thumbnail">
                        <img src="{{ asset('storage/' . $post->thumbnail_path) }}" alt="Současný náhled" class="w-full h-auto rounded-lg">
                        <p class="text-gray-400 text-sm mt-1">Současný obrázek</p>
                    </div>
                @endif
                
                <div class="mb-4" id="thumbnail-preview">
                    <!-- JavaScript bude sem vkládat náhled -->
                </div>

                <div class="flex items-center justify-center w-full">
                    <label for="thumbnail" class="w-full flex flex-col items-center px-4 py-6 bg-gray-800 text-white rounded-lg tracking-wide cursor-pointer hover:bg-gray-700 transition-colors"
                           style="border: 2px dashed #fed501;">
                        <svg class="w-8 h-8 text-yellow-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                        </svg>
                        <span class="mt-2 text-base text-yellow-600">Nahrát nový náhledový obrázek (nahradí stávající)</span>
                    </label>
                    <input type="file" id="thumbnail" name="thumbnail" class="hidden" accept="image/*">
                </div>
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
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" 
                                            class="text-red-500 hover:text-red-400 delete-image-btn"
                                            data-image-id="{{ $image->id }}"
                                            onclick="deleteImage({{ $image->id }}, this)">
                                        Smazat
                                    </button>
                                </div>
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
        
        // Zajistí, že obsah TinyMCE bude odeslán s formulářem
        document.querySelector('form').addEventListener('submit', function() {
            // Uloží obsah editoru do textarey před odesláním formuláře
            tinymce.triggerSave();
        });

        // Funkce pro mazání obrázků pomocí AJAX
        function deleteImage(imageId, buttonElement) {
            if (confirm('Opravdu chcete smazat tento obrázek?')) {
                fetch('{{ route('admin.images.destroy', '') }}/' + imageId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Odstraníme obrázek z DOM
                        buttonElement.closest('.relative.group').remove();
                        console.log('Obrázek byl úspěšně smazán');
                    } else {
                        alert('Chyba při mazání obrázku');
                    }
                })
                .catch(error => {
                    console.error('Chyba při mazání obrázku:', error);
                    alert('Chyba při mazání obrázku');
                });
            }
        }

        // Náhledový obrázek
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const currentThumbnail = document.getElementById('current-thumbnail');

        thumbnailInput.addEventListener('change', function() {
            thumbnailPreview.innerHTML = '';
            
            if (this.files[0]) {
                if (currentThumbnail) {
                    currentThumbnail.style.display = 'none'; // Skryje starý obrázek
                }
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.style.maxWidth = '300px';
                    
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-auto object-cover rounded-lg" alt="Náhled nového obrázku">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <button type="button" class="text-white hover:text-red-500" onclick="clearThumbnail()">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-yellow-500 text-sm mt-1">Nově vybraný obrázek k uložení</p>
                    `;
                    
                    thumbnailPreview.appendChild(div);
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        function clearThumbnail() {
            thumbnailPreview.innerHTML = '';
            thumbnailInput.value = '';
            if (currentThumbnail) {
                currentThumbnail.style.display = 'block'; // Znovu zobrazí starý obrázek
            }
        }
    </script>
@endsection