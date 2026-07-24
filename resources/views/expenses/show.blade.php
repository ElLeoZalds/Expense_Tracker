@extends('layouts.app')

@section('title', 'Detalle del Gasto')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Detalle del Gasto</h5>
                <div>
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Fecha</h6>
                        <p class="fs-5">{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Monto</h6>
                        <p class="fs-4 fw-bold text-primary">${{ number_format($expense->amount, 2, '.', ',') }}</p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted">Descripción</h6>
                    <p class="fs-5">{{ $expense->description }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted">Categoría</h6>
                    @if($expense->category)
                        <span class="badge fs-6" style="background-color: {{ $expense->category->color ?? '#6c757d' }}; padding: 10px 15px;">
                            <span class="fs-4">{{ $expense->category->icon ?? '' }}</span>
                            {{ $expense->category->name }}
                        </span>
                    @else
                        <span class="text-muted">Sin categoría</span>
                    @endif
                </div>

                @if($expense->notes)
                    <div class="mb-3">
                        <h6 class="text-muted">Notas</h6>
                        <p class="text-muted">{{ $expense->notes }}</p>
                    </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a la lista
                    </a>
                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                          onsubmit="return confirm('¿Está seguro de eliminar este gasto? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar Gasto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection