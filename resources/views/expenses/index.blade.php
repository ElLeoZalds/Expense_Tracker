@extends('layouts.app')

@section('title', 'Mis Gastos - ExpenseTracker')

@section('content')
<div class="row mb-4 fade-in-up">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1 text-gradient">Gastos Registrados</h2>
                <p class="text-muted mb-0">Administra y visualiza todos tus movimientos</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary-gradient d-flex align-items-center gap-2">
                <span>+</span> Nuevo Gasto
            </a>
        </div>
    </div>
</div>

<div class="card card-custom fade-in-up delay-1">
    <div class="card-body p-0">
        @if($expenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="text-end">Monto</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $expense->date->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $expense->date->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $expense->description }}</span>
                                    @if($expense->notes)
                                        <br><small class="text-muted fst-italic"><i class="bi bi-chat-left"></i> Nota</small>
                                    @endif
                                </td>
                                <td>
                                    @if($expense->category)
                                        <span class="badge badge-soft" style="background-color: {{ $expense->category->color }}20; color: {{ $expense->category->color }};">
                                            {{ $expense->category->icon }} {{ $expense->category->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin categoría</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold {{ $expense->amount > 100 ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format($expense->amount, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('expenses.show', $expense) }}" class="btn btn-icon btn-outline-custom" title="Ver">
                                            👁️
                                        </a>
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-icon btn-outline-custom" title="Editar">
                                            ✏️
                                        </a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este gasto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-custom text-danger border-danger-subtle" title="Eliminar">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="p-4">
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state py-5">
                <div class="empty-state-icon">💸</div>
                <h4 class="fw-semibold mb-2">No hay gastos registrados</h4>
                <p class="mb-4">Comienza agregando tu primer gasto para ver el resumen aquí.</p>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary-gradient">Agregar primer gasto</a>
            </div>
        @endif
    </div>
</div>
@endsection