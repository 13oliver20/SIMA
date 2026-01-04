@echo off
REM Script final - Corregir expansion de PORT

echo ========================================
echo   Subiendo Correccion Final
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

echo Agregando Dockerfile corregido...
%GIT% add Dockerfile

echo.
echo Creando commit...
%GIT% commit -m "Fix PORT variable expansion in Dockerfile CMD"

echo.
echo Subiendo a GitHub...
%GIT% push

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo   EXITO! Correccion subida
    echo ========================================
    echo.
    echo Railway desplegara con la correccion
    echo Espera 2-3 minutos
    echo.
) else (
    echo.
    echo ERROR al subir
    echo.
)

pause
