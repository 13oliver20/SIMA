# Instalación Rápida de PHP para Windows

## Paso 1: Descargar PHP

1. Visita: https://windows.php.net/download/
2. Descarga **PHP 8.2 o 8.3** (Thread Safe, x64)
3. Extrae el ZIP en `C:\php`

## Paso 2: Configurar extensiones de PostgreSQL

1. Ve a la carpeta `C:\php`
2. Copia el archivo `php.ini-development` y renómbralo a `php.ini`
3. Abre `php.ini` con un editor de texto
4. Busca estas líneas y quita el `;` del inicio:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```
5. Guarda el archivo

## Paso 3: Agregar PHP al PATH (Opcional pero recomendado)

1. Presiona `Windows + R`, escribe `sysdm.cpl` y presiona Enter
2. Ve a la pestaña "Opciones avanzadas"
3. Haz clic en "Variables de entorno"
4. En "Variables del sistema", busca `Path` y haz clic en "Editar"
5. Haz clic en "Nuevo" y agrega: `C:\php`
6. Haz clic en "Aceptar" en todas las ventanas
7. **Reinicia PowerShell o CMD**

## Paso 4: Ejecutar el proyecto

Abre una nueva ventana de PowerShell o CMD y ejecuta:

```bash
cd "C:\Users\CONTROLADOR DWARF\Desktop\sima\comercio"
C:\php\php.exe -S localhost:8000
```

Si agregaste PHP al PATH, simplemente:
```bash
cd "C:\Users\CONTROLADOR DWARF\Desktop\sima\comercio"
php -S localhost:8000
```

Abre tu navegador en: http://localhost:8000

---

## Alternativa más rápida: Usar el script de inicio

He creado un archivo `start_server.bat` que puedes usar directamente.
Solo haz doble clic en él y se abrirá el servidor automáticamente.
