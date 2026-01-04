@echo off
REM Script para subir correccion final de PORT

echo ========================================
echo   Subiendo Correccion Final de PORT
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando archivos...
%GIT% add Dockerfile start.sh

echo.
echo Creando commit...
%GIT% commit -m "Fix PORT with startup script"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Correccion final subida
    echo ========================================
    echo.
    echo Railway desplegara con el script de inicio
    echo Espera 2-3 minutos
    echo.
    echo Esta vez deberia funcionar correctamente
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
