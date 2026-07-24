@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Categoría</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $category->name) }}"
                               placeholder="Ej: Comida"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Icono (emoji) -->
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icono (emoji)</label>
                        <input type="text"
                               class="form-control @error('icon') is-invalid @enderror"
                               id="icon"
                               name="icon"
                               value="{{ old('icon', $category->icon) }}"
                               placeholder="Ej: 🍽️"
                               maxlength="10">
                        <div class="form-text">Puede copiar y pegar un emoji.</div>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <div class="input-group">
                            <input type="color"
                                   class="form-control form-control-color @error('color') is-invalid @enderror"
                                   id="color"
                                   name="color"
                                   value="{{ old('color', $category->color ?? '#6c757d') }}"
                                   title="Seleccione un color">
                            <input type="text"
                                   class="form-control @error('color') is-invalid @enderror"
                                   id="colorText"
                                   value="{{ old('color', $category->color ?? '#6c757d') }}"
                                   placeholder="#6c757d"
                                   maxlength="7">
                        </div>
                        <div class="form-text">Formato hexadecimal (ej: #FF6B6B).</div>
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sincronizar input color con el texto
    document.getElementById('color').addEventListener('input', function(e) {
        document.getElementById('colorText').value = e.target.value;
    });

    document.getElementById('colorText').addEventListener('input', function(e) {
        let value = e.target.value;
        if (!value.startsWith('#')) {
            value = '#' + value;
        }
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            document.getElementById('color').value = value;
        }
    });
</script>
@endpush
@endsection