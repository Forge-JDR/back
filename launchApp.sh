#!/bin/sh
set -eu

export APP_ENV="${APP_ENV:-prod}"
export APP_DEBUG="${APP_DEBUG:-0}"
cd /var/www

# Keys are provisioned once and kept outside the image in persistent storage.
private_key="${JWT_SECRET_KEY:-/var/www/config/jwt/private.pem}"
public_key="${JWT_PUBLIC_KEY:-/var/www/config/jwt/public.pem}"
private_key=$(printf '%s' "$private_key" | sed 's|%kernel.project_dir%|/var/www|g')
public_key=$(printf '%s' "$public_key" | sed 's|%kernel.project_dir%|/var/www|g')
if [ ! -r "$private_key" ] || [ ! -r "$public_key" ]; then
    echo "JWT keys are missing or unreadable. Provision persistent keys before starting." >&2
    exit 1
fi

mkdir -p var/cache var/log
chown -R www-data:www-data var
chmod -R 775 var
php bin/console cache:clear --env="$APP_ENV" --no-debug

# Database creation, migrations and demo fixtures are explicit maintenance tasks.
# Starting or restarting the application must never modify the database schema.
exec apache2-foreground
