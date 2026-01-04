FROM php:8.2-cli

# Instalar extensiones PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copiar archivos del proyecto
WORKDIR /app
COPY . .

# Copiar script de inicio y dar permisos
COPY start.sh /app/start.sh
RUN chmod +x /app/start.sh

# Exponer puerto (Railway usa variable PORT)
EXPOSE ${PORT:-8080}

# Comando de inicio - ejecutar script
CMD ["/app/start.sh"]
