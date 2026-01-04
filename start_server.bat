@echo off
echo ========================================
echo  Iniciando Servidor PHP
echo ========================================
echo.
echo Buscando PHP...

REM Intentar rutas comunes de PHP
set PHP_PATH=

REM Verificar si PHP está en el PATH
where php >nul 2>&1
if %errorlevel% == 0 (
    set PHP_PATH=php
    goto :found
)

REM Verificar ubicaciones comunes
if exist "C:\php\php.exe" (
    set PHP_PATH=C:\php\php.exe
    goto :found
)

if exist "C:\xampp\php\php.exe" (
    set PHP_PATH=C:\xampp\php\php.exe
    goto :found
)

if exist "C:\wamp64\bin\php\php8.2.0\php.exe" (
    set PHP_PATH=C:\wamp64\bin\php\php8.2.0\php.exe
    goto :found
)

REM No se encontró PHP
echo ERROR: No se encontro PHP instalado
echo.
echo Por favor:
echo 1. Instala PHP siguiendo las instrucciones en INSTALL_PHP.md
echo 2. O ejecuta manualmente: C:\ruta\a\php.exe -S localhost:8000
echo.
pause
exit /b 1

:found
echo PHP encontrado en: %PHP_PATH%
echo.

REM Verificar extensiones de PostgreSQL
echo Verificando extensiones de PostgreSQL...
%PHP_PATH% -m | findstr "pdo_pgsql" >nul 2>&1
if %errorlevel% neq 0 (
    echo.
    echo ADVERTENCIA: La extension PDO_PGSQL no esta habilitada
    echo Por favor sigue las instrucciones en INSTALL_PHP.md para habilitarla
    echo.
)

echo.
echo ========================================
echo  Servidor iniciado en:
echo  http://localhost:8000
echo ========================================
echo.
echo Presiona Ctrl+C para detener el servidor
echo.

cd /d "%~dp0comercio"
%PHP_PATH% -S localhost:8000

pause
