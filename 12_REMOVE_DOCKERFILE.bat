@echo off
REM Script para eliminar Dockerfile de GitHub

echo ========================================
echo   Eliminando Dockerfile de GitHub
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Eliminando Dockerfile del repositorio...
%GIT% rm Dockerfile

echo.
echo Creando commit...
%GIT% commit -m "Remove Dockerfile - use Railway native PHP support"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Dockerfile eliminado
    echo ========================================
    echo.
    echo Ahora configura el Start Command en Railway
    echo Settings -^> Deploy -^> Custom Start Command
    echo.
    echo Comando: php -S 0.0.0.0:$PORT -t comercio
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
