@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold" style="color: #fed501;">Nový příspěvek</h2>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6">
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
            @csrf
            
            <div class="mb-4">
                <label for="title" class="block mb-2" style="color: #fed501;">Název příspěvku</label>
                <input type="text" name="title" id="title" 
                       class="w-full px-3 py-2 bg-gray-800 rounded-lg text-white focus:outline-none"
                       style="border: 1px solid #fed501;"
                       value="{{ old('title') }}" required>
            </div>

            <div class="mb-4">
                <label for="category_id" class="block mb-2" style="color: #fed501;">Kategorie</label>
                <select name="category_id" id="category_id" 
                        class="w-full px-3 py-2 bg-gray-800 rounded-lg text-white focus:outline-none"
                        style="border: 1px solid #fed501;" required>
                    <option value="">Vyberte kategorii</option>
                    @foreach(\App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="content" class="block mb-2" style="color: #fed501;">Obsah příspěvku</label>
                <textarea name="content" id="content" rows="6" 
                          class="w-full px-3 py-2 bg-gray-800 rounded-lg text-white focus:outline-none"
                          style="border: 1px solid #fed501;" required>{{ old('content') }}</textarea>
            </div>

            <!-- Galerie -->
            <div class="mb-6">
                <label class="block mb-2" style="color: #fed501;">Galerie obrázků</label>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4" id="image-preview">
                    <!-- JavaScript bude sem vkládat náhledy -->
                </div>

                <div class="flex items-center justify-center w-full">
                    <label for="images" class="w-full flex flex-col items-center px-4 py-6 bg-gray-800 text-white rounded-lg tracking-wide cursor-pointer hover:bg-gray-700 transition-colors"
                           style="border: 2px dashed #fed501;">
                        <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                        </svg>
                        <span class="mt-2 text-base">Přetáhněte sem obrázky nebo klikněte pro výběr</span>
                    </label>
                    <input type="file" id="images" name="images[]" class="hidden" multiple accept="image/*">
                </div>
            </div>

            <!-- Náhledový obrázek -->
            <div class="mb-6">
                <label class="block mb-2" style="color: #fed501;">Náhledový obrázek</label>
                
                <div class="mb-4" id="thumbnail-preview">
                    <!-- JavaScript bude sem vkládat náhled -->
                </div>

                <div class="flex items-center justify-center w-full">
                    <label for="thumbnail" class="w-full flex flex-col items-center px-4 py-6 bg-gray-800 text-white rounded-lg tracking-wide cursor-pointer hover:bg-gray-700 transition-colors"
                           style="border: 2px dashed #fed501;">
                        <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                        </svg>
                        <span class="mt-2 text-base">Vyberte náhledový obrázek</span>
                    </label>
                    <input type="file" id="thumbnail" name="thumbnail" class="hidden" accept="image/*">
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.posts.index') }}" 
                   class="px-4 py-2 rounded-lg border transition-colors"
                   style="border-color: #fed501; color: #fed501;">
                    Zrušit
                </a>
                <button type="submit" 
                        class="px-4 py-2 rounded-lg transition-colors"
                        style="background-color: #fed501; color: black;">
                    Vytvořit příspěvek
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

    <!-- JavaScript pro náhledy obrázků -->
    <script>
        const input = document.getElementById('images');
        const preview = document.getElementById('image-preview');

        input.addEventListener('change', function() {
            preview.innerHTML = ''; // Vyčistit předchozí náhledy
            
            for (const file of this.files) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg" alt="Náhled">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <button type="button" class="text-white hover:text-red-500" onclick="this.parentElement.parentElement.remove()">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        `;
                        
                        preview.appendChild(div);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        });

        // Náhledový obrázek
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');

        thumbnailInput.addEventListener('change', function() {
            thumbnailPreview.innerHTML = '';
            
            if (this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-48 object-cover rounded-lg" alt="Náhled">
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <button type="button" class="text-white hover:text-red-500" onclick="clearThumbnail()">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    thumbnailPreview.appendChild(div);
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        function clearThumbnail() {
            thumbnailPreview.innerHTML = '';
            thumbnailInput.value = '';
        }
    </script>
@endsection 