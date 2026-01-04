# Guía de Configuración del Servidor (Sin XAMPP)

## Opción 1: Usar el Servidor Integrado de PHP (Más Fácil)

Si tienes PHP instalado en tu sistema, puedes usar el servidor web integrado que viene con PHP.

### Paso 1: Verificar si tienes PHP instalado

Abre PowerShell o CMD y ejecuta:
```bash
php -v
```

Si ves información de la versión, tienes PHP instalado. Si no, descárgalo de [php.net](https://windows.php.net/download/).

### Paso 2: Habilitar extensiones de PostgreSQL

1. Descarga PHP 8.x desde [windows.php.net](https://windows.php.net/download/) si no lo tienes
2. Extrae el ZIP en `C:\php` (o donde prefieras)
3. Copia `php.ini-development` a `php.ini`
4. Abre `php.ini` en un editor de texto
5. Busca y descomenta (quita el `;` al inicio) estas líneas:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```
6. Guarda el archivo

### Paso 3: Ejecutar el servidor

Desde PowerShell o CMD, navega a la carpeta del proyecto:
```bash
cd "C:\Users\CONTROLADOR DWARF\Desktop\sima\comercio"
php -S localhost:8000
```

Ahora abre tu navegador en: `http://localhost:8000`

---

## Opción 2: Si tienes Apache instalado directamente

### Habilitar módulos de PostgreSQL

1. Localiza tu archivo `php.ini` (usualmente en `C:\php\php.ini` o `C:\Program Files\PHP\php.ini`)
2. Abre el archivo con un editor de texto
3. Busca y descomenta estas líneas:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```
4. Guarda el archivo

### Reiniciar Apache

**En Windows con Apache como servicio:**
```bash
# Opción 1: Desde servicios de Windows
services.msc
# Busca "Apache" y haz clic derecho > Reiniciar

# Opción 2: Desde CMD como administrador
net stop Apache2.4
net start Apache2.4
```

**Si Apache NO está como servicio:**
```bash
# Navega a la carpeta de Apache
cd "C:\Apache24\bin"
httpd.exe -k restart
```

---

## Opción 3: Instalar WAMP/Laragon (Alternativa a XAMPP)

Si prefieres una solución todo-en-uno:

1. **WAMP**: [wampserver.com](https://www.wampserver.com/en/)
2. **Laragon**: [laragon.org](https://laragon.org/) - Más moderno y ligero

Ambos incluyen Apache, PHP, MySQL y permiten habilitar fácilmente extensiones de PostgreSQL desde su interfaz.

---

## Verificar que PostgreSQL funciona

Crea un archivo `test_pgsql.php` en tu carpeta del proyecto:

```php
<?php
if (extension_loaded('pdo_pgsql')) {
    echo "✓ PDO PostgreSQL está habilitado\n";
} else {
    echo "✗ PDO PostgreSQL NO está habilitado\n";
}

if (extension_loaded('pgsql')) {
    echo "✓ PostgreSQL está habilitado\n";
} else {
    echo "✗ PostgreSQL NO está habilitado\n";
}
?>
```

Visita `http://localhost:8000/test_pgsql.php` (o la URL correspondiente) para verificar.

---

## ¿Qué método prefieres?

- **Servidor integrado de PHP**: Rápido, sin configuración adicional
- **Apache standalone**: Si ya lo tienes configurado
- **Instalar WAMP/Laragon**: Si quieres una solución completa
