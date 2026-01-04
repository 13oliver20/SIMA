@echo off
REM Script para conectar con GitHub y subir el código
REM Repositorio: https://github.com/13oliver20/SIMA.git

echo ========================================
echo   Subiendo SIMA a GitHub
echo ========================================
echo.

set GIT="C:\Program Files\Git\bin\git.exe"

REM Verificar si Git está instalado
%GIT% --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Git no encontrado
    echo Por favor ejecuta primero: install_git.bat
    pause
    exit /b 1
)

echo Git encontrado!
echo.

REM Configurar usuario si no está configurado
echo Configurando Git...
%GIT% config --global user.name "Oliver" >nul 2>&1
%GIT% config --global user.email "13oliver20@github.com" >nul 2>&1
%GIT% config --global init.defaultBranch main >nul 2>&1

echo.
echo Inicializando repositorio...
%GIT% init

if %ERRORLEVEL% NEQ 0 (
    echo Ya existe un repositorio Git
)

echo.
echo Verificando archivos a incluir...
%GIT% status

echo.
echo IMPORTANTE: Verifica que "conexion.php" NO aparezca en la lista
echo Si aparece, presiona Ctrl+C para cancelar
echo.
pause

echo.
echo Agregando archivos...
%GIT% add .

echo.
echo Creando commit...
%GIT% commit -m "Initial commit: SIMA system with Supabase integration"

if %ERRORLEVEL% NEQ 0 (
    echo Nota: Es posible que ya exista un commit
)

echo.
echo Conectando con GitHub...
%GIT% remote add origin https://github.com/13oliver20/SIMA.git 2>nul

if %ERRORLEVEL% NEQ 0 (
    echo Repositorio remoto ya configurado, actualizando...
    %GIT% remote set-url origin https://github.com/13oliver20/SIMA.git
)

echo.
echo ========================================
echo   Subiendo codigo a GitHub...
echo ========================================
echo.
echo Se te pedira autenticacion:
echo - Usuario: 13oliver20
echo - Password: Personal Access Token (NO tu contraseña de GitHub)
echo.
echo Si no tienes un token, ve a:
echo https://github.com/settings/tokens
echo.

%GIT% push -u origin main

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ========================================
    echo   Error al subir a GitHub
    echo ========================================
    echo.
    echo Posibles soluciones:
    echo.
    echo 1. Asegurate de tener un Personal Access Token
    echo    https://github.com/settings/tokens
    echo.
    echo 2. Si ya existe contenido en GitHub, usa:
    %GIT% pull origin main --rebase
    echo    Luego ejecuta este script nuevamente
    echo.
    pause
    exit /b 1
)

echo.
echo ========================================
echo   EXITO! Codigo en GitHub
echo ========================================
echo.
echo Verifica en tu navegador:
echo https://github.com/13oliver20/SIMA
echo.
echo SIGUIENTE: Desplegar en Railway
echo Lee el archivo: PASOS_FINALES.md
echo.
pause
