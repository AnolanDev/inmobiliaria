# Fase 0 — Red de calidad (plan aprobado)

Stack: Laravel 13, PHP 8.3, Livewire 4 (kit oficial con Fortify y Flux, solo componentes gratuitos), MySQL 8.0, Pest 4. Código en inglés, interfaz en español.

Producción: hosting compartido con cPanel, sin procesos permanentes, sin SSH garantizado, sin Node en el servidor.

Repositorio público. Un commit por entregable (Conventional Commits) en la rama `fase-0`, con un único PR hacia `main` que se fusiona con merge commit o rebase (squash desactivado).

Forma de trabajo: un entregable a la vez; al terminar se muestran pruebas, Pint y Larastan, se hace commit, se resume en lenguaje sencillo y se espera "continúa".

## Entregables

| # | Entregable | Commit |
|---|---|---|
| E0 | Arranque + CI mínimo | `chore: bootstrap Livewire starter kit with minimal CI` |
| E1 | Usuarios de base de datos | `feat(db): add least-privilege owner and app database users` |
| E2 | Pruebas contra MySQL y en español | `test: run test suite against MySQL with Spanish locale` |
| E3 | Pint, Larastan y `composer check` | `chore: add Pint, Larastan level 7 and composer check script` |
| E4 | CI completo | `ci: run quality checks, owner migrations and app-user tests` |
| E5 | Configuración de Claude Code | `chore(claude): add settings and Stop quality gate` |
| E6 | CLAUDE.md | `docs: add CLAUDE.md with commands, decisions and pitfalls` |
| E7 | Sin registro abierto ni autoeliminación + `users:create` | `feat(auth): remove self-registration and account self-deletion` |
| E8 | Agente revisor | `chore(claude): add read-only compliance reviewer agent` |

E1 y E2 se adelantaron respecto al orden original: las pruebas solo corren contra MySQL y localmente no pueden correr hasta que existan los usuarios de base de datos.

### E0 — Arranque
- Kit de Livewire con Pest y Laravel Boost, driver MySQL, sin migraciones ni archivo SQLite.
- `.gitignore` revisado: excluye `.env`, `.env.testing` y cualquier `.env.*` salvo los `*.example`.
- `git init`, `user.email` solo para este repositorio, commit vacío en `main`, rama `fase-0` y PR en borrador hacia `main`.
- Se retiran de `phpunit.xml` las variables de SQLite / `:memory:` desde este entregable.
- CI mínimo en verde: `ubuntu-24.04`, `mysql:8.0.46`, PHP 8.3, acciones fijadas por SHA, build del frontend, migraciones y pruebas. El job se llama `tests` (nombre estable: lo exige la protección de `main`).
- Tras la primera corrida en verde, el dueño del repositorio activa la protección de `main` exigiendo `tests`.

### E1 — Usuarios de base de datos
- Conexión `mysql_owner` en `config/database.php` (`DB_OWNER_USERNAME`, `DB_OWNER_PASSWORD`; mismo host y base).
- `database/sql/users.sql` idempotente: `CREATE USER IF NOT EXISTS` → `ALTER USER` → `REVOKE ALL` → `GRANT` explícitos, con sentencias preparadas a partir de `@owner_password`, `@app_password` y `@user_host` (por defecto `'localhost'`; en CI `'%'`).
- Dueño (`inmobiliaria_owner`): `ALL` sobre `inmobiliaria`, `inmobiliaria_test`, `inmobiliaria_test_hook`, sin `GRANT OPTION`.
- Aplicación (`inmobiliaria_app`): solo `SELECT, INSERT, UPDATE, DELETE` sobre esas bases. Sin privilegios globales.
- Si una contraseña falta o es demasiado corta, la sentencia preparada queda en `NULL`, `PREPARE` falla y el script se detiene.
- Documentación: ejecución local con `read -rs` + `printf` (builtin) por tubería, sin historial; réplica en cPanel con marcadores (`CUENTA_inmobiliaria_owner`).

### E2 — Pruebas contra MySQL y en español
- `.env.testing` fuera de git; `.env.testing.example` sin contraseñas.
- Las pruebas usan `DatabaseTransactions`; el esquema lo migra antes el dueño (`php artisan migrate --database=mysql_owner --env=testing`). El proceso de pruebas solo usa el usuario de la aplicación.
- Prueba de guarda: el usuario de la aplicación no puede crear tablas.
- Adelantado desde E4 para que el CI siga en verde con las guardas: el CI ejecuta `users.sql` con contraseñas aleatorias de la misma corrida, migra con el dueño y corre las pruebas con el usuario de la aplicación.
- `APP_LOCALE=es`, `APP_FALLBACK_LOCALE=es`; traducciones de `laravel-lang` (solo desarrollo) publicadas en `lang/es` y `lang/es.json`, incluyendo los textos visibles del kit. Prueba de un mensaje de validación en español.

### E3 — Calidad
- Pint (preset `laravel`), Larastan nivel 7 sin baseline sobre `app/`, `bootstrap/app.php`, `config/`, `database/` y `routes/` (alcance del kit, aprobado), Pest.
- `composer check` corre los tres (con la migración previa del dueño para las pruebas).

### E4 — CI completo
- Pint y Larastan en el CI, antes del build y de las pruebas.
- Ya desde E2: contraseñas aleatorias en la misma corrida (`openssl rand -hex`, enmascaradas), `users.sql` por entrada estándar, migraciones con el dueño y todas las pruebas con el usuario de la aplicación.
- Imagen `mysql:8.0.46` fijada por etiqueta y digest; límite de tiempo y cancelación de corridas repetidas.
- Ramas `main` y `fase-*`, en `push` y `pull_request`.

### E5 — Claude Code (`.claude/`)
- `settings.json`: prohibido leer `.env`, `.env.testing` y demás `.env.*` conocidos (no los `.example`); prohibidos por completo `migrate:fresh`, `migrate:refresh`, `migrate:reset` y `db:wipe`; `git push` requiere aprobación; excepción del sandbox solo para `php artisan test`.
- Sandbox activado en el proyecto; las reglas `Read(...)` de `deny` también bloquean a los comandos dentro del sandbox.
- Hook Stop `quality-gate.sh`: Pint y Larastan siempre bloquean; las pruebas corren en `inmobiliaria_test_hook` con `flock`, y esa base se actualiza con `migrate` (nunca con comandos destructivos); si MySQL no responde se omiten con aviso (otros errores de conexión bloquean); si el árbol no cambió desde la última corrida en verde, no repite.
- Sin hook de formateo después de cada edición.

### E6 — CLAUDE.md
Corto, fuera del bloque de Boost: comandos, decisiones y trampas:
- `REVOKE` a nivel de tabla no resta un permiso de base; tablas de solo inserción se protegen con triggers.
- cPanel: sin `queue:work` permanente; colas y tareas por cron de `schedule:run`; assets compilados localmente.
- El usuario de la aplicación no migra: toda migración va por `mysql_owner`.
- Triggers en cPanel pueden exigir `log_bin_trust_function_creators`; antes de la Fase 1 hay que probar crear un trigger en cPanel y, si no se puede, el historial de solo inserción se protege desde la aplicación con pruebas.
- Renombrar el job `tests` rompe la protección de `main`.
- `php artisan db:show` lista las tablas de todas las bases a las que el usuario tiene acceso, no solo la de la conexión; para contar tablas de una base, consultar `information_schema` filtrando por `TABLE_SCHEMA`.
- Repositorio público: nunca datos reales (clientes, cédulas, teléfonos, precios o lotes de proyectos reales) en seeders, fixtures ni pruebas; nunca dominio, proveedor de hosting ni prefijo real de cPanel en `docs/` (usar marcadores como `CUENTA_inmobiliaria_owner`).

### E7 — Registro y autoeliminación
- Sin `Features::registration()`, rutas, vistas, enlaces ni componente de eliminar cuenta; pruebas ajustadas.
- `php artisan users:create` interactivo: contraseña oculta, nunca se muestra ni se registra.

### E8 — Agente revisor
- `.claude/agents/revisor-cumplimiento.md`, solo lectura (Read, Grep, Glob), revisa cada bloque contra `docs/specs/` y reporta en español con archivo y línea.
