@echo off
REM Script para limpiar archivos de configuracion conflictivos

echo ========================================
echo   Limpiando Archivos Conflictivos
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Eliminando archivos conflictivos...
del nixpacks.toml
del start.sh

echo.
echo Agregando cambios a Git...
%GIT% add -A

echo.
echo Creando commit...
%GIT% commit -m "Clean up: remove nixpacks.toml and start.sh"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Archivos eliminados
    echo ========================================
    echo.
    echo Railway usara solo el Start Command configurado
    echo Espera 2-3 minutos para el deployment
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
