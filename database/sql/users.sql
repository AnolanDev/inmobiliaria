-- =============================================================================
-- Database users for the project (idempotent).
--
--   inmobiliaria_owner : ALL PRIVILEGES on the project databases (no GRANT
--                        OPTION, no global privileges). Migrations only.
--   inmobiliaria_app   : SELECT, INSERT, UPDATE, DELETE only. Used by the app.
--
-- Passwords are NEVER written in this file. Prepend them as session variables
-- and pipe everything to the mysql client (see docs/database-users.md):
--
--   SET @owner_password = '...', @app_password = '...';   -- required
--   SET @user_host = '%';                                 -- optional (CI)
--
-- Safe to run repeatedly: it creates missing users, resets their passwords,
-- revokes everything and grants exactly the minimum list again.
-- The script aborts at the first error (mysql client in batch mode) and does
-- nothing if a password is missing, shorter than 16 characters, or if both
-- passwords are equal.
-- =============================================================================

SET @user_host = COALESCE(NULLIF(@user_host, ''), 'localhost');

-- --- Guards -------------------------------------------------------------------
-- A failing guard runs a statement whose error message names the problem:
--   "Unknown column 'abort_...' in 'field list'".

SET @stmt = IF(CHAR_LENGTH(COALESCE(@owner_password, '')) >= 16,
    'DO 0', 'SELECT abort_owner_password_missing_or_shorter_than_16_chars');
PREPARE guard FROM @stmt; EXECUTE guard; DEALLOCATE PREPARE guard;

SET @stmt = IF(CHAR_LENGTH(COALESCE(@app_password, '')) >= 16,
    'DO 0', 'SELECT abort_app_password_missing_or_shorter_than_16_chars');
PREPARE guard FROM @stmt; EXECUTE guard; DEALLOCATE PREPARE guard;

SET @stmt = IF(BINARY @owner_password <> BINARY @app_password,
    'DO 0', 'SELECT abort_owner_and_app_passwords_must_differ');
PREPARE guard FROM @stmt; EXECUTE guard; DEALLOCATE PREPARE guard;

SET @stmt = IF(@user_host IN ('localhost', '127.0.0.1', '%'),
    'DO 0', 'SELECT abort_user_host_must_be_localhost_127_0_0_1_or_percent');
PREPARE guard FROM @stmt; EXECUTE guard; DEALLOCATE PREPARE guard;

SET @owner = CONCAT(QUOTE('inmobiliaria_owner'), '@', QUOTE(@user_host));
SET @app = CONCAT(QUOTE('inmobiliaria_app'), '@', QUOTE(@user_host));

-- --- Owner --------------------------------------------------------------------

SET @stmt = CONCAT('CREATE USER IF NOT EXISTS ', @owner, ' IDENTIFIED BY ', QUOTE(@owner_password));
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('ALTER USER ', @owner, ' IDENTIFIED BY ', QUOTE(@owner_password));
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('REVOKE ALL PRIVILEGES, GRANT OPTION FROM ', @owner);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('GRANT ALL PRIVILEGES ON `inmobiliaria`.* TO ', @owner);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('GRANT ALL PRIVILEGES ON `inmobiliaria_test`.* TO ', @owner);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('GRANT ALL PRIVILEGES ON `inmobiliaria_test_hook`.* TO ', @owner);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

-- --- Application ----------------------------------------------------------------

SET @stmt = CONCAT('CREATE USER IF NOT EXISTS ', @app, ' IDENTIFIED BY ', QUOTE(@app_password));
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('ALTER USER ', @app, ' IDENTIFIED BY ', QUOTE(@app_password));
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('REVOKE ALL PRIVILEGES, GRANT OPTION FROM ', @app);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('GRANT SELECT, INSERT, UPDATE, DELETE ON `inmobiliaria`.* TO ', @app);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('GRANT SELECT, INSERT, UPDATE, DELETE ON `inmobiliaria_test`.* TO ', @app);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('GRANT SELECT, INSERT, UPDATE, DELETE ON `inmobiliaria_test_hook`.* TO ', @app);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

-- --- Cleanup and report ---------------------------------------------------------

SET @owner_password = NULL, @app_password = NULL, @stmt = NULL;

SET @stmt = CONCAT('SHOW GRANTS FOR ', @owner);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = CONCAT('SHOW GRANTS FOR ', @app);
PREPARE s FROM @stmt; EXECUTE s; DEALLOCATE PREPARE s;

SET @stmt = NULL, @owner = NULL, @app = NULL;
