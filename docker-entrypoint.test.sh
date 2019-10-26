#!/usr/bin/env bash
# -e exit       when a command fails
# -u exit       when trying to use undefined variable
# -o pipefail   return the exit code of piped commands that fail
# -x            debug

set -euo pipefail

PREFIX="[*]"

echo
echo $PREFIX "Running migrations on database"
php artisan config:cache
php artisan migrate --force

echo
echo $PREFIX "Current migrations in database"
php artisan migrate:status

echo
echo $PREFIX "Routes"
php artisan route:list

echo
echo $PREFIX "Running tests"
./vendor/bin/phpunit --log-junit reports/tests.xml --stop-on-fail

echo
echo $PREFIX "Restoring user permissions"
chown -R $TEST_UID:$TEST_GID .
