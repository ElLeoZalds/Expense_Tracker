@extends('layouts.app')

@section('title', 'Editar Gasto')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Gasto</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('expenses.update', $expense) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('description') is-invalid @enderror"
                               id="description"
                               name="description"
                               value="{{ old('description', $expense->description) }}"
                               placeholder="Ej: Compra en supermercado"
                               required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Monto y Fecha -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Monto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       id="amount"
                                       name="amount"
                                       value="{{ old('amount', $expense->amount) }}"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('date') is-invalid @enderror"
                                   id="date"
                                   name="date"
                                   value="{{ old('date', $expense->date) }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id"
                                name="category_id"
                                required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon ?? '' }} {{ $category->name }}
                                    @if($category->color)
                                        <span style="display:inline-block; width:12px; height:12px; background-color:{{ $category->color }}; border-radius:50%; margin-left:5px;"></span>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notas -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas (opcional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="3"
                                  placeholder="Agregar notas adicionales...">{{ old('notes', $expense->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Actualizar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection