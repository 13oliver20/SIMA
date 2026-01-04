FROM php:8.2-cli

# Instalar extensiones PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copiar archivos del proyecto
WORKDIR /app
COPY . .

# Exponer puerto (Railway usa variable PORT)
EXPOSE ${PORT:-8080}

# Comando de inicio - Railway inyecta PORT automáticamente
CMD php -S 0.0.0.0:${PORT:-8080} -t comercio
