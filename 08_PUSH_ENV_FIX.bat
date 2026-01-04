@echo off
REM Script para subir correccion de variables de entorno

echo ========================================
echo   Subiendo Correccion de Variables ENV
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando archivos...
%GIT% add comercio/includes/conexion.php comercio/test.php

echo.
echo Creando commit...
%GIT% commit -m "Fix: Use environment variables for database connection"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Correccion subida
    echo ========================================
    echo.
    echo Ahora Railway usara las variables de entorno
    echo Espera 2-3 minutos para el deployment
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
