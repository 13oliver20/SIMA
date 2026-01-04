@echo off
REM Script para subir correccion con ENTRYPOINT

echo ========================================
echo   Subiendo Correccion con ENTRYPOINT
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando Dockerfile...
%GIT% add Dockerfile

echo.
echo Creando commit...
%GIT% commit -m "Fix: Use ENTRYPOINT instead of CMD for PORT expansion"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! ENTRYPOINT configurado
    echo ========================================
    echo.
    echo Railway desplegara con ENTRYPOINT
    echo Espera 2-3 minutos
    echo.
    echo ENTRYPOINT garantiza la expansion de PORT
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
