#!/bin/bash
set -e

# Otras configuraciones si es necesario

# Ejecutar composer install
cd /var/www && composer install
cd /var/www && php artisan migrate

# Iniciar la aplicación
exec "$@"
