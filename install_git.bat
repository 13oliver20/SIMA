@echo off
REM Script para instalar Git en Windows de forma silenciosa
REM Este script descarga e instala Git con configuraciones recomendadas

echo ========================================
echo   Instalador Automatico de Git
echo ========================================
echo.
echo Este script instalara Git para Windows
echo con las configuraciones recomendadas.
echo.
pause

REM Descargar Git (versión 64-bit)
echo Descargando Git para Windows...
powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/git-for-windows/git/releases/download/v2.43.0.windows.1/Git-2.43.0-64-bit.exe' -OutFile '%TEMP%\GitInstaller.exe'}"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo descargar Git
    echo Por favor descarga manualmente desde: https://git-scm.com/download/win
    pause
    exit /b 1
)

REM Instalar Git de forma silenciosa con configuraciones recomendadas
echo.
echo Instalando Git...
"%TEMP%\GitInstaller.exe" /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEAPPLICATIONS /RESTARTAPPLICATIONS /Components="icons,ext\reg\shellhere,assoc,assoc_sh"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: La instalacion fallo
    pause
    exit /b 1
)

REM Limpiar archivo temporal
del "%TEMP%\GitInstaller.exe"

echo.
echo ========================================
echo   Git instalado exitosamente!
echo ========================================
echo.
echo IMPORTANTE: Cierra y vuelve a abrir PowerShell
echo para que Git funcione correctamente.
echo.
pause
