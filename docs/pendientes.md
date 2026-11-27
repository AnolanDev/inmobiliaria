# Pendientes

- **Doble separación simultánea (Fase 1):** la prueba necesita dos conexiones reales y no funciona dentro de `DatabaseTransactions`. Resolverla en la Fase 1.
- **Producción sin SSH:** definir cómo ejecutar las migraciones (con el usuario dueño) y `php artisan users:create` antes del primer despliegue.
- **Dependabot:** antes de producción, configurar `.github/dependabot.yml` para actualizaciones de versión. El kit traía uno solo para GitHub Actions; se retiró en E0 para configurarlo completo en su momento.
