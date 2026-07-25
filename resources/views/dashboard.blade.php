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

                @if($percentageChange != 0)
                    <div class="mt-2">
                        @if($percentageChange > 0)
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-up"></i> {{ abs($percentageChange) }}%
                            </span>
                        @else
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-down"></i> {{ abs($percentageChange) }}%
                            </span>
                        @endif
                        <small class="text-muted ms-1">vs mes anterior</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Total Mes Anterior -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-secondary shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted text-uppercase small">Mes Anterior</h6>
                <h2 class="display-6 fw-bold text-secondary">${{ number_format($previousMonthTotal, 2, '.', ',') }}</h2>
                <small class="text-muted">{{ \Carbon\Carbon::now()->subMonth()->format('F Y') }}</small>
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

<!-- Gráficos y Últimos Gastos -->
<div class="row g-4">
    <!-- Gráfico de Evolución Diaria (Líneas) -->
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Evolución Diaria - Mes Actual</h5>
            </div>
            <div class="card-body">
                @if(array_sum($dailyChartData) > 0)
                    <canvas id="dailyChart" height="180"></canvas>
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

        <!-- Gráfico por Categoría (Dona) -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Gastos por Categoría - Mes Actual</h5>
            </div>
            <div class="card-body">
                @if(count($chartData) > 0)
                    <canvas id="categoryChart" height="200"></canvas>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No hay gastos por categoría este mes.</p>
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
                                                <i class="bi {{ $expense->category->icon }}"></i> {{ $expense->category->name }}
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
@if(array_sum($dailyChartData) > 0 || count($chartData) > 0)
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Configuración común
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6c757d';

        // Gráfico de Evolución Diaria (Líneas)
        @if(array_sum($dailyChartData) > 0)
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyGradient = dailyCtx.createLinearGradient(0, 0, 0, 400);
        dailyGradient.addColorStop(0, 'rgba(52, 152, 219, 0.3)');
        dailyGradient.addColorStop(1, 'rgba(52, 152, 219, 0.01)');

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyChartLabels) !!},
                datasets: [{
                    label: 'Gasto ($)',
                    data: {!! json_encode($dailyChartData) !!},
                    borderColor: '#3498DB',
                    backgroundColor: dailyGradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3498DB',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Día del mes'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
        @endif

        // Gráfico por Categoría (Dona)
        @if(count($chartData) > 0)
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: {!! json_encode($chartColors) !!},
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        return {
                                            text: label + ' ($' + value.toFixed(2) + ')',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: data.datasets[0].borderColor[i],
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif
    </script>
@endif
@endpush