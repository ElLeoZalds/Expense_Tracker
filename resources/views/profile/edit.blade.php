@extends('layouts.app')

@section('title', 'Perfil - ExpenseTracker')

@section('content')
<div class="row mb-4 fade-in-up">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1 text-gradient">Configuración de Perfil</h2>
                <p class="text-muted mb-0">Gestiona tu información personal y seguridad</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Actualizar Información -->
    <div class="col-12 col-lg-6 fade-in-up delay-1">
        <div class="card card-custom h-100">
            <div class="card-header-custom d-flex align-items-center gap-2">
                <span>👤</span>
                <span>Información del Perfil</span>
            </div>
            <div class="card-body p-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Actualizar Contraseña -->
    <div class="col-12 col-lg-6 fade-in-up delay-2">
        <div class="card card-custom h-100">
            <div class="card-header-custom d-flex align-items-center gap-2">
                <span>🔒</span>
                <span>Seguridad</span>
            </div>
            <div class="card-body p-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Eliminar Cuenta -->
    <div class="col-12 fade-in-up delay-3">
        <div class="card card-custom border-danger-subtle">
            <div class="card-header-custom d-flex align-items-center gap-2 text-danger">
                <span>⚠️</span>
                <span>Zona de Peligro</span>
            </div>
            <div class="card-body p-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection