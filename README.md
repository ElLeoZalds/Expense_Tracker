# Sistema de Gestión de Gastos Personales

Una aplicación web moderna para el seguimiento y gestión de gastos personales, construida con Laravel 11.

## 📋 Descripción

Este sistema permite a los usuarios:

- **Registrar gastos** con descripción, monto, fecha y categoría
- **Gestionar categorías** personalizadas para organizar los gastos
- **Establecer presupuestos** mensuales por categoría
- **Exportar datos** a archivos descargables
- **Auditoría completa** de todas las operaciones realizadas
- **Visualizar estadísticas** en un dashboard interactivo

## 🚀 Características Principales

- ✅ Autenticación de usuarios segura
- ✅ CRUD completo de gastos y categorías
- ✅ Sistema de presupuestos mensuales
- ✅ Exportación de datos a archivos
- ✅ Logs de auditoría detallados (quién, qué, cuándo)
- ✅ Soft deletes para recuperación de datos eliminados
- ✅ Rate limiting para protección contra abuso
- ✅ Verificación de propiedad de recursos
- ✅ Interfaz responsive con Tailwind CSS

## 📦 Requisitos

- PHP ^8.2
- Composer
- Node.js y NPM
- Base de datos (MySQL, PostgreSQL, SQLite, etc.)

## ⚙️ Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd <directorio-del-proyecto>
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar la base de datos** en el archivo `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tu_base_de_datos
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

5. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

6. **Instalar dependencias de frontend y compilar assets:**
   ```bash
   npm install
   npm run build
   ```

7. **Iniciar el servidor de desarrollo:**
   ```bash
   php artisan serve
   ```

La aplicación estará disponible en `http://localhost:8000`

## 🛠️ Comandos Útiles

### Desarrollo
```bash
# Iniciar todos los servicios (servidor, cola, logs, vite)
composer run dev

# Solo servidor
php artisan serve

# Solo cola de trabajos
php artisan queue:work

# Compilar assets en modo desarrollo
npm run dev
```

### Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Resetear base de datos
php artisan migrate:fresh --seed
```

### Testing
```bash
# Ejecutar tests
composer run test

# Ejecutar tests con coverage
php artisan test --coverage
```

## 📁 Estructura del Proyecto

```
├── app/
│   ├── Http/Controllers/     # Controladores (Expense, Category, Dashboard)
│   ├── Models/               # Modelos (Expense, Category, Budget, User, AuditLog, FileExport)
│   ├── Services/             # Lógica de negocio
│   └── Policies/             # Políticas de autorización
├── database/
│   ├── migrations/           # Migraciones de base de datos
│   └── seeders/              # Seeders para datos de prueba
├── resources/
│   ├── views/                # Plantillas Blade
│   ├── css/                  # Estilos Tailwind
│   └── js/                   # JavaScript
├── routes/
│   ├── web.php               # Rutas web
│   └── auth.php              # Rutas de autenticación
└── tests/                    # Tests automatizados
```

## 🗄️ Modelo de Datos

### Tablas Principales

- **users**: Usuarios del sistema
- **expenses**: Gastos registrados (con soft delete)
- **categories**: Categorías de gastos (con soft delete)
- **budgets**: Presupuestos mensuales por categoría
- **file_exports**: Registro de archivos exportados
- **audit_logs**: Log de auditoría de todas las operaciones
- **cache & jobs**: Tablas del sistema Laravel

## 🔐 Seguridad

- Middleware de autenticación en todas las rutas protegidas
- Rate limiting configurado por tipo de operación:
  - 60 requests/minuto para operaciones de lectura
  - 20 requests/minuto para creación/actualización
  - 10 requests/minuto para eliminaciones
- Verificación de propiedad de recursos
- Protección CSRF habilitada

## 🧪 Testing

El proyecto incluye tests automatizados. Para ejecutarlos:

```bash
composer run test
```

## 📝 Licencia

Este proyecto está licenciado bajo la [Licencia MIT](LICENSE).

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Haz fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📞 Soporte

Si encuentras algún bug o tienes sugerencias, por favor crea un issue en el repositorio.

---

Hecho con ❤️ usando Laravel 11
