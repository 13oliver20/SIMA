@echo off
REM Script para configurar Git y crear el repositorio inicial
REM Ejecutar DESPUÉS de instalar Git

echo ========================================
echo   Configuracion de Git y Repositorio
echo ========================================
echo.

REM Verificar si Git está instalado
git --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Git no esta instalado o no se encuentra en PATH
    echo.
    echo Por favor:
    echo 1. Instala Git desde https://git-scm.com/download/win
    echo 2. Reinicia PowerShell
    echo 3. Ejecuta este script nuevamente
    echo.
    pause
    exit /b 1
)

echo Git detectado correctamente!
echo.

REM Solicitar configuración de usuario
set /p USERNAME="Ingresa tu nombre para Git: "
set /p EMAIL="Ingresa tu email para Git: "

echo.
echo Configurando Git...
git config --global user.name "%USERNAME%"
git config --global user.email "%EMAIL%"
git config --global init.defaultBranch main

echo.
echo Configuracion aplicada:
git config --global --list | findstr "user\|branch"

echo.
echo Inicializando repositorio Git...
git init

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo inicializar el repositorio
    pause
    exit /b 1
)

echo.
echo Verificando archivos a incluir (segun .gitignore)...
git status

echo.
echo Agregando archivos al repositorio...
git add .

echo.
echo Creando primer commit...
git commit -m "Initial commit: SIMA system with Supabase integration"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo crear el commit
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Repositorio creado exitosamente!
echo ========================================
echo.
echo Siguientes pasos:
echo.
echo 1. Ve a https://github.com/new
echo 2. Crea un repositorio llamado: sima-sistema-asociaciones
echo 3. Marca como Privado
echo 4. NO inicialices con README, .gitignore o license
echo 5. Copia la URL del repositorio
echo 6. Ejecuta:
echo.
echo    git remote add origin [URL-DE-TU-REPO]
echo    git push -u origin main
echo.
pause
