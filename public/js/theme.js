/**
 * ExpenseTracker - Gestor de Tema (Claro/Oscuro)
 * Persistencia en localStorage y detección de preferencia del sistema
 */

document.addEventListener('DOMContentLoaded', () => {
    const htmlElement = document.documentElement;
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    
    // Clases y atributos clave
    const DARK_THEME = 'dark';
    const LIGHT_THEME = 'light';
    const STORAGE_KEY = 'theme';

    /**
     * Obtiene el tema inicial:
     * 1. Revisa localStorage
     * 2. Si no existe, revisa la preferencia del sistema operativo
     * 3. Default: light
     */
    function getInitialTheme() {
        const storedTheme = localStorage.getItem(STORAGE_KEY);
        if (storedTheme) {
            return storedTheme;
        }
        
        // Detección de preferencia del sistema
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        return prefersDark ? DARK_THEME : LIGHT_THEME;
    }

    /**
     * Aplica el tema al documento y actualiza el ícono
     */
    function applyTheme(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);
        
        // Actualizar ícono con animación suave
        if (themeIcon) {
            themeIcon.style.transition = 'transform 0.3s ease';
            themeIcon.style.display = 'inline-block';
            themeIcon.textContent = theme === DARK_THEME ? '☀️' : '🌙';
        }
        
        // Actualizar estado del botón si es necesario
        if (themeToggleBtn) {
            themeToggleBtn.setAttribute('aria-label', `Cambiar a tema ${theme === DARK_THEME ? 'claro' : 'oscuro'}`);
        }

        // Actualizar gráfico de Chart.js si existe
        updateChartTheme(theme);
    }

    /**
     * Alterna entre temas y guarda la preferencia
     */
    function toggleTheme() {
        const currentTheme = htmlElement.getAttribute('data-bs-theme') || LIGHT_THEME;
        const newTheme = currentTheme === DARK_THEME ? LIGHT_THEME : DARK_THEME;
        
        localStorage.setItem(STORAGE_KEY, newTheme);
        applyTheme(newTheme);
    }

    /**
     * Actualiza los colores del gráfico de Chart.js según el tema
     */
    function updateChartTheme(theme) {
        if (typeof window.expensesChartInstance !== 'undefined') {
            const chart = window.expensesChartInstance;
            const textColor = theme === DARK_THEME ? '#8b949e' : '#6b7280';
            const gridColor = theme === DARK_THEME ? '#30363d' : '#e2e8f0';

            chart.options.plugins.legend.labels.color = textColor;
            chart.options.plugins.tooltip.titleColor = theme === DARK_THEME ? '#f0f6fc' : '#1f2937';
            chart.options.plugins.tooltip.bodyColor = theme === DARK_THEME ? '#f0f6fc' : '#1f2937';
            chart.options.plugins.tooltip.backgroundColor = theme === DARK_THEME ? 'rgba(22, 27, 34, 0.9)' : 'rgba(31, 41, 55, 0.9)';
            
            // Si es un gráfico de barras o línea, actualizar escalas
            if (chart.options.scales) {
                Object.values(chart.options.scales).forEach(scale => {
                    if (scale.ticks) scale.ticks.color = textColor;
                    if (scale.grid) scale.grid.color = gridColor;
                });
            }
            
            chart.update();
        }
    }

    // Inicialización
    const initialTheme = getInitialTheme();
    applyTheme(initialTheme);

    // Event Listener para el botón
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', toggleTheme);
    }

    // Escuchar cambios en la preferencia del sistema (si el usuario no ha fijado uno manual)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches ? DARK_THEME : LIGHT_THEME);
        }
    });
});