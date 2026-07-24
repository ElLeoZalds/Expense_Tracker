@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-tags"></i> Mis Categorías</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nueva Categoría
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Color</th>
                            <th class="text-center"># Gastos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td class="fs-3">{{ $category->icon ?? '📁' }}</td>
                                <td>
                                    <span class="fw-bold">{{ $category->name }}</span>
                                </td>
                                <td>
                                    @if($category->color)
                                        <span class="badge" style="background-color: {{ $category->color }}; padding: 8px 15px;">
                                            {{ $category->color }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin color</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill">{{ $category->expenses_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($category->expenses_count == 0)
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta categoría?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-outline-secondary disabled" title="Tiene gastos asociados">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="mt-3 text-muted">No hay categorías registradas.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Crear primera categoría
                </a>
            </div>
        @endif
    </div>
</div>
@endsection