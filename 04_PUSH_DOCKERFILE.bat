@echo off
REM Script para subir Dockerfile a GitHub

echo ========================================
echo   Subiendo Dockerfile a GitHub
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando Dockerfile...
%GIT% add Dockerfile

echo.
echo Creando commit...
%GIT% commit -m "Add Dockerfile for Railway deployment"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Dockerfile subido
    echo ========================================
    echo.
    echo Railway usara Docker para compilar
    echo Espera 2-3 minutos y verifica en Railway
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
