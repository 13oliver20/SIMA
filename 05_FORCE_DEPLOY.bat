@echo off
REM Script para forzar nuevo deployment en Railway

echo ========================================
echo   Forzando Nuevo Deployment
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Actualizando README...
%GIT% add README.md

echo.
echo Creando commit...
%GIT% commit -m "Update README - trigger Railway deployment"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Cambio subido
    echo ========================================
    echo.
    echo Railway detectara el cambio y desplegara con Docker
    echo Espera 2-3 minutos y verifica en Railway
    echo.
    echo IMPORTANTE: Esta vez deberia usar el Dockerfile
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
