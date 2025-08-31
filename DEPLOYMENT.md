# 🚀 Guía de Despliegue a Producción

## 📋 Requisitos del Servidor

### Servidor Web
- **Sistema Operativo**: Ubuntu 22.04 LTS (recomendado)
- **Nginx**: 1.18+ o Apache 2.4+
- **PHP**: 8.2 o superior
- **Node.js**: 18.0+ con npm
- **Supervisor**: Para colas de trabajo
- **SSL**: Certificado Let's Encrypt

### Base de Datos
- **MySQL**: 8.0+ (recomendado)
- **PostgreSQL**: 13+ (alternativa)
- **Redis**: 6.0+ (opcional, para caché y colas)

### Recursos Mínimos
- **RAM**: 2GB mínimo, 4GB recomendado
- **CPU**: 2 cores mínimo
- **Disco**: 20GB SSD mínimo
- **Ancho de banda**: 100Mbps

---

## 🔧 Instalación Paso a Paso

### 1. Preparar el Servidor

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias
sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-gd php8.2-zip php8.2-mbstring redis-server supervisor git curl -y

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Configurar Base de Datos

```bash
# Crear base de datos
sudo mysql -u root -p

CREATE DATABASE tierra_sonada_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'usuario_prod'@'localhost' IDENTIFIED BY 'CONTRASEÑA_SEGURA';
GRANT ALL PRIVILEGES ON tierra_sonada_prod.* TO 'usuario_prod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Clonar y Configurar Proyecto

```bash
# Crear directorio del proyecto
sudo mkdir -p /var/www/tierra-sonada
cd /var/www/tierra-sonada

# Clonar repositorio
sudo git clone https://github.com/TU-USUARIO/inmobiliaria.git .

# Configurar permisos
sudo chown -R www-data:www-data /var/www/tierra-sonada
sudo chmod -R 755 /var/www/tierra-sonada
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Instalar Dependencias

```bash
# Dependencias PHP
composer install --optimize-autoloader --no-dev

# Dependencias Node.js
npm install
npm run build
```

### 5. Configurar Entorno

```bash
# Copiar archivo de entorno
cp .env.production.example .env

# Editar configuración (usar tu editor favorito)
sudo nano .env

# Generar clave de aplicación
php artisan key:generate
```

### 6. Configurar Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (opcional, solo para datos de prueba)
php artisan db:seed --force

# Cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Configurar Nginx

```nginx
# /etc/nginx/sites-available/tierra-sonada
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/tierra-sonada/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/tu-dominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/tu-dominio.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Verificar renovación automática
sudo certbot renew --dry-run
```

### 9. Configurar Colas de Trabajo

```bash
# /etc/supervisor/conf.d/tierra-sonada-worker.conf
[program:tierra-sonada-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/tierra-sonada/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/tierra-sonada/storage/logs/worker.log
stopwaitsecs=3600

# Reiniciar supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tierra-sonada-worker:*
```

### 10. Configurar Tareas Programadas

```bash
# Agregar crontab
sudo crontab -e

# Agregar línea:
* * * * * cd /var/www/tierra-sonada && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Configuraciones de Seguridad

### Firewall
```bash
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
sudo ufw enable
```

### Fail2ban
```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
```

---

## 📊 Monitoreo y Mantenimiento

### Logs Importantes
- Laravel: `/var/www/tierra-sonada/storage/logs/`
- Nginx: `/var/log/nginx/`
- PHP: `/var/log/php8.2-fpm.log`

### Comandos de Mantenimiento
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Backup de base de datos
mysqldump -u usuario_prod -p tierra_sonada_prod > backup_$(date +%Y%m%d).sql
```

---

## 🚀 Actualizaciones Futuras

### Proceso de Actualización
1. **Backup**: Crear respaldo completo
2. **Mantenimiento**: Activar modo mantenimiento
3. **Pull**: Descargar cambios del repositorio
4. **Dependencias**: Actualizar composer y npm
5. **Migraciones**: Ejecutar migraciones de BD
6. **Caché**: Limpiar y regenerar caché
7. **Verificación**: Probar funcionalidad
8. **Activar**: Desactivar modo mantenimiento

```bash
# Script de actualización
php artisan down
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

---

## 🆘 Solución de Problemas

### Problemas Comunes
1. **Error 500**: Verificar logs de Laravel y permisos
2. **Error 404**: Verificar configuración de Nginx
3. **Error de BD**: Verificar credenciales y conexión
4. **Archivos no cargan**: Verificar permisos de storage

### Contacto de Soporte
- **Desarrollador**: Gustavo Olivera
- **Email**: [tu-email@dominio.com]
- **Documentación**: Este archivo y CHANGELOG.md