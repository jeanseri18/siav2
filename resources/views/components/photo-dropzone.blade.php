{{--
    Upload image drag & drop avec prévisualisation.
    @param string $name
    @param string|null $id
    @param string $label
    @param string|null $currentUrl Image actuelle
    @param string $help Texte d'aide
    @param string $accept
    @param bool $required
    @param bool $circle Aperçu circulaire (photo profil)
    @param string|null $errorKey Clé d'erreur validation (défaut = name)
--}}
@props([
    'name' => 'photo',
    'id' => null,
    'label' => 'Photo',
    'currentUrl' => null,
    'help' => 'Formats : JPG, PNG, GIF, WEBP. Glissez-déposez ou cliquez pour sélectionner.',
    'accept' => 'image/*',
    'required' => false,
    'circle' => false,
    'errorKey' => null,
])

@php
    $inputId = $id ?? $name;
    $errorField = $errorKey ?? $name;
    $hasError = $errors->has($errorField);
@endphp

<div class="photo-dropzone-field app-form-group mb-3" data-photo-dropzone>
    @if($label)
        <label for="{{ $inputId }}" class="app-form-label form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="photo-dropzone {{ $hasError ? 'is-invalid' : '' }} {{ $circle ? 'photo-dropzone--circle' : '' }}"
         data-dropzone-area
         tabindex="0"
         role="button"
         aria-label="{{ $label }}">
        <input type="file"
               class="photo-dropzone__input @error($errorField) is-invalid @enderror"
               id="{{ $inputId }}"
               name="{{ $name }}"
               accept="{{ $accept }}"
               @if($required) required @endif
               data-dropzone-input>

        <div class="photo-dropzone__preview {{ $currentUrl ? '' : 'd-none' }}" data-dropzone-preview>
            <img @if($currentUrl) src="{{ $currentUrl }}" @endif
                 alt="Aperçu"
                 class="photo-dropzone__img {{ $circle ? 'photo-dropzone__img--circle' : '' }}"
                 data-dropzone-img>
            <button type="button" class="photo-dropzone__clear" data-dropzone-clear title="Retirer l'image">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="photo-dropzone__placeholder {{ $currentUrl ? 'd-none' : '' }}" data-dropzone-placeholder>
            <div class="photo-dropzone__icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <p class="photo-dropzone__text mb-1">
                <strong>Glissez-déposez</strong> votre image ici
            </p>
            <p class="photo-dropzone__hint mb-0">ou cliquez pour parcourir</p>
        </div>
    </div>

    @error($errorField)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if($help)
        <div class="app-form-text form-text">{{ $help }}</div>
    @endif
</div>

@once
<style>
.photo-dropzone-field {
    position: relative;
    display: block;
    width: 100%;
    clear: both;
    margin-top: 0.5rem;
    margin-bottom: 1rem;
    z-index: 1;
}
.photo-dropzone {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 180px;
    height: auto !important;
    max-height: none !important;
    padding: 1.25rem;
    border: 2px dashed #033d71;
    border-radius: 1rem;
    background: #fff;
    cursor: pointer;
    transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
    box-sizing: border-box;
}
.photo-dropzone:hover,
.photo-dropzone.is-dragover {
    border-color: #033d71;
    background: rgba(3, 61, 113, 0.04);
    box-shadow: 0 0 0 0.2rem rgba(3, 61, 113, 0.12);
}
.photo-dropzone.is-invalid {
    border-color: #dc3545;
}
.photo-dropzone__input {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    border: 0 !important;
    min-height: 0 !important;
}
.photo-dropzone__placeholder {
    text-align: center;
    color: #033d71;
    pointer-events: none;
}
.photo-dropzone__icon {
    font-size: 2.25rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}
.photo-dropzone__text {
    font-size: 0.95rem;
    color: #033d71;
}
.photo-dropzone__hint {
    font-size: 0.8rem;
    color: #6c757d;
}
.photo-dropzone__preview {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.photo-dropzone__img {
    max-width: 100%;
    max-height: 220px;
    object-fit: contain;
    border-radius: 0.75rem;
    border: 1px solid #dee2e6;
    background: #f8f9fa;
}
.photo-dropzone__img--circle {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 50%;
}
.photo-dropzone__clear {
    position: absolute;
    top: -0.35rem;
    right: 0.5rem;
    width: 2rem;
    height: 2rem;
    border: none;
    border-radius: 50%;
    background: #dc3545;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 2;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
.photo-dropzone__clear:hover {
    background: #bb2d3b;
}
</style>
@endonce

@once
@push('scripts')
<script>
(function () {
    function initPhotoDropzone(root) {
        if (root.dataset.ready === '1') return;
        root.dataset.ready = '1';

        const area = root.querySelector('[data-dropzone-area]');
        const input = root.querySelector('[data-dropzone-input]');
        const preview = root.querySelector('[data-dropzone-preview]');
        const img = root.querySelector('[data-dropzone-img]');
        const placeholder = root.querySelector('[data-dropzone-placeholder]');
        const clearBtn = root.querySelector('[data-dropzone-clear]');
        if (!area || !input) return;

        function showPreview(file) {
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }

        function clearPreview() {
            input.value = '';
            img.removeAttribute('src');
            preview.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }

        area.addEventListener('click', function (e) {
            if (e.target.closest('[data-dropzone-clear]')) return;
            input.click();
        });

        area.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                input.click();
            }
        });

        ['dragenter', 'dragover'].forEach(function (evt) {
            area.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                area.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (evt) {
            area.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                area.classList.remove('is-dragover');
            });
        });

        area.addEventListener('drop', function (e) {
            const files = e.dataTransfer && e.dataTransfer.files;
            if (!files || !files.length) return;
            const file = files[0];
            if (!file.type.startsWith('image/')) {
                alert('Veuillez déposer une image (JPG, PNG, GIF, WEBP).');
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showPreview(file);
        });

        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                showPreview(input.files[0]);
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clearPreview();
            });
        }
    }

    function boot() {
        document.querySelectorAll('[data-photo-dropzone]').forEach(initPhotoDropzone);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
</script>
@endpush
@endonce
