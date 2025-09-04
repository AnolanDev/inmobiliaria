# 🚀 Guía de Actualización de Producción - Tierra Soñada

**Versión:** 1.1.0  
**Fecha:** Septiembre 2025  
**Cambios:** Fix de permisos de usuario y menú inteligente  

---

## 📋 **Resumen de cambios**

Esta actualización soluciona:
- ✅ Error 403 al crear usuarios desde panel de administración
- ✅ Auto-login no deseado después de registrar usuarios  
- ✅ Menú de navegación inteligente (se expande según sección activa)
- ✅ Detección mejorada de super administradores
- ✅ Sistema de logs para diagnóstico de permisos

---

## ⚠️ **IMPORTANTE - Leer antes de comenzar**

1. **Hacer backup de la base de datos** antes de iniciar
2. **Tiempo estimado:** 10-15 minutos
3. **Requiere acceso SSH** al servidor de producción
4. **Requiere permisos** de MySQL para ALTER TABLE

---

## 🔧 **Paso 1: Actualizar código desde Git**

```bash
# Navegar al directorio del proyecto Laravel (donde está el archivo artisan)
cd /ruta/a/tu/proyecto/inmobiliaria

# Verificar rama actual
git branch

# Actualizar código desde repositorio
git pull origin main

# Verificar que se descargaron los cambios
git log --oneline -3
```

**✅ Verificación:** Deberías ver commits recientes sobre "navigation menu" y "user permission"

---

## 🗄️ **Paso 2: Actualizar base de datos (CRÍTICO)**

### 2.1 Conectar a MySQL
```bash
mysql -u serboome_inmobiliaria -p serboome_inmobiliaria
```

### 2.2 Ejecutar comandos SQL
```sql
-- Agregar columna is_super_admin a la tabla users
ALTER TABLE users ADD COLUMN is_super_admin BOOLEAN DEFAULT FALSE;

-- Hacer super admin a los usuarios que necesiten acceso completo
-- IMPORTANTE: Cambiar emails por los correctos
UPDATE users SET is_super_admin = 1 WHERE email IN (
    'goliverar@gmail.com', 
    'comercial@tierrasonada.com', 
    'gerencia@tierrasonada.com'
);

-- Verificar que se aplicó correctamente
SELECT id, name, email, is_super_admin FROM users;

-- Salir de MySQL
EXIT;
```

**✅ Verificación:** La consulta SELECT debe mostrar `is_super_admin = 1` para los usuarios indicados.

---

## 🧹 **Paso 3: Limpiar cachés de Laravel**

```bash
# Limpiar todos los cachés existentes
php artisan config:clear
php artisan route:clear  
php artisan cache:clear
php artisan view:clear

# Regenerar cachés optimizados para producción
php artisan config:cache
php artisan route:cache
```

**✅ Verificación:** Todos los comandos deben ejecutarse sin errores.

---

## 🔍 **Paso 4: Verificar instalación**

### 4.1 Ejecutar diagnóstico
```bash
php artisan diagnose:permissions
```

**Resultado esperado:**
```
=== DIAGNÓSTICO DE PERMISOS TIERRA SOÑADA ===

1. VERIFICANDO TABLAS:
✅ users: X registros
✅ roles: 6 registros
✅ permissions: 73 registros
✅ role_permissions: 114 registros
✅ user_roles: X registros

2. USUARIOS:
- Nombre Usuario (email@ejemplo.com) - Super Administrador

3. ROLES DISPONIBLES:
- Super Administrador (slug: super-admin) - X permisos
[...]
```

### 4.2 Verificar logs (opcional)
```bash
# Ver últimas entradas del log
tail -n 20 storage/logs/laravel.log
```

---

## 🧪 **Paso 5: Probar funcionalidad**

### 5.1 Test de creación de usuarios
1. Ir a **https://app.tierrasonada.com**
2. **Iniciar sesión** con cuenta de administrador
3. Navegar a **Administración > Usuarios**
4. Hacer clic en **"Crear Usuario"**
5. Completar formulario y **enviar**

**✅ Resultado esperado:**
- ✅ NO debe aparecer error 403
- ✅ Usuario se crea exitosamente
- ✅ Redirige a página de login (NO auto-login)
- ✅ Mensaje: "Usuario registrado exitosamente. Por favor inicia sesión."

### 5.2 Test de menú inteligente
1. Navegar entre diferentes secciones del sistema
2. **Verificar** que solo se expande la sección activa:
   - Dashboard → todas las secciones colapsadas
   - Proyectos → solo "Gestión Inmobiliaria" expandida
   - Usuarios → solo "Administración" expandida

---

## 🚨 **Solución de problemas**

### Error: "Unknown column 'is_super_admin'"
```sql
-- Verificar si la columna existe
DESCRIBE users;

-- Si no existe, crearla manualmente
ALTER TABLE users ADD COLUMN is_super_admin BOOLEAN DEFAULT FALSE;
```

### Error 403 persiste
```bash
# Verificar permisos específicos del usuario
php artisan tinker
>>> $user = \App\Models\User::where('email', 'tu-email@ejemplo.com')->first();
>>> echo $user->isSuperAdmin() ? 'ES SUPER ADMIN' : 'NO ES SUPER ADMIN';
>>> exit
```

### Problemas de caché
```bash
# Limpiar todo agresivamente
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Reiniciar servicios web
sudo systemctl restart apache2  # o nginx
```

### Ver logs detallados
```bash
# Monitorear logs en tiempo real
tail -f storage/logs/laravel.log

# Ver logs específicos de permisos
grep -i "permission\|super admin" storage/logs/laravel.log
```

---

## 🔄 **Rollback (Si algo sale mal)**

### 1. Revertir cambios de código
```bash
git log --oneline -5  # Ver commits recientes
git checkout HEAD~1   # Volver al commit anterior
```

### 2. Revertir cambios de base de datos
```sql
-- Solo si es absolutamente necesario
ALTER TABLE users DROP COLUMN is_super_admin;
```

### 3. Limpiar cachés
```bash
php artisan optimize:clear
```

---

## 📞 **Soporte**

### Información para debugging
Si necesitas ayuda, ejecuta estos comandos y comparte los resultados:

```bash
# Información del sistema
php artisan --version
git log --oneline -3

# Estado de base de datos  
php artisan diagnose:permissions

# Logs recientes
tail -n 30 storage/logs/laravel.log
```

### Comandos útiles para desarrollo
```bash
# Ver todas las rutas
php artisan route:list | grep users

# Verificar middleware
php artisan route:list --name=users.create

# Test de permisos específicos
php artisan tinker
>>> Auth::user()->isSuperAdmin()
>>> Auth::user()->hasPermission('users-create')
```

---

## ✅ **Checklist de verificación final**

- [ ] Código actualizado desde Git
- [ ] Base de datos modificada (columna is_super_admin)
- [ ] Usuarios convertidos a super admin
- [ ] Cachés limpiados y regenerados
- [ ] Comando diagnose:permissions ejecutado exitosamente
- [ ] Creación de usuarios funciona sin error 403
- [ ] Menú se comporta inteligentemente
- [ ] No hay auto-login después de crear usuarios

---

**🎉 ¡Actualización completada exitosamente!**

El sistema Tierra Soñada ahora tiene mejor gestión de permisos y una experiencia de usuario mejorada.

---

*Generado automáticamente por Claude Code - Septiembre 2025*