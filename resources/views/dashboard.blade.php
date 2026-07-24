@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h1>

<!-- Cards de estadísticas -->
<div class="row g-4 mb-4">
    <!-- Total Mes Actual -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-primary shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted text-uppercase small">Gasto del Mes</h6>
                <h2 class="display-6 fw-bold text-primary">${{ number_format($totalMonth, 2, '.', ',') }}</h2>
                <small class="text-muted">{{ \Carbon\Carbon::now()->format('F Y') }}</small>
            </div>
        </div>
    </div>

    <!-- Total Año Actual -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-success shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted text-uppercase small">Gasto del Año</h6>
                <h2 class="display-6 fw-bold text-success">${{ number_format($totalYear, 2, '.', ',') }}</h2>
                <small class="text-muted">{{ date('Y') }}</small>
            </div>
        </div>
    </div>

    <!-- Total Categorías -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-info shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted text-uppercase small">Categorías</h6>
                <h2 class="display-6 fw-bold text-info">{{ $totalCategories }}</h2>
                <small class="text-muted">Categorías activas</small>
            </div>
        </div>
    </div>

    <!-- Total Gastos -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-warning shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted text-uppercase small">Total Gastos</h6>
                <h2 class="display-6 fw-bold text-warning">{{ $totalExpenses }}</h2>
                <small class="text-muted">Gastos registrados</small>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico y Últimos Gastos -->
<div class="row g-4">
    <!-- Espacio para gráfico -->
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Gastos por Categoría (Año)</h5>
            </div>
            <div class="card-body">
                @if(count($chartData) > 0)
                    <canvas id="expensesChart" height="200"></canvas>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No hay datos para mostrar el gráfico.</p>
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Registrar primer gasto
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Últimos 5 Gastos -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Últimos Gastos</h5>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                @if($recentExpenses->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($recentExpenses as $expense)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ Str::limit($expense->description, 25) }}</div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                        @if($expense->category)
                                            · <span style="color: {{ $expense->category->color ?? '#6c757d' }}">
                                                {{ $expense->category->name }}
                                            </span>
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill">${{ number_format($expense->amount, 2, '.', ',') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No hay gastos recientes.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(count($chartData) > 0)
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('expensesChart').getContext('2d');

        // Colores para las categorías
        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A',
            '#98D8C8', '#F7DC6F', '#BB8FCE', '#95A5A6',
            '#E74C3C', '#3498DB', '#2ECC71', '#F39C12'
        ];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: colors.slice(0, {!! count($chartLabels) !!}),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                }
            }
        });
    </script>
@endif
@endpush