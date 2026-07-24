@extends('layouts.app')

@section('title', 'Dashboard - ExpenseTracker')

@section('content')
<div class="row g-4 mb-4">
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center fade-in-up">
            <div>
                <h2 class="fw-bold mb-1 text-gradient">Panel de Control</h2>
                <p class="text-muted mb-0">Resumen financiero de este mes</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge badge-soft badge-soft-primary p-2">
                    {{ now()->format('F Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Card: Total Mes Actual -->
    <div class="col-12 col-md-6 col-xl-3 fade-in-up delay-1">
        <div class="card stat-card bg-gradient-primary h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-label mb-1">Gasto Mensual</p>
                        <h3 class="stat-card-value mb-0">${{ number_format($stats['month_total'], 2) }}</h3>
                    </div>
                    <div class="stat-card-icon">
                        📅
                    </div>
                </div>
                <div class="mt-3 opacity-75 small">
                    <i class="bi bi-arrow-up"></i> vs mes anterior
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Año Actual -->
    <div class="col-12 col-md-6 col-xl-3 fade-in-up delay-1">
        <div class="card stat-card bg-gradient-success h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-label mb-1">Gasto Anual</p>
                        <h3 class="stat-card-value mb-0">${{ number_format($stats['year_total'], 2) }}</h3>
                    </div>
                    <div class="stat-card-icon">
                        🗓️
                    </div>
                </div>
                <div class="mt-3 opacity-75 small">
                    Acumulado {{ now()->year }}
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Categorías -->
    <div class="col-12 col-md-6 col-xl-3 fade-in-up delay-2">
        <div class="card stat-card bg-gradient-warning h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-label mb-1">Categorías</p>
                        <h3 class="stat-card-value mb-0">{{ $stats['categories_count'] }}</h3>
                    </div>
                    <div class="stat-card-icon">
                        🏷️
                    </div>
                </div>
                <div class="mt-3 opacity-75 small">
                    Activas utilizadas
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Total Gastos -->
    <div class="col-12 col-md-6 col-xl-3 fade-in-up delay-2">
        <div class="card stat-card bg-gradient-info h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-label mb-1">Total Gastos</p>
                        <h3 class="stat-card-value mb-0">{{ $stats['expenses_count'] }}</h3>
                    </div>
                    <div class="stat-card-icon">
                        🧾
                    </div>
                </div>
                <div class="mt-3 opacity-75 small">
                    Registros históricos
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico y Lista Reciente -->
<div class="row g-4">
    <!-- Gráfico -->
    <div class="col-12 col-lg-8 fade-in-up delay-3">
        <div class="card card-custom h-100">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span>📊 Gastos por Categoría</span>
                <button class="btn btn-sm btn-outline-custom">Ver reporte completo</button>
            </div>
            <div class="card-body p-4">
                <canvas id="expensesChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Últimos Gastos -->
    <div class="col-12 col-lg-4 fade-in-up delay-3">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <span>⏱️ Recientes</span>
            </div>
            <div class="card-body p-0">
                @if(count($recentExpenses) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentExpenses as $expense)
                            <div class="list-group-item border-0 px-4 py-3 d-flex justify-content-between align-items-center hover-bg-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; background: {{ $expense->category->color ?? '#e2e8f0' }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                        {{ $expense->category->icon ?? '📦' }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold text-truncate" style="max-width: 120px;">{{ $expense->description }}</h6>
                                        <small class="text-muted">{{ $expense->date->format('d M') }}</small>
                                    </div>
                                </div>
                                <span class="fw-bold {{ $expense->amount > 100 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($expense->amount, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 text-center border-top">
                        <a href="{{ route('expenses.index') }}" class="text-decoration-none fw-semibold small">Ver todos los gastos →</a>
                    </div>
                @else
                    <div class="empty-state py-5">
                        <div class="empty-state-icon">📭</div>
                        <p class="mb-0">No hay gastos recientes</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('expensesChart').getContext('2d');
    
    // Datos desde el controlador
    const labels = {!! json_encode(array_column($stats['categories_data'], 'name')) !!};
    const data = {!! json_encode(array_column($stats['categories_data'], 'total')) !!};
    const colors = [
        '#6366f1', '#10b981', '#f59e0b', '#ef4444', 
        '#3b82f6', '#8b5cf6', '#ec4899', '#64748b'
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { family: 'Inter', size: 12, weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.9)',
                    padding: 12,
                    titleFont: { family: 'Inter', size: 13, weight: '600' },
                    bodyFont: { family: 'Inter', size: 12 },
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            label += '$' + context.raw.toLocaleString('en-US', {minimumFractionDigits: 2});
                            return label;
                        }
                    }
                }
            },
            cutout: '70%',
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
</script>
@endpush
@endsection