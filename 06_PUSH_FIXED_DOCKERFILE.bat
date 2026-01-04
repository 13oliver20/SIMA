@echo off
REM Script final para subir Dockerfile corregido

echo ========================================
echo   Subiendo Dockerfile Corregido
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando Dockerfile corregido...
%GIT% add Dockerfile

echo.
echo Creando commit...
%GIT% commit -m "Fix Dockerfile PORT variable for Railway"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Dockerfile corregido subido
    echo ========================================
    echo.
    echo Railway desplegara con Docker
    echo Espera 2-3 minutos
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
