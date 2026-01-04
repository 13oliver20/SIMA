# 🏛️ SIMA - Sistema de Información Municipal de Asociaciones

Sistema web para la gestión integral de asociaciones, socios, actas, juntas directivas y documentación legal.

## 🚀 Tecnologías

- **Backend:** PHP 8.2
- **Base de Datos:** PostgreSQL (Supabase)
- **Frontend:** HTML, CSS, JavaScript, Bootstrap (SB Admin 2)
- **Librerías:** DataTables, Chart.js, jQuery

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
   git clone https://github.com/tu-usuario/sima-sistema-asociaciones.git
   cd sima-sistema-asociaciones
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

4. **Crear Usuario Inicial**
   Usa el generador de hash para crear tu primer usuario:
   ```bash
   php -S localhost:8000
   # Navega a: http://localhost:8000/generar_hash.php
   ```
   
   Copia el hash generado y ejecútalo en Supabase:
   ```sql
   INSERT INTO usuarios (nombre, correo, contraseña, personal_id)
   VALUES ('Admin', 'admin@tudominio.com', '$2y$10$...', 1);
   ```

5. **Iniciar Servidor Local**
   ```bash
   cd comercio
   php -S localhost:8000
   ```

6. **Acceder al Sistema**
   - URL: http://localhost:8000
   - Login: http://localhost:8000/login.php

## 🌐 Despliegue en Producción

### Opción A: Railway.app (Recomendado)

1. Crea una cuenta en [railway.app](https://railway.app)
2. Conecta tu repositorio de GitHub
3. Configura las variables de entorno:
   ```
   DB_HOST=db.xxx.supabase.co
   DB_PORT=5432
   DB_USER=postgres
   DB_PASSWORD=tu_password
   DB_NAME=postgres
   ```
4. Deploy automático

### Opción B: Hosting Tradicional (cPanel)

1. Sube los archivos vía FTP
2. Configura `conexion.php` con tus credenciales
3. Asegúrate de que PHP 8.2+ y extensiones PostgreSQL estén instaladas

## 📁 Estructura del Proyecto

```
sima/
├── comercio/              # Aplicación principal
│   ├── includes/          # Archivos de configuración
│   │   ├── conexion.php   # Configuración de BD (no subir a Git)
│   │   ├── PostgresAdapter.php
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── modules/           # Módulos del sistema
│   │   ├── asociaciones/
│   │   ├── socios/
│   │   ├── junta_directiva/
│   │   └── ...
│   ├── login.php          # Página de login
│   ├── index.php          # Dashboard
│   └── ...
├── database/              # Scripts SQL
│   └── supa_schema_clean.sql
├── .gitignore
├── .env.example
└── README.md
```

## 🔒 Seguridad

> [!CAUTION]
> **NUNCA** subas credenciales a GitHub. Usa siempre variables de entorno.

- Las contraseñas se almacenan con hash bcrypt
- Usa HTTPS en producción
- Configura `error_reporting` apropiadamente

## 🧪 Pruebas

El sistema ha sido probado exitosamente con:
- ✅ 8 módulos principales verificados
- ✅ 0 bugs críticos
- ✅ 100% funcionalidad core operativa
- ✅ Compatible con PostgreSQL/Supabase

Ver `walkthrough.md` para reporte completo de pruebas.

## 📝 Módulos Disponibles

- ✅ **Dashboard** - Estadísticas y resumen general
- ✅ **Socios** - Búsqueda, registro, listado
- ✅ **Grupos** - Gestión de grupos y asociaciones
- ✅ **Junta Directiva** - Administración de juntas
- ✅ **Actas** - Verificación y constitución
- ✅ **Documentos** - Gestión documental
- ✅ **Reportes** - Cruce de información, días laborables

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama de features (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agrega nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es de uso interno. Todos los derechos reservados.

## 👨‍💻 Soporte

Para soporte y preguntas, contacta al administrador del sistema.

---

**Desarrollado con ❤️ para la gestión municipal de asociaciones**
