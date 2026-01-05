#!/bin/sh
# Debug: mostrar el valor de PORT
echo "DEBUG: PORT variable is: ${PORT}"
echo "DEBUG: Starting PHP server on port ${PORT}"

# Iniciar servidor PHP
php -S 0.0.0.0:${PORT} -t . router.php
