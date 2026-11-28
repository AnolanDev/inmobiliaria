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
#   - Loop guard: after blocking 3 times in a row with the same failure
#     (consecutive stops with stop_hook_active=true), the next stop is let
#     through with a visible warning asking the user to step in. The streak
#     lives in .git/quality-gate.streak.
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

# stop_hook_active is true when Claude is already continuing because this
# hook blocked the previous stop attempt.
PAYLOAD="$(cat 2>/dev/null || true)"
STOP_HOOK_ACTIVE="$(jq -r '.stop_hook_active // false' <<<"$PAYLOAD" 2>/dev/null || echo false)"

GIT_DIR="$(git rev-parse --absolute-git-dir 2>/dev/null)" || exit 0
STATE_FILE="$GIT_DIR/quality-gate.green"
LOCK_FILE="$GIT_DIR/quality-gate.lock"
STREAK_FILE="$GIT_DIR/quality-gate.streak"
MAX_SAME_FAILURE_BLOCKS=3

# Allows the stop. Clears the failure streak: the loop is broken either way.
allow() {
    rm -f "$STREAK_FILE"
    if [[ -n "${1:-}" ]]; then
        jq -cn --arg msg "$1" '{systemMessage: $msg}'
    fi
    exit 0
}

# Blocks the stop, unless the same failure has already blocked
# MAX_SAME_FAILURE_BLOCKS times in a row: then lets the turn end with a
# visible warning, so an unfixable failure never loops forever.
block() {
    local details=""
    if [[ -n "${2:-}" ]]; then
        details="$(printf '%s\n' "$2" | sed -E 's/\x1b\[[0-9;]*m//g' | tail -c "$MAX_OUTPUT_CHARS")"
    fi

    # Same failure = same reason and output once timings are removed.
    local signature count=1
    signature="$(printf '%s\n%s' "$1" "$details" \
        | sed -E 's/"duration_ms":[0-9]+//g; s/[0-9]+(\.[0-9]+)?m?s\b//g' \
        | sha256sum | cut -d' ' -f1)"
    if [[ "$STOP_HOOK_ACTIVE" == "true" && -f "$STREAK_FILE" ]]; then
        read -r last_signature last_count <"$STREAK_FILE" || true
        if [[ "${last_signature:-}" == "$signature" ]]; then
            count=$(( ${last_count:-0} + 1 ))
        fi
    fi

    if (( count > MAX_SAME_FAILURE_BLOCKS )); then
        allow "Quality gate: la misma falla bloqueó ${MAX_SAME_FAILURE_BLOCKS} veces seguidas y se deja terminar el turno. NECESITAS INTERVENIR: $1"
    fi
    echo "$signature $count" >"$STREAK_FILE"

    {
        echo "Quality gate (bloqueo ${count} de ${MAX_SAME_FAILURE_BLOCKS} por esta misma falla): $1"
        if [[ -n "$details" ]]; then
            echo
            printf '%s\n' "$details"
        fi
        echo
        echo "Corrige el problema antes de terminar el turno."
    } >&2
    exit 2
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
