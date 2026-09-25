@php
    $inputClass = 'w-full px-3 py-2 bg-gray-800 rounded-lg text-white focus:outline-none border border-brand';
    $status = old('status', $post->exists && ! $post->published_at ? 'draft' : 'published');
@endphp

<div class="bg-gray-900 rounded-lg shadow-lg p-6">
    <form action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
          method="POST" enctype="multipart/form-data" id="post-form">
        @csrf
        @if($post->exists)
            @method('PUT')
        @endif

        <div class="mb-4">
            <label for="title" class="block mb-2 text-brand">Název příspěvku</label>
            <input type="text" name="title" id="title" class="{{ $inputClass }}"
                   value="{{ old('title', $post->title) }}" required maxlength="255">
        </div>

        @if($post->exists)
            <div class="mb-4">
                <label for="slug" class="block mb-2 text-brand">Adresa příspěvku</label>
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-sm whitespace-nowrap">{{ url('/posts') }}/</span>
                    <input type="text" name="slug" id="slug" class="{{ $inputClass }}"
                           value="{{ old('slug', $post->slug) }}" maxlength="255" pattern="[a-z0-9]+(-[a-z0-9]+)*">
                </div>
                <p class="text-gray-400 text-sm mt-1">Při změně přestanou fungovat dříve sdílené odkazy.</p>
            </div>
        @endif

        <div class="mb-4">
            <label for="category_id" class="block mb-2 text-brand">Kategorie</label>
            <select name="category_id" id="category_id" class="{{ $inputClass }}" required>
                <option value="">Vyberte kategorii</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="content" class="block mb-2 text-brand">Obsah příspěvku</label>
            <textarea name="content" id="content" rows="10" class="{{ $inputClass }}">{{ old('content', $post->content) }}</textarea>
        </div>

        <!-- Publikace -->
        <fieldset class="mb-6 p-4 rounded-lg border border-gray-700" x-data="{ status: '{{ $status }}' }">
            <legend class="px-2 text-brand">Publikace</legend>
            <div class="flex flex-wrap gap-6 mb-3">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="draft" x-model="status" @checked($status === 'draft') class="accent-yellow-500">
                    Koncept (neveřejný)
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="published" x-model="status" @checked($status === 'published') class="accent-yellow-500">
                    Publikovat
                </label>
            </div>
            <div x-show="status === 'published'">
                <label for="published_at" class="block mb-1 text-sm text-gray-300">Datum a čas publikace</label>
                <input type="datetime-local" name="published_at" id="published_at"
                       value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                       class="px-3 py-2 bg-gray-800 rounded-lg text-white border border-gray-600 [color-scheme:dark]">
                <p class="text-gray-400 text-sm mt-1">Nechte prázdné pro okamžité zveřejnění. Budoucí datum příspěvek naplánuje.</p>
            </div>
        </fieldset>

        <!-- Náhledový obrázek -->
        <div class="mb-6">
            <label class="block mb-2 text-brand">Náhledový obrázek</label>

            @if($post->thumbnail_path)
                <div class="mb-4" style="max-width: 300px;" id="current-thumbnail">
                    <img src="{{ asset('storage/'.$post->thumbnail_path) }}" alt="Současný náhled" class="w-full h-auto rounded-lg">
                    <p class="text-gray-400 text-sm mt-1">Současný obrázek</p>
                </div>
            @endif

            <div class="mb-4" id="thumbnail-preview" style="max-width: 300px;"></div>

            <label for="thumbnail" class="w-full flex flex-col items-center px-4 py-6 bg-gray-800 text-white rounded-lg tracking-wide cursor-pointer hover:bg-gray-700 transition-colors border-2 border-dashed border-brand">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                </svg>
                <span class="mt-2 text-base">{{ $post->thumbnail_path ? 'Nahrát nový náhledový obrázek (nahradí stávající)' : 'Vyberte náhledový obrázek' }}</span>
            </label>
            <input type="file" id="thumbnail" name="thumbnail" class="hidden" accept="image/*">
        </div>

        <!-- Současná galerie -->
        @if($post->images->isNotEmpty())
            <div class="mb-6">
                <label class="block mb-2 text-brand">Současné obrázky galerie</label>
                <p class="text-gray-400 text-sm mb-3">Pořadí změníte šipkami, popis slouží pro nevidomé a vyhledávače. Změny pořadí a popisů se uloží tlačítkem dole.</p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="gallery">
                    @foreach($post->images as $image)
                        <div class="gallery-item bg-gray-800 rounded-lg p-2" data-delete-url="{{ route('admin.images.destroy', $image) }}">
                            <input type="hidden" name="image_order[]" value="{{ $image->id }}">
                            <img src="{{ asset('storage/'.$image->image_path) }}" alt="" class="w-full h-32 object-cover rounded">
                            <input type="text" name="image_alt[{{ $image->id }}]" value="{{ old('image_alt.'.$image->id, $image->alt) }}"
                                   placeholder="Popis obrázku" maxlength="255"
                                   class="mt-2 w-full px-2 py-1 text-sm bg-gray-900 rounded border border-gray-700 text-white focus:outline-none focus:border-brand">
                            <div class="flex justify-between items-center mt-2 text-sm">
                                <div class="flex gap-1">
                                    <button type="button" class="px-2 py-1 rounded bg-gray-700 hover:bg-gray-600" data-move="-1" aria-label="Posunout doleva">←</button>
                                    <button type="button" class="px-2 py-1 rounded bg-gray-700 hover:bg-gray-600" data-move="1" aria-label="Posunout doprava">→</button>
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-400" data-delete>Smazat</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Nové obrázky -->
        <div class="mb-6">
            <label class="block mb-2 text-brand">{{ $post->images->isNotEmpty() ? 'Přidat další obrázky' : 'Galerie obrázků' }}</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4" id="image-preview"></div>
            <label for="images" id="images-drop" class="w-full flex flex-col items-center px-4 py-6 bg-gray-800 text-white rounded-lg tracking-wide cursor-pointer hover:bg-gray-700 transition-colors border-2 border-dashed border-brand">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                </svg>
                <span class="mt-2 text-base">Přetáhněte sem obrázky nebo klikněte pro výběr</span>
                <span class="text-gray-400 text-sm">JPG, PNG, WebP nebo GIF, max. 6 MB. Velké fotky se automaticky zmenší.</span>
            </label>
            <input type="file" id="images" name="images[]" class="hidden" multiple accept="image/*">
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 rounded-lg border transition-colors border-brand text-brand">
                Zrušit
            </a>
            <button type="submit" class="px-4 py-2 rounded-lg transition-colors bg-brand text-black">
                {{ $post->exists ? 'Uložit změny' : 'Vytvořit příspěvek' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
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
            document_base_url: @json(config('app.url')),
        });

        document.getElementById('post-form').addEventListener('submit', function (e) {
            tinymce.triggerSave();
            if (!document.getElementById('content').value.trim()) {
                e.preventDefault();
                alert('Vyplňte obsah příspěvku.');
            }
        });

        const csrfToken = @json(csrf_token());

        // Současná galerie: řazení a mazání
        const gallery = document.getElementById('gallery');
        if (gallery) {
            gallery.addEventListener('click', function (e) {
                const item = e.target.closest('.gallery-item');
                if (!item) return;

                const move = e.target.closest('[data-move]');
                if (move) {
                    const sibling = move.dataset.move === '-1' ? item.previousElementSibling : item.nextElementSibling;
                    if (sibling) {
                        move.dataset.move === '-1' ? sibling.before(item) : sibling.after(item);
                    }
                    return;
                }

                if (e.target.closest('[data-delete]') && confirm('Opravdu chcete smazat tento obrázek?')) {
                    fetch(item.dataset.deleteUrl, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    }).then(response => {
                        if (!response.ok) throw new Error(response.status);
                        item.remove();
                    }).catch(() => alert('Obrázek se nepodařilo smazat.'));
                }
            });
        }

        // Nové obrázky: náhledy s možností odebrat jednotlivé soubory a přetažení myší
        const imagesInput = document.getElementById('images');
        const imagesPreview = document.getElementById('image-preview');
        const imagesDrop = document.getElementById('images-drop');
        let selectedImages = [];

        function syncImages() {
            const transfer = new DataTransfer();
            selectedImages.forEach(file => transfer.items.add(file));
            imagesInput.files = transfer.files;

            imagesPreview.innerHTML = '';
            selectedImages.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'relative group';
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'w-full h-32 object-cover rounded-lg';
                img.alt = '';
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'absolute top-1 right-1 bg-black bg-opacity-70 text-white rounded-full w-7 h-7 hover:text-red-500';
                remove.setAttribute('aria-label', 'Odebrat obrázek');
                remove.textContent = '✕';
                remove.addEventListener('click', () => {
                    selectedImages.splice(index, 1);
                    syncImages();
                });
                div.append(img, remove);
                imagesPreview.appendChild(div);
            });
        }

        function addImages(files) {
            selectedImages = selectedImages.concat([...files].filter(f => f.type.startsWith('image/')));
            syncImages();
        }

        imagesInput.addEventListener('change', function () {
            // Výběr v dialogu nahradí obsah inputu, proto přidáváme k dříve vybraným.
            const files = [...this.files].filter(f => !selectedImages.includes(f));
            addImages(files);
        });

        ['dragenter', 'dragover'].forEach(type => imagesDrop.addEventListener(type, e => {
            e.preventDefault();
            imagesDrop.classList.add('bg-gray-700');
        }));
        ['dragleave', 'drop'].forEach(type => imagesDrop.addEventListener(type, () => imagesDrop.classList.remove('bg-gray-700')));
        imagesDrop.addEventListener('drop', e => {
            e.preventDefault();
            addImages(e.dataTransfer.files);
        });

        // Náhledový obrázek
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const currentThumbnail = document.getElementById('current-thumbnail');

        thumbnailInput.addEventListener('change', function () {
            thumbnailPreview.innerHTML = '';
            if (!this.files[0]) return;

            if (currentThumbnail) currentThumbnail.style.display = 'none';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(this.files[0]);
            img.className = 'w-full h-auto rounded-lg';
            img.alt = 'Náhled nového obrázku';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'absolute top-1 right-1 bg-black bg-opacity-70 text-white rounded-full w-7 h-7 hover:text-red-500';
            remove.setAttribute('aria-label', 'Zrušit výběr');
            remove.textContent = '✕';
            remove.addEventListener('click', () => {
                thumbnailPreview.innerHTML = '';
                thumbnailInput.value = '';
                if (currentThumbnail) currentThumbnail.style.display = 'block';
            });
            const note = document.createElement('p');
            note.className = 'text-yellow-500 text-sm mt-1';
            note.textContent = 'Nově vybraný obrázek k uložení';
            wrapper.append(img, remove, note);
            thumbnailPreview.appendChild(wrapper);
        });
    </script>
@endpush
