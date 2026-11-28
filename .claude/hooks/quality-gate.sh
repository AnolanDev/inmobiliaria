#!/usr/bin/env bash
# =============================================================================
# Stop hook: quality gate.
#
# Claude cannot end its turn while Pint, Larastan or the test suite fail.
#   - Pint and Larastan always block.
#   - Tests run against their own database (inmobiliaria_test_hook), brought
#     up to date with "migrate" (never a destructive command) by the owner
#     connection, under a flock so two runs never collide.
#   - If the database server does not respond, tests are skipped with a
#     warning (Pint and Larastan still block).
#   - If the working tree has not changed since the last fully green run,
#     nothing is re-run.
#
# Exit 0 = allow stop. Exit 2 = block (stderr is fed back to Claude).
# Credentials are read by Laravel from .env.testing; this script never
# prints or handles them.
# =============================================================================
set -uo pipefail

HOOK_DB="inmobiliaria_test_hook"
LOCK_WAIT_SECONDS="${QUALITY_GATE_LOCK_WAIT:-600}"
MAX_OUTPUT_CHARS=6000

PROJECT_DIR="${CLAUDE_PROJECT_DIR:-$(cd "$(dirname "$0")/../.." && pwd)}"
cd "$PROJECT_DIR" || exit 0

# The hook payload (stdin) is not needed; drain it so the writer never blocks.
cat >/dev/null 2>&1 || true

GIT_DIR="$(git rev-parse --absolute-git-dir 2>/dev/null)" || exit 0
STATE_FILE="$GIT_DIR/quality-gate.green"
LOCK_FILE="$GIT_DIR/quality-gate.lock"

block() {
    {
        echo "Quality gate: $1"
        if [[ -n "${2:-}" ]]; then
            echo
            printf '%s\n' "$2" | sed -E 's/\x1b\[[0-9;]*m//g' | tail -c "$MAX_OUTPUT_CHARS"
        fi
        echo
        echo "Corrige el problema antes de terminar el turno."
    } >&2
    exit 2
}

allow() {
    if [[ -n "${1:-}" ]]; then
        jq -cn --arg msg "$1" '{systemMessage: $msg}'
    fi
    exit 0
}

# --- Skip when nothing changed since the last fully green run -----------------
fingerprint() {
    {
        git rev-parse HEAD 2>/dev/null
        git status --porcelain=v1 --untracked-files=all
        git diff HEAD --binary 2>/dev/null
        git ls-files --others --exclude-standard -z | xargs -0 -r sha256sum 2>/dev/null
    } | sha256sum | cut -d' ' -f1
}

CURRENT="$(fingerprint)"
if [[ -f "$STATE_FILE" && "$(cat "$STATE_FILE")" == "$CURRENT" ]]; then
    allow
fi

# --- Pint -----------------------------------------------------------------------
if ! out="$(vendor/bin/pint --test 2>&1)"; then
    block "Pint encontró problemas de formato (ejecuta: vendor/bin/pint)." "$out"
fi

# --- Larastan -------------------------------------------------------------------
if ! out="$(vendor/bin/phpstan analyse --no-progress --memory-limit=1G 2>&1)"; then
    block "Larastan encontró errores." "$out"
fi

# --- Tests (own database, under a lock) -----------------------------------------
export DB_DATABASE="$HOOK_DB"

exec 9>"$LOCK_FILE"
if ! flock -w "$LOCK_WAIT_SECONDS" 9; then
    block "otra corrida del hook sigue usando $HOOK_DB después de ${LOCK_WAIT_SECONDS}s."
fi

# Is the database server reachable? Only "server not responding" skips tests;
# any other connection problem (bad credentials, missing database) blocks.
if ! probe="$(timeout 30 php artisan db:show --database=mysql_owner --env=testing 2>&1)"; then
    if grep -qE 'SQLSTATE\[HY000\] \[(2002|2003|2006|2013)\]|Connection refused|timed out' <<<"$probe" || [[ -z "$probe" ]]; then
        allow "Quality gate: Pint y Larastan OK; pruebas OMITIDAS porque MySQL no responde."
    fi
    block "no se pudo conectar a $HOOK_DB con la conexión mysql_owner (.env.testing)." "$probe"
fi

# Only ever migrate the hook database (a stale config cache, for example,
# would ignore the DB_DATABASE override).
resolved="$(php artisan config:show database.connections.mysql_owner.database --env=testing 2>&1)"
if ! grep -qE "[[:space:]]${HOOK_DB}[[:space:]]*\$" <<<"$resolved"; then
    block "la conexión mysql_owner no apunta a $HOOK_DB; no se migra nada (¿caché de configuración?)." "$resolved"
fi

if ! out="$(php artisan migrate --database=mysql_owner --env=testing --force --no-interaction 2>&1)"; then
    block "fallaron las migraciones en $HOOK_DB." "$out"
fi

if ! out="$(php artisan test --compact 2>&1)"; then
    # Agent-format output is one JSON line; keep every failure (test, file,
    # line) and trim long messages such as full HTML responses.
    short="$(jq -c '.failures |= map(.message |= .[0:500])' <<<"$out" 2>/dev/null)" && out="$short"
    block "fallan las pruebas." "$out"
fi

echo "$CURRENT" >"$STATE_FILE"
allow
