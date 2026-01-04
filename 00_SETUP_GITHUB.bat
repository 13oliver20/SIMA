@echo off
REM Script Final para Configurar Repositorio Git
REM Git ya está instalado, ahora configuramos el repositorio

echo ========================================
echo   SIMA - Configuracion de Git
echo ========================================
echo.

REM Usar ruta completa de Git
set GIT="C:\Program Files\Git\bin\git.exe"

REM Verificar instalación
%GIT% --version
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Git no instalado correctamente
    echo Instala Git desde: https://git-scm.com/download/win
    pause
    exit /b 1
)

echo.
echo === Paso 1: Configurar nombre y email ===
set /p USERNAME="Tu nombre: "
set /p EMAIL="Tu email: "

%GIT% config --global user.name "%USERNAME%"
%GIT% config --global user.email "%EMAIL%"
%GIT% config --global init.defaultBranch main

echo.
echo Configuracion aplicada:
%GIT% config --global --list | findstr "user\|branch"

echo.
echo === Paso 2: Inicializar repositorio ===
%GIT% init

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo inicializar
    pause
    exit /b 1
)

echo.
echo === Paso 3: Verificar archivos (segun .gitignore) ===
%GIT% status

echo.
echo IMPORTANTE: Verifica que conexion.php NO aparezca arriba
echo Si aparece, hay un problema con .gitignore
echo.
pause

echo.
echo === Paso 4: Agregar archivos ===
%GIT% add .

echo.
echo === Paso 5: Crear primer commit ===
%GIT% commit -m "Initial commit: SIMA system with Supabase integration"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: No se pudo crear commit
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Repositorio Local Creado!
echo ========================================
echo.
echo SIGUIENTE PASO:
echo.
echo 1. Ve a: https://github.com/new
echo 2. Repository name: sima-sistema-asociaciones
echo 3. Description: Sistema de Informacion Municipal de Asociaciones
echo 4. Privado: SI
echo 5. NO inicializar con README
echo 6. Clic en "Create repository"
echo.
echo 7. Copia la URL que te muestra GitHub
echo 8. Vuelve aqui y presiona cualquier tecla
echo.
pause

echo.
set /p REPO_URL="Pega la URL de tu repositorio de GitHub: "

echo.
echo Conectando con GitHub...
%GIT% remote add origin %REPO_URL%

echo.
echo Subiendo codigo a GitHub...
%GIT% push -u origin main

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: No se pudo subir a GitHub
    echo.
    echo Posibles causas:
    echo 1. URL incorrecta
    echo 2. No tienes autenticacion configurada
    echo.
    echo SOLUCION:
    echo Si te pide usuario/password:
    echo - Usuario: tu username de GitHub
    echo - Password: Personal Access Token (NO tu contraseña)
    echo.
    echo Para crear token:
    echo 1. GitHub -^> Settings -^> Developer settings
    echo 2. Personal access tokens -^> Tokens (classic)
    echo 3. Generate new token
    echo 4. Permisos: repo
    echo.
    pause
    exit /b 1
)

echo.
echo ========================================
echo   EXITO! Codigo en GitHub
echo ========================================
echo.
echo Verifica en tu navegador que los archivos esten en GitHub
echo.
echo SIGUIENTE: Desplegar en Railway
echo.
pause
