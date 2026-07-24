@extends('layouts.app')

@section('title', 'Lista de Gastos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-cash-coin"></i> Mis Gastos</h1>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo Gasto
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($expenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                                <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                                <td>{{ Str::limit($expense->description, 40) }}</td>
                                <td>
                                    @if($expense->category)
                                        <span class="badge" style="background-color: {{ $expense->category->color ?? '#6c757d' }}">
                                            {{ $expense->category->icon ?? '' }} {{ $expense->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin categoría</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">
                                    ${{ number_format($expense->amount, 2, '.', ',') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar este gasto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
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
            <div class="p-3">
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="mt-3 text-muted">No hay gastos registrados.</p>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Registrar primer gasto
                </a>
            </div>
        @endif
    </div>
</div>
@endsection