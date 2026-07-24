@extends('layouts.app')

@section('title', 'Nuevo Gasto - ExpenseTracker')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <!-- Header -->
        <div class="mb-4 fade-in-up">
            <a href="{{ route('expenses.index') }}" class="text-decoration-none text-muted small mb-2 d-inline-block">
                ← Volver al listado
            </a>
            <h2 class="fw-bold text-gradient mb-1">Registrar Nuevo Gasto</h2>
            <p class="text-muted">Completa la información del movimiento</p>
        </div>

        <!-- Form Card -->
        <div class="card card-custom fade-in-up delay-1">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    
                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('description') is-invalid @enderror" 
                               id="description" 
                               name="description" 
                               value="{{ old('description') }}" 
                               placeholder="Ej: Compra en supermercado"
                               required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fila: Monto y Fecha -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Monto ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.01"
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount') }}" 
                                       placeholder="0.00"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', date('Y-m-d')) }}" 
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-4">
                        <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" 
                                name="category_id" 
                                required>
                            <option value="">Seleccionar categoría...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Notas (Opcional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Detalles adicionales...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-flex gap-3 pt-3">
                        <button type="submit" class="btn btn-primary-gradient flex-grow-1">
                            Guardar Gasto
                        </button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-custom px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection