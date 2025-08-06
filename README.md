# Sistema de Gestión Inmobiliaria

Un sistema completo para la gestión de propiedades inmobiliarias desarrollado con Laravel 11, Inertia.js y Vue 3.

## Características

- **Gestión de Propiedades**: Crear, editar, eliminar y listar propiedades
- **Gestión de Agentes**: Administrar agentes inmobiliarios
- **Gestión de Clientes**: Administrar clientes e interesados
- **Agendamiento de Visitas**: Programar y gestionar visitas a propiedades
- **Dashboard Administrativo**: Estadísticas y resumen del sistema
- **Autenticación**: Sistema completo de autenticación con Laravel Breeze
- **Responsive**: Interfaz adaptativa con TailwindCSS

## Tecnologías Utilizadas

- **Backend**: Laravel 11
- **Frontend**: Vue 3 con Inertia.js
- **Estilos**: TailwindCSS
- **Base de datos**: MySQL
- **Build**: Vite
- **Autenticación**: Laravel Breeze
- **Validación**: Form Request Classes
- **Formateo de código**: Laravel Pint

## Requisitos del Sistema

- PHP >= 8.2
- Composer
- Node.js >= 16
- MySQL >= 8.0

## Instalación y Configuración

### 1. Instalar dependencias de PHP

```bash
composer install
```

### 2. Configurar el archivo de entorno

El archivo `.env` ya está configurado. Solo necesitas actualizar las credenciales de MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inmobiliaria_db
DB_USERNAME=root
DB_PASSWORD=tu_contraseña_mysql
```

### 3. Verificar que la base de datos existe

Asegúrate de que la base de datos `inmobiliaria_db` existe en MySQL.

### 4. Ejecutar migraciones (ya ejecutadas)

```bash
# Ya ejecutado, pero si necesitas limpiar y volver a ejecutar:
php artisan migrate:fresh --seed
```

### 5. Instalar dependencias de Node.js

```bash
npm install --legacy-peer-deps
```

### 6. Compilar assets para desarrollo

```bash
npm run dev
```

O para producción:

```bash
npm run build
```

### 7. Iniciar el servidor de desarrollo

```bash
php artisan serve
```

El sistema estará disponible en: http://localhost:8000

## Credenciales de Prueba

- **Email**: admin@inmobiliaria.com
- **Password**: password

## Funcionalidades Implementadas

### ✅ Completadas

- [x] Proyecto Laravel 11 configurado
- [x] Inertia.js + Vue 3 integrados
- [x] TailwindCSS configurado
- [x] Autenticación Laravel Breeze
- [x] Base de datos MySQL configurada
- [x] Migraciones para todas las entidades
- [x] Modelos Eloquent con relaciones
- [x] Form Request para validaciones
- [x] Controlador de Propiedades completo
- [x] Vistas Vue para propiedades (Index, Show)
- [x] Seeders con datos de ejemplo
- [x] Laravel Pint configurado

### 🚧 Pendientes

- [ ] Controladores completos para Agents, Clients, Visits
- [ ] Políticas y Gates para autorización
- [ ] Dashboard con estadísticas
- [ ] Componentes Vue adicionales (Create, Edit)
- [ ] Tests unitarios

## Estructura del Proyecto

### Modelos Principales

- **Agent**: Agentes inmobiliarios con información de contacto
- **Property**: Propiedades con detalles completos, imágenes y características
- **Client**: Clientes con niveles de interés
- **Visit**: Visitas programadas entre clientes, agentes y propiedades

### Rutas Implementadas

```php
Route::middleware('auth')->group(function () {
    Route::resource('properties', PropertyController::class);
    Route::resource('agents', AgentController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('visits', VisitController::class);
});
```

### Ejemplo de Uso

1. **Acceder al sistema**: http://localhost:8000
2. **Iniciar sesión** con las credenciales de prueba
3. **Ver propiedades**: Navega a http://localhost:8000/properties
4. **Ver detalle**: Haz clic en "Ver" en cualquier propiedad

## Comandos Útiles

```bash
# Servidor de desarrollo
php artisan serve

# Compilar assets en modo desarrollo
npm run dev

# Formatear código PHP
./vendor/bin/pint

# Limpiar caché
php artisan cache:clear

# Ver rutas
php artisan route:list

# Ejecutar seeders
php artisan db:seed
```

## Próximos Pasos de Desarrollo

1. **Completar controladores faltantes**
2. **Implementar sistema de autorización**
3. **Crear dashboard con estadísticas**
4. **Añadir componentes Vue para CRUD completo**
5. **Implementar upload de imágenes**
6. **Añadir filtros y búsqueda**

## Arquitectura y Patrones

- **MVC**: Separación clara de responsabilidades
- **Form Requests**: Validaciones centralizadas
- **Eloquent Relationships**: Relaciones bien definidas
- **Inertia.js**: SPA sin API, comunicación directa entre Laravel y Vue
- **Component-based**: Componentes Vue reutilizables

## Base de Datos

El sistema incluye datos de ejemplo:
- 3 agentes inmobiliarios
- 4 propiedades de diferentes tipos
- Usuario administrador

## Licencia

MIT License
