@echo off
REM Script para subir index.php de redireccion

echo ========================================
echo   Subiendo Redireccion Index
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando index.php...
%GIT% add comercio/index.php

echo.
echo Creando commit...
%GIT% commit -m "Add index.php redirect to login"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Redireccion subida
    echo ========================================
    echo.
    echo Railway desplegara con la redireccion
    echo Espera 2-3 minutos
    echo.
    echo Luego prueba: https://sima-production-f3e3.up.railway.app
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
