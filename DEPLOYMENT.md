# 🚀 Guía Rápida de Despliegue

## Paso 1: Instalar Git

### Opción A: Instalación Automática (Recomendada)
```batch
# Doble clic en:
install_git.bat
```

Esto descargará e instalará Git automáticamente.

### Opción B: Instalación Manual
1. Ve a: https://git-scm.com/download/win
2. Descarga el instalador de 64-bit
3. Ejecuta el instalador con opciones por defecto
4. **Reinicia PowerShell** después de instalar

---

## Paso 2: Configurar Git y Repositorio

```batch
# Después de instalar Git, ejecuta:
setup_git_repo.bat
```

Este script:
- Configurará tu nombre y email en Git
- Inicializará el repositorio
- Creará el primer commit

---

## Paso 3: Crear Repositorio en GitHub

1. Ve a: https://github.com/new
2. **Repository name:** `sima-sistema-asociaciones`
3. **Description:** `Sistema de Información Municipal de Asociaciones`
4. **Visibilidad:** Privado ✅
5. **NO marques:** README, .gitignore, license
6. Clic en **"Create repository"**

---

## Paso 4: Conectar con GitHub

Copia la URL que GitHub te muestra, luego en PowerShell:

```powershell
cd "C:\Users\CONTROLADOR DWARF\Desktop\sima"

# Conectar con GitHub (reemplaza con tu URL)
git remote add origin https://github.com/TU-USUARIO/sima-sistema-asociaciones.git

# Subir el código
git push -u origin main
```

**Autenticación:**
- Usuario: tu username de GitHub
- Password: tu **Personal Access Token** (NO tu contraseña)

**Para crear un token:**
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. **Generate new token**
3. Permisos: ✅ `repo`
4. Copia el token y úsalo como password

---

## Paso 5: Desplegar en Railway

### 5.1 Crear Cuenta
1. Ve a: https://railway.app
2. **"Login"** → **"Login with GitHub"**

### 5.2 Nuevo Proyecto
1. **"New Project"** → **"Deploy from GitHub repo"**
2. Autoriza Railway a acceder a tus repos
3. Selecciona: `sima-sistema-asociaciones`

### 5.3 Configurar Variables de Entorno

En Railway, ve a tu proyecto → pestaña **"Variables"** → **"New Variable"**:

```
DB_HOST = db.ejtmcckwcfdgrqmdruax.supabase.co
DB_PORT = 5432
DB_USER = postgres
DB_PASSWORD = 6enniudV12@
DB_NAME = postgres
APP_ENV = production
```

### 5.4 Configurar Start Command

Railway → **"Settings"** → **"Start Command"**:
```bash
php -S 0.0.0.0:$PORT -t comercio
```

### 5.5 Deploy

Railway desplegará automáticamente. Espera 2-3 minutos.

### 5.6 Obtener URL

Railway → **"Settings"** → **"Domains"** → Verás tu URL pública

Ejemplo: `https://sima-production.up.railway.app`

---

## Paso 6: Verificar Deployment

1. Visita tu URL de Railway
2. Deberías ver la página de login
3. Intenta iniciar sesión
4. Si funciona: ¡Listo! 🎉

---

## 🔄 Para Futuros Cambios

Cada vez que modifiques código:

```powershell
cd "C:\Users\CONTROLADOR DWARF\Desktop\sima"

git add .
git commit -m "Descripción del cambio"
git push
```

Railway detectará el cambio y desplegará automáticamente.

---

## 🚨 Troubleshooting

### "Git not found"
→ Reinicia PowerShell después de instalar Git

### "Permission denied"
→ Verifica tu Personal Access Token de GitHub

### "Error 500 en producción"
→ Verifica las variables de entorno en Railway

### "Can't connect to database"
→ Asegúrate de que las credenciales de Supabase sean correctas

---

## 📋 Checklist

- [ ] Git instalado
- [ ] Git configurado (nombre, email)
- [ ] Repositorio inicializado localmente
- [ ] Primer commit creado
- [ ] Repositorio creado en GitHub
- [ ] Código subido a GitHub
- [ ] Cuenta en Railway creada
- [ ] Proyecto conectado desde GitHub
- [ ] Variables de entorno configuradas
- [ ] Start command configurado
- [ ] Deployment exitoso
- [ ] Sitio accesible desde internet

---

**¡Todo listo para producción!** 🚀
