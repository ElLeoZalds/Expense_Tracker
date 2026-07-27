@extends('layouts.app')

@section('title', 'Detalle de la Categoría')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-tag"></i> Detalle de la Categoría</h5>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Nombre</h6>
                        <p class="fs-5">{{ $category->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Ícono</h6>
                        <p class="fs-5">{{ $category->icon ?? 'Sin ícono' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted">Color</h6>
                    @if($category->color)
                        <span class="badge" style="background-color: {{ $category->color }}; padding: 10px 15px;">
                            {{ $category->color }}
                        </span>
                    @else
                        <span class="text-muted">Sin color</span>
                    @endif
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a la lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection