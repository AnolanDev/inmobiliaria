# 📧 Guía de Implementación - Email Marketing

## 🚀 Pasos para Implementar Email Marketing

### **1. Ejecutar Migraciones y Seeders**

```bash
# Ejecutar las migraciones existentes
php artisan migrate

# Ejecutar el seeder de Email Marketing
php artisan db:seed --class=EmailMarketingSeeder

# Crear tabla de jobs para queue
php artisan queue:table
php artisan migrate
```

### **2. Configurar Variables de Entorno (.env)**

```env
# Configuración de Email
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Configuración de Queue
QUEUE_CONNECTION=database

# Email Marketing Settings
EMAIL_DAILY_LIMIT=1000
EMAIL_HOURLY_LIMIT=100
EMAIL_BATCH_SIZE=50
EMAIL_TRACKING_ENABLED=true
EMAIL_CLICK_TRACKING_ENABLED=true

# Información de la empresa (opcional)
COMPANY_ADDRESS="Tu dirección física"
COMPANY_PHONE="+1234567890"
```

### **3. Asignar Permisos a Roles**

```bash
# Ejecutar en tinker o crear un seeder personalizado
php artisan tinker

# Ejemplo para asignar permisos al rol Admin
$adminRole = Spatie\Permission\Models\Role::where('name', 'Admin')->first();
$permissions = [
    'email-marketing-view',
    'email-marketing-create', 
    'email-marketing-edit',
    'email-marketing-delete'
];

foreach($permissions as $permission) {
    $perm = Spatie\Permission\Models\Permission::where('slug', $permission)->first();
    if($perm) {
        $adminRole->givePermissionTo($perm);
    }
}
```

### **4. Iniciar Queue Worker**

```bash
# Para desarrollo (se debe ejecutar en una terminal separada)
php artisan queue:work

# Para producción (usar supervisor o similar)
php artisan queue:work --daemon --tries=3 --timeout=300
```

### **5. Verificar Funcionamiento**

1. **Acceder al módulo**: Navegar a `/email-templates` en la aplicación
2. **Ver templates**: Verificar que se cargaron los templates por defecto
3. **Crear template de prueba**: Crear un template simple
4. **Crear campaña de prueba**: Crear una campaña con pocos destinatarios
5. **Enviar prueba**: Enviar la campaña y verificar recepción

---

## 📋 **Estado Actual de Implementación**

### ✅ **Completado**

- [x] Migraciones de base de datos
- [x] Modelos con relaciones completas
- [x] Controladores backend (Templates, Campaigns, Tracking)
- [x] Job queue para envío masivo
- [x] Sistema de tracking de aperturas y clics
- [x] Rutas completas
- [x] Permisos y navegación
- [x] Vistas Vue (Templates: Index, Create, Show, Edit)
- [x] Vista Vue (Campaigns: Index)
- [x] Configuración completa
- [x] Templates por defecto
- [x] Vistas de unsubscribe

### 🔄 **En Progreso**

- [ ] Vistas Vue restantes de Campaigns (Create, Show, Edit)
- [ ] Sistema de drip campaigns automáticas
- [ ] Editor WYSIWYG avanzado
- [ ] Tests unitarios

### 📊 **Funcionalidades Implementadas**

1. **Sistema de Templates**
   - CRUD completo con preview
   - Variables dinámicas por categoría
   - Duplicación de templates
   - Templates del sistema vs usuario

2. **Sistema de Campañas**
   - Gestión de estados (draft, sending, sent, paused)
   - Segmentación avanzada de leads
   - A/B Testing de subject lines
   - Métricas en tiempo real

3. **Tracking Avanzado**
   - Aperturas con pixel tracking
   - Clics con redirección
   - Unsubscribe automático
   - Analytics detallados

4. **Integración Completa**
   - Sistema de permisos
   - Actividades automáticas
   - Queue jobs escalables
   - Interface Vue moderna

---

## 🔧 **Comandos Útiles**

```bash
# Ver jobs en cola
php artisan queue:monitor

# Limpiar jobs fallidos
php artisan queue:flush

# Reiniciar queue workers
php artisan queue:restart

# Ver estadísticas de email
php artisan tinker
>>> App\Models\EmailSend::count()
>>> App\Models\EmailCampaign::where('status', 'sent')->count()

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 🚨 **Troubleshooting**

### **Error: Templates no cargan**
```bash
# Verificar permisos
php artisan permission:cache-reset

# Re-ejecutar seeders
php artisan db:seed --class=EmailMarketingPermissionsSeeder
```

### **Error: Emails no se envían**
```bash
# Verificar configuración
php artisan config:show mail

# Verificar queue
php artisan queue:monitor

# Verificar logs
tail -f storage/logs/laravel.log
```

### **Error: Variables no se reemplazan**
- Verificar sintaxis: `{{variable_name}}`
- Verificar que la variable esté en la lista permitida
- Verificar el contenido del template

---

## 📈 **Métricas y Analytics**

El sistema incluye tracking completo de:

- **Entregas**: Emails enviados exitosamente
- **Aperturas**: Tracking con pixel transparente
- **Clics**: Tracking con redirección
- **Rebotes**: Emails que no pudieron ser entregados
- **Unsubscribes**: Bajas automáticas
- **Engagement Score**: Puntuación automática de engagement

---

## 🔐 **Seguridad y Compliance**

- Tokens únicos para tracking y unsubscribe
- Cumplimiento con regulaciones de email marketing
- Links de unsubscribe obligatorios
- Sanitización de contenido HTML
- Rate limiting para prevenir spam

---

## 📱 **Próximos Pasos Recomendados**

1. **Completar vistas Vue de Campaigns**
2. **Implementar drip campaigns**
3. **Agregar editor WYSIWYG**
4. **Crear tests automatizados**
5. **Optimizar performance**
6. **Agregar más templates por defecto**

---

¿Necesitas ayuda con algún paso específico? ¡El sistema está casi listo para producción! 🎉