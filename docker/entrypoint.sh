#!/bin/sh
set -e

# Ensure runtime and web/assets directories exist and have correct permissions
mkdir -p /var/www/html/runtime /var/www/html/web/assets
chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets
chmod -R 775 /var/www/html/runtime /var/www/html/web/assets

# Execute the CMD (which will be apache2-foreground)
exec "$@"