#!/bin/sh
# Script de inicio para Railway
# Railway inyecta la variable PORT automáticamente

echo "Starting PHP server on port ${PORT}..."
php -S 0.0.0.0:${PORT} -t comercio
