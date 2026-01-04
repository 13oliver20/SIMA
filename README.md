# SIMA - Sistema de Información Municipal de Asociaciones

Sistema web para la gestión integral de asociaciones, socios, actas, juntas directivas y documentación legal.

## 🚀 Tecnologías

- **Backend:** PHP 8.2
- **Base de Datos:** PostgreSQL (Supabase)
- **Frontend:** HTML, CSS, JavaScript, Bootstrap (SB Admin 2)
- **Deployment:** Railway + Docker

## ✨ Características

- 👥 Gestión de Socios y Asociados
- 🏢 Administración de Grupos y Asociaciones
- 📋 Control de Actas de Verificación y Constitución
- 🏛️ Gestión de Juntas Directivas
- 📊 Reportes y Estadísticas
- 📁 Gestión Documental
- 🔐 Sistema de Autenticación Seguro

## 📦 Instalación Local

### Requisitos Previos
- PHP 8.2 o superior
- Extensiones PHP: `pdo_pgsql`, `pgsql`, `mysqli`
- Cuenta en Supabase (o PostgreSQL)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/13oliver20/SIMA.git
   cd SIMA
   ```

2. **Configurar Base de Datos**
   - Crea un proyecto en [Supabase](https://supabase.com)
   - Ejecuta el script SQL: `database/supa_schema_clean.sql` en el SQL Editor de Supabase
   - Anota tus credenciales de conexión

3. **Configurar Credenciales**
   ```bash
   cp comercio/includes/conexion.example.php comercio/includes/conexion.php
   ```
   
   Edita `conexion.php` y reemplaza con tus credenciales:
   ```php
   $host = 'db.xxx.supabase.co';
   $password = 'tu_password_aqui';
   ```

4. **Iniciar Servidor Local**
   ```bash
   cd comercio
   php -S localhost:8000
   ```

5. **Acceder al Sistema**
   - URL: http://localhost:8000
   - Login: http://localhost:8000/login.php

## 🌐 Despliegue en Producción

Este proyecto está configurado para desplegarse en Railway usando Docker.

### Variables de Entorno Requeridas:
```
DB_HOST=db.xxx.supabase.co
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=tu_password
DB_NAME=postgres
APP_ENV=production
```

## 📄 Licencia

Este proyecto es de uso interno. Todos los derechos reservados.

---

**Desarrollado con ❤️ para la gestión municipal de asociaciones**
