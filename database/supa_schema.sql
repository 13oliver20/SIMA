-- Estructura de tabla para la tabla `acta_constitucion`
CREATE TABLE acta_constitucion (
  idacta_constitucion SERIAL PRIMARY KEY,
  fecha_fundacion date DEFAULT NULL,
  archivo_acta varchar(255) DEFAULT NULL,
  grupo_idgrupo int NOT NULL
);

-- Estructura de tabla para la tabla `acta_verificacion`
CREATE TABLE acta_verificacion (
  idacta_verificacion SERIAL PRIMARY KEY,
  num_verificacion varchar(45) NOT NULL
);

INSERT INTO acta_verificacion (idacta_verificacion, num_verificacion) VALUES
(1, 'PRIMERA VERIFICACION'),
(2, 'SEGUNDA VERIFICACION'),
(3, 'TERCERA VERIFICACION');

-- Estructura de tabla para la tabla `acta_verificacion_has_grupo`
CREATE TABLE acta_verificacion_has_grupo (
  grupo_idgrupo int NOT NULL,
  fecha_verificacion date NOT NULL,
  acta_verificacion_idacta_verificacion int NOT NULL,
  archivo_verificacion varchar(355) DEFAULT NULL
);

-- Estructura de tabla para la tabla `agrupamiento`
CREATE TABLE agrupamiento (
  idagrupamiento SERIAL PRIMARY KEY,
  cod_etiqueta varchar(15) NOT NULL,
  nom_agrupamiento varchar(45) NOT NULL
);

INSERT INTO agrupamiento (idagrupamiento, cod_etiqueta, nom_agrupamiento) VALUES
(1, 'TS', '27 ASOCIACIONES'),
(2, 'FV', 'FUACVRO'),
(3, 'CA', 'CACHINA'),
(4, 'OT', 'OTROS'),
(5, 'FS', 'FERIANTES SABATINOS');

-- Estructura de tabla para la tabla `cargo`
CREATE TABLE cargo (
  idcargo SERIAL PRIMARY KEY,
  tipo_cargo varchar(45) NOT NULL
);

INSERT INTO cargo (idcargo, tipo_cargo) VALUES
(13, 'PRESIDENTE'),
(14, 'VICEPRESIDENTE'),
(16, 'DELEGADO'),
(17, 'SECRETARIO'),
(49, 'DELEGADO DE DEPORTES'),
(51, 'DELEGADO DE ACTIVIDADES');

-- Estructura de tabla para la tabla `categoria`
CREATE TABLE categoria (
  idcategoria SERIAL PRIMARY KEY,
  tipo varchar(45) NOT NULL
);

INSERT INTO categoria (idcategoria, tipo) VALUES
(1, 'ASOCIACIONES'),
(2, 'MERCADOS');

-- Estructura de tabla para la tabla `dia_laborable`
-- Enum simulado con check constraint o varchar
CREATE TABLE dia_laborable (
  iddia_laborable SERIAL PRIMARY KEY,
  dia varchar(20) NOT NULL CHECK (dia IN ('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'))
);

INSERT INTO dia_laborable (iddia_laborable, dia) VALUES
(1, 'Lunes'),
(2, 'Martes'),
(3, 'Miércoles'),
(4, 'Jueves'),
(5, 'Viernes'),
(6, 'Sábado'),
(7, 'Domingo');

-- Estructura de tabla para la tabla `dia_laborable_has_grupo`
CREATE TABLE dia_laborable_has_grupo (
  dia_laborable_iddia_laborable int NOT NULL,
  grupo_idgrupo int NOT NULL
);

-- Estructura de tabla para la tabla `grupo`
CREATE TABLE grupo (
  idgrupo SERIAL PRIMARY KEY,
  etiqueta_grupo varchar(7) NOT NULL,
  nombre_grupo varchar(250) NOT NULL,
  ubicacion varchar(45) NOT NULL,
  agrupamiento_idagrupamiento int NOT NULL,
  categoria_idcategoria int NOT NULL,
  estado varchar(10) NOT NULL CHECK (estado IN ('Activo','Inactivo'))
);

-- Estructura de tabla para la tabla `junta_directiva`
CREATE TABLE junta_directiva (
  idjunta_directiva SERIAL PRIMARY KEY,
  socio_asociacion_socio_idsocio int NOT NULL,
  socio_asociacion_grupo_idgrupo int NOT NULL,
  cargo_idcargo int NOT NULL,
  fecha_inicio date NOT NULL,
  fecha_fin date NOT NULL,
  celular varchar(9) DEFAULT NULL,
  estado varchar(9) NOT NULL
);

-- Estructura de tabla para la tabla `padron_socios`
CREATE TABLE padron_socios (
  idpadron_socios SERIAL PRIMARY KEY,
  archivo_padron varchar(255) DEFAULT NULL,
  grupo_idgrupo int NOT NULL
);

-- Estructura de tabla para la tabla `personal`
CREATE TABLE personal (
  id SERIAL PRIMARY KEY,
  dni char(8) NOT NULL,
  nombres varchar(100) NOT NULL,
  apellidos varchar(100) NOT NULL,
  cargo varchar(100) DEFAULT NULL,
  creado timestamp NOT NULL DEFAULT current_timestamp
);

INSERT INTO personal (dni, nombres, apellidos, cargo, creado) VALUES
('47479350', 'DENNIS URIEL', 'PAXI', 'FISCALIZADOR', '2025-05-23 16:22:40'),
('70299922', 'AMALIA MARIBEL', 'QUISPE TUNI', 'GERENTE', '2025-05-23 16:31:54'),
('47479350', 'DENNIS URIEL', 'AÑASCO CHATA', 'ANALISTA DE SISTEMAS', '2025-05-24 03:28:49'),
('12345678', 'demo', 'demo', 'demo', '2025-09-01 00:23:16');

-- Tabla usuarios faltaba en el dump original pero se usa en login.php
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    correo VARCHAR(100) UNIQUE,
    contraseña VARCHAR(255),
    personal_id INT
);
-- Nota: Asegúrate de insertar un usuario administrador.

-- Estructura de tabla para la tabla `resolucion_gdh`
CREATE TABLE resolucion_gdh (
  idresolucion_gdh SERIAL PRIMARY KEY,
  num_resolucion varchar(45) NOT NULL,
  fecha_emision date NOT NULL,
  archivo_gdh varchar(255) DEFAULT NULL,
  grupo_idgrupo int NOT NULL
);

-- Estructura de tabla para la tabla `rubro_principal`
CREATE TABLE rubro_principal (
  idrubro char(2) NOT NULL PRIMARY KEY,
  nombre varchar(100) NOT NULL
);

INSERT INTO rubro_principal (idrubro, nombre) VALUES
('01', 'Alimentos y Bebidas'),
('02', 'Artículos de librería'),
('03', 'Farmacia y Salud'),
('04', 'Ferretería'),
('05', 'Mascotas'),
('06', 'Muebles'),
('07', 'Otros Productos'),
('08', 'Plastiqueria'),
('09', 'Productos De Belleza'),
('10', 'Productos de Higiene y Limpieza'),
('11', 'No recopilado'),
('12', 'Tecnología y hogar'),
('13', 'Alimentos y bebidas');

-- Estructura de tabla para la tabla `salida_campo`
CREATE TABLE salida_campo (
  idsalida SERIAL PRIMARY KEY,
  tipo_salida varchar(50) NOT NULL
);

INSERT INTO salida_campo (idsalida, tipo_salida) VALUES
(1, 'PRIMERA SALIDA'),
(2, 'SEGUNDA SALIDA'),
(3, 'TERCERA SALIDA');

-- Estructura de tabla para la tabla `socio`
CREATE TABLE socio (
  idsocio SERIAL PRIMARY KEY,
  dni varchar(8) NOT NULL,
  nombre varchar(45) NOT NULL,
  apellido_pat varchar(45) NOT NULL,
  apellido_mat varchar(45) DEFAULT NULL,
  genero char(1) NOT NULL,
  departamento varchar(45) NOT NULL,
  provincia varchar(45) NOT NULL,
  distrito varchar(45) DEFAULT NULL
);

-- Inserción de datos de ejemplo para socio
INSERT INTO socio (idsocio, dni, nombre, apellido_pat, apellido_mat, genero, departamento, provincia, distrito) VALUES
(14127, '40704128', 'AMPARO', 'RIVA', 'OSNAYO', 'F', 'PUNO', 'PUNO', 'PUNO');
-- (Añadir el resto de inserts si es necesario, pero recuerda resetear la secuencia después)

-- Resetear secuencias después de inserts manuales con IDs explícitos
SELECT setval('socio_idsocio_seq', (SELECT MAX(idsocio) FROM socio));
SELECT setval('cargo_idcargo_seq', (SELECT MAX(idcargo) FROM cargo));
SELECT setval('grupo_idgrupo_seq', (SELECT MAX(idgrupo) FROM grupo));
