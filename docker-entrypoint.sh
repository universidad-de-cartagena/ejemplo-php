#!/usr/bin/env bash
# -e exit       when a command fails
# -u exit       when trying to use undefined variable
# -o pipefail   return the exit code of piped commands that fail
# -x            debug

set -euo pipefail

PREFIX="[*]"

echo
echo $PREFIX "Waiting for db"
/bin/wait

echo
echo $PREFIX "Current migrations in database"
php artisan migrate:status

echo
echo $PREFIX "Running migrations on database"
php artisan migrate --force

echo
echo $PREFIX "Current migrations in database"
php artisan migrate:status

if [ "$APP_DEBUG" = "true" -a "$APP_ENV" = "local" ]; then
    echo
    echo $PREFIX "Routes"
    php artisan route:list
fi

exec "$@"
