# Guía de Migración a Supabase

Este proyecto ha sido adaptado para funcionar con Supabase (PostgreSQL) en lugar de MySQL local (XAMPP). Sigue estos pasos para completar la migración de la base de datos y la aplicación.

## Paso 1: Configurar Supabase

1.  Crea una cuenta y un nuevo proyecto en [Supabase](https://supabase.com/).
2.  Una vez creado el proyecto, ve a la sección **Project Settings (Configuración)** -> **Database**.
3.  Toma nota de los siguientes datos de conexión:
    -   **Host** (ej. `db.xyz.supabase.co`)
    -   **Database** (usualmente `postgres`)
    -   **Port** (5432)
    -   **User** (`postgres`)
    -   **Password** (la contraseña que definiste al crear el proyecto).

## Paso 2: Importar la Base de Datos

1.  En el panel de Supabase, ve a **SQL Editor**.
2.  Haz clic en **New Query**.
3.  Abre el archivo local `database/supa_schema.sql` que he generado.
4.  Copia todo su contenido y pégalo en el editor SQL de Supabase.
5.  Haz clic en **Run** para crear las tablas e insertar los datos iniciales.

## Paso 3: Configurar la Aplicación PHP

1.  Abre el archivo `comercio/includes/conexion.php`.
2.  Reemplaza los valores de marcador de posición con tus credenciales reales:
    ```php
    $host = 'db.tu-proyecto.supabase.co';
    $password = 'tu_contraseña_real';
    // ...
    ```
3.  (Opcional) Haz lo mismo en `comercio/includes/db.php` si utilizas scripts que dependan de él.

## Paso 4: Configurar PHP en tu entorno (XAMPP/Local)

Para que PHP pueda conectarse a PostgreSQL, necesitas habilitar las extensiones correspondientes.

1.  Abre el archivo `php.ini` de tu configuración PHP (en XAMPP suele estar en `C:\xampp\php\php.ini`).
2.  Busca las siguientes líneas y quita el punto y coma (`;`) del inicio para descomentarlas:
    ```ini
    extension=pgsql
    extension=pdo_pgsql
    ```
3.  Guarda el archivo y **reinicia** el servidor Apache en XAMPP.

## Notas sobre la Adaptación

-   Se ha creado un archivo `comercio/includes/PostgresAdapter.php`. Este archivo actúa como un puente ("shim") para que el código antiguo (que usaba `mysqli` para MySQL) funcione con PostgreSQL usando `PDO`.
-   Esto permite que la mayoría de tu código funcione sin cambios masivos. Sin embargo, si encuentras errores específicos en consultas complejas, podría ser necesario ajustar esas consultas SQL manualmente debido a diferencias entre MySQL y PostgreSQL (aunque se ha intentado cubrir lo básico).
