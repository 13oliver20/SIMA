@echo off
echo ================================================
echo  INSTALACION RAPIDA DE PHP PARA WINDOWS
echo ================================================
echo.
echo Este script descargara e instalara PHP automaticamente
echo.
pause

echo.
echo Descargando PHP 8.3...
echo.

REM Crear carpeta temporal
mkdir C:\temp_php 2>nul

REM Descargar PHP usando PowerShell
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/php-8.3.1-Win32-vs16-x64.zip' -OutFile 'C:\temp_php\php.zip'}"

if not exist "C:\temp_php\php.zip" (
    echo.
    echo ERROR: No se pudo descargar PHP
    echo Por favor descarga manualmente desde: https://windows.php.net/download/
    pause
    exit /b 1
)

echo.
echo Extrayendo archivos...
echo.

REM Extraer PHP
powershell -Command "& {Expand-Archive -Path 'C:\temp_php\php.zip' -DestinationPath 'C:\php' -Force}"

if not exist "C:\php\php.exe" (
    echo.
    echo ERROR: No se pudo extraer PHP
    pause
    exit /b 1
)

echo.
echo Configurando PHP...
echo.

REM Copiar php.ini
copy "C:\php\php.ini-development" "C:\php\php.ini"

REM Habilitar extensiones de PostgreSQL usando PowerShell
powershell -Command "& {(Get-Content 'C:\php\php.ini') -replace ';extension=pdo_pgsql', 'extension=pdo_pgsql' -replace ';extension=pgsql', 'extension=pgsql' | Set-Content 'C:\php\php.ini'}"

echo.
echo Limpiando archivos temporales...
rd /s /q C:\temp_php

echo.
echo ================================================
echo  PHP INSTALADO CORRECTAMENTE EN: C:\php
echo ================================================
echo.
echo Ahora puedes ejecutar: start_server.bat
echo.
pause
