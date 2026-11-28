# Pendientes

- **Doble separación simultánea (Fase 1):** la prueba necesita dos conexiones reales y no funciona dentro de `DatabaseTransactions`. Resolverla en la Fase 1.
- **Producción sin SSH:** definir cómo ejecutar las migraciones (con el usuario dueño) y `php artisan users:create` antes del primer despliegue.
- **Dependabot:** antes de producción, configurar `.github/dependabot.yml` para actualizaciones de versión. El kit traía uno solo para GitHub Actions; se retiró en E0 para configurarlo completo en su momento.
- **Triggers y registro binario:** con la primera migración que cree un trigger, activar `log_bin_trust_function_creators` en local y en el CI (ver `docs/database-users.md`). Sin eso, MySQL 8.0 rechaza el trigger incluso al dueño (error 1419).
- **Zona horaria (antes de la Fase 1):** decidir entre guardar en UTC y mostrar en `America/Bogota`, o configurar la aplicación en `America/Bogota`. Las fechas límite de separación dependen de ello.
