#!/usr/bin/env bash

# Clear config and cache
php artisan optimize:clear

# Cache for production
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Make sure SQLite database file exists and has correct permissions
touch database/database.sqlite
chown www-data:www-data database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force

# Start Apache in foreground
apache2-foreground
