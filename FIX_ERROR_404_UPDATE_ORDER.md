# Fix Error 404 - Update Order Route

## Problema

Al intentar reordenar proyectos en el módulo de proyectos (opción "Ordenar"), se generaba un error 404:

```
POST http://localhost:8000/projects/update-order 404 (Not Found)
```

Esto ocurría al arrastrar y soltar un proyecto para cambiar su posición en la lista.

## Causa Raíz

El problema era el **orden de definición de rutas** en `routes/web.php`.

Laravel procesa las rutas en el orden en que están definidas. La ruta dinámica `/projects/{project}` estaba definida **ANTES** que la ruta específica `/projects/update-order`:

```php
// ANTES (INCORRECTO):
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');  // Línea 99

Route::middleware('permission:projects:edit')->group(function () {
    Route::post('/projects/update-order', [ProjectController::class, 'updateOrder'])->name('projects.updateOrder');  // Línea 105
});
```

Cuando Laravel intentaba hacer match con `/projects/update-order`, primero encontraba la ruta `/projects/{project}` y trataba de usar "update-order" como el valor del parámetro `{project}`, causando que la ruta correcta nunca fuera alcanzada.

## Solución Aplicada

Reorganizar las rutas para que las **rutas específicas** estén **ANTES** de las **rutas con parámetros dinámicos**:

```php
// DESPUÉS (CORRECTO):
Route::middleware('permission:projects:edit')->group(function () {
    // Specific routes must come BEFORE dynamic parameter routes
    Route::post('/projects/update-order', [ProjectController::class, 'updateOrder'])->name('projects.updateOrder');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update.post');
    Route::patch('/projects/{project}/toggle-visibility', [ProjectController::class, 'toggleVisibility'])->name('projects.toggleVisibility');
});

// Dynamic parameter routes come AFTER specific routes
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
```

### Cambios realizados:

1. **Movida la ruta `/projects/update-order`** al principio del grupo de rutas de edición (antes de cualquier ruta con parámetros dinámicos)
2. **Movida la ruta `/projects/{project}`** fuera del grupo de edición y después de todas las rutas específicas
3. **Agregados comentarios** para documentar el orden correcto y prevenir futuros errores
4. **Limpiado el cache de rutas** con `php artisan route:clear && php artisan route:cache`

## Beneficio

Ahora la funcionalidad de reordenamiento de proyectos funciona correctamente:
- Los usuarios pueden arrastrar y soltar proyectos para cambiar su orden
- La petición POST a `/projects/update-order` se procesa correctamente
- El orden se guarda en la base de datos sin errores

## Verificación

Para verificar que la ruta está correctamente registrada:

```bash
php artisan route:list --name=updateOrder
```

Debería mostrar:
```
POST  projects/update-order  projects.updateOrder › ProjectController@updateOrder
```

## Nota Importante

**Las rutas específicas siempre deben ir ANTES de las rutas con parámetros dinámicos.**

Ejemplo:
```php
// ✅ CORRECTO
Route::post('/resources/update-order', ...);  // Específica primero
Route::get('/resources/{resource}', ...);     // Dinámica después

// ❌ INCORRECTO
Route::get('/resources/{resource}', ...);     // Dinámica primero
Route::post('/resources/update-order', ...);  // Específica después - NUNCA SE ALCANZARÁ
```

---

**Fecha de creación:** 2025-11-24
**Autor:** Claude Code
**Rama:** fix/error-404
