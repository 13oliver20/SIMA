@echo off
REM Script para subir nixpacks.toml a GitHub

echo ========================================
echo   Subiendo nixpacks.toml a GitHub
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando nixpacks.toml...
%GIT% add nixpacks.toml

echo.
echo Creando commit...
%GIT% commit -m "Add nixpacks.toml for Railway PHP detection"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Archivo subido
    echo ========================================
    echo.
    echo Railway detectara el cambio y re-desplegara automaticamente
    echo Espera 2-3 minutos y verifica en Railway
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
