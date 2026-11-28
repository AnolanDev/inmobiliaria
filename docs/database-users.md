# Usuarios de base de datos

El proyecto usa dos usuarios de MySQL con responsabilidades separadas:

| Usuario | Para qué | Permisos |
|---|---|---|
| `inmobiliaria_owner` | Solo migraciones (`php artisan migrate --database=mysql_owner`) | `ALL PRIVILEGES` sobre las bases del proyecto, sin `GRANT OPTION` ni privilegios globales |
| `inmobiliaria_app` | La aplicación y las pruebas (conexión `mysql`, la predeterminada) | Solo `SELECT, INSERT, UPDATE, DELETE` |

Bases del proyecto en local: `inmobiliaria` (desarrollo), `inmobiliaria_test` (pruebas manuales) e `inmobiliaria_test_hook` (pruebas del hook de Claude Code).

El usuario de la aplicación **no puede migrar**: sin `CREATE`, `ALTER`, `DROP`, `INDEX`, `REFERENCES`, `TRIGGER` ni `GRANT OPTION`. Toda migración va por la conexión `mysql_owner`.

## Local: crear o actualizar los usuarios

El script `database/sql/users.sql` es idempotente: se puede ejecutar las veces que haga falta. Crea los usuarios que falten, les pone la contraseña indicada, les **retira todos los permisos** y vuelve a otorgar exactamente la lista mínima. Si alguien agregó un permiso a mano, la siguiente ejecución lo quita.

Las contraseñas nunca se escriben en el archivo. Se anteponen como variables de sesión de MySQL y todo se envía al cliente por entrada estándar. El script se detiene sin hacer nada si falta una contraseña, si tiene menos de 16 caracteres o si las dos son iguales.

Desde la raíz del proyecto:

```bash
read -rs -p "Contraseña para inmobiliaria_owner: " OWNER_PW; echo
read -rs -p "Contraseña para inmobiliaria_app: " APP_PW; echo
esc() { local s=${1//\\/\\\\}; printf '%s' "${s//\'/\'\'}"; }
{ printf "SET @owner_password='%s', @app_password='%s';\n" "$(esc "$OWNER_PW")" "$(esc "$APP_PW")"; cat database/sql/users.sql; } | sudo mysql
unset OWNER_PW APP_PW; unset -f esc
```

- `read -s` no muestra lo que escribes, y en el historial de bash solo quedan los comandos, nunca las contraseñas.
- `printf` es un comando interno de bash: las contraseñas no aparecen en la lista de procesos (`ps`).
- `esc` duplica las comillas simples y las barras invertidas, así que la contraseña puede contener cualquier carácter.
- `sudo` pide tu contraseña de Ubuntu por la terminal, no por la tubería.
- Al final, el script muestra los permisos resultantes (`SHOW GRANTS`), sin contraseñas.

Variante con redirección `<`: también funciona si las variables y el script están en un archivo, pero ese archivo tendría las contraseñas en disco. Por eso se recomienda la tubería.

Después, edita tú mismo `.env` y `.env.testing` (nunca se suben a git):

```dotenv
DB_USERNAME=inmobiliaria_app
DB_PASSWORD=...
DB_OWNER_USERNAME=inmobiliaria_owner
DB_OWNER_PASSWORD=...
```

Comprobación sin mostrar secretos:

```bash
php artisan db:show                                # conecta como inmobiliaria_app
php artisan migrate --database=mysql_owner         # migra como dueño
```

### Host de los usuarios

Por defecto los usuarios se crean como `'usuario'@'localhost'`. Con `DB_HOST=127.0.0.1` la conexión entra por TCP y MySQL la acepta como `localhost` (comprobado). Para el CI, donde la base corre en un contenedor, se antepone además `SET @user_host = '%';`. El script solo acepta `localhost`, `127.0.0.1` o `%`.

### Triggers y registro binario

MySQL 8.0 trae el registro binario activado. Con él, crear un trigger exige `SUPER` o la variable global `log_bin_trust_function_creators=1`. Sin eso, ni siquiera el dueño puede crear triggers (comprobado: error 1419). Cuando la primera migración necesite un trigger:

- **Local:** agregar `log_bin_trust_function_creators = 1` en la sección `[mysqld]` de `/etc/mysql/mysql.conf.d/mysqld.cnf` y reiniciar MySQL.
- **CI:** ejecutar `SET GLOBAL log_bin_trust_function_creators = 1` como root del contenedor antes de migrar.
- **cPanel:** no se pueden cambiar variables globales. Hay que probarlo antes de la Fase 1 (ver CLAUDE.md).

## cPanel: replicar los dos usuarios

En hosting compartido no se puede ejecutar `users.sql`, porque la cuenta no tiene permiso para `CREATE USER` ni `GRANT`. Los usuarios se crean desde la interfaz de cPanel. cPanel antepone el nombre de la cuenta a bases y usuarios. En este documento ese prefijo aparece como `CUENTA_` (marcador, no el valor real).

1. **Bases de datos MySQL → Crear nueva base de datos:** `inmobiliaria`. Queda como `CUENTA_inmobiliaria`.
2. **Usuarios MySQL → Añadir nuevo usuario:**
   - `inmobiliaria_owner`, que queda como `CUENTA_inmobiliaria_owner`.
   - `inmobiliaria_app`, que queda como `CUENTA_inmobiliaria_app`.
   - Usa contraseñas largas y distintas, generadas con el generador de cPanel o un gestor de contraseñas.
3. **Añadir usuario a la base de datos:**
   - `CUENTA_inmobiliaria_owner` → `CUENTA_inmobiliaria`: marcar **TODOS LOS PRIVILEGIOS**.
   - `CUENTA_inmobiliaria_app` → `CUENTA_inmobiliaria`: marcar **solo** `SELECT`, `INSERT`, `UPDATE` y `DELETE`. Ninguna otra casilla (ni `CREATE TEMPORARY TABLES`, `LOCK TABLES`, `EXECUTE`, `CREATE VIEW`, `SHOW VIEW`, `TRIGGER`, `EVENT`, `REFERENCES`, `INDEX`, `ALTER`, `CREATE`, `DROP`, `CREATE ROUTINE`, `ALTER ROUTINE`).
4. Verificación opcional desde phpMyAdmin, conectado como el usuario de la aplicación: `SHOW GRANTS;` solo debe listar `USAGE` y `SELECT, INSERT, UPDATE, DELETE ON CUENTA_inmobiliaria.*`.

`.env` de producción:

```dotenv
DB_DATABASE=CUENTA_inmobiliaria
DB_USERNAME=CUENTA_inmobiliaria_app
DB_PASSWORD=...
```

Las credenciales del dueño solo hacen falta mientras se migra. Cómo ejecutar migraciones sin SSH está pendiente (ver `docs/pendientes.md`).
