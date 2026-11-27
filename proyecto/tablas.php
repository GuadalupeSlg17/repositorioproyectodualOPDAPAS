<?php

require 'conexion.php';

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql_regiones = "CREATE TABLE IF NOT EXISTS regiones (
    id_region INT AUTO_INCREMENT PRIMARY KEY,
    nombre_region VARCHAR(60) NOT NULL
)";

if ($conexion->query($sql_regiones) === TRUE) {
    echo "Tabla 'regiones' creada correctamente.<br>";
} else {
    die("Error al crear regiones: " . $conexion->error);
}



$sql_comunidades = "CREATE TABLE IF NOT EXISTS comunidades (
    id_comunidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comunidad VARCHAR(100) NOT NULL,
    id_region INT NOT NULL,
    FOREIGN KEY (id_region) REFERENCES regiones(id_region)
    ON DELETE CASCADE
)";

if ($conexion->query($sql_comunidades) === TRUE) {
    echo "Tabla 'comunidades' creada correctamente.<br>";
} else {
    die("Error al crear comunidades: " . $conexion->error);
}


$sql_solicitudes = "CREATE TABLE IF NOT EXISTS solicitudes (
    id_solicitante INT AUTO_INCREMENT PRIMARY KEY,
    nombre_solicitante VARCHAR(100) NOT NULL,
    puesto VARCHAR(50) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    calle_beneficiada VARCHAR(100) NOT NULL,

    cantidad_mat1 INT NOT NULL,
    descripcion_mat1 VARCHAR(300) NOT NULL,
    categoria_mat1 VARCHAR(20) NOT NULL,
    metros_mat1 DECIMAL(10,2) NOT NULL,

    cantidad_mat2 INT DEFAULT 0,
    descripcion_mat2 VARCHAR(300) DEFAULT NULL,
    categoria_mat2 VARCHAR(20) DEFAULT NULL,
    metros_mat2 DECIMAL(10,2) DEFAULT NULL,

    id_region INT NOT NULL,
    id_comunidad INT NOT NULL,

    FOREIGN KEY (id_region) REFERENCES regiones(id_region),
    FOREIGN KEY (id_comunidad) REFERENCES comunidades(id_comunidad),

    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conexion->query($sql_solicitudes) === TRUE) {
    echo "Tabla 'solicitudes' creada correctamente.<br>";
} else {
    die("Error al crear solicitudes: " . $conexion->error);
}




if ($conexion->query("DELETE FROM comunidades") === TRUE) {
    echo "Comunidades anteriores eliminadas.<br>";
} else {
    die("Error al eliminar comunidades: " . $conexion->error);
}

if ($conexion->query("DELETE FROM regiones") === TRUE) {
    echo "Regiones anteriores eliminadas.<br>";
} else {
    die("Error al eliminar regiones: " . $conexion->error);
}



$conexion->query("ALTER TABLE regiones AUTO_INCREMENT = 1");
$conexion->query("ALTER TABLE comunidades AUTO_INCREMENT = 1");



$sql_insert_regiones = "INSERT INTO regiones
    (id_region, nombre_region)
VALUES
    (1, 'I HUITZIZILAPAN'),
    (2, 'II TLALMIMILOLPAN'),
    (3, 'III ATARASQUILLO'),
    (4, 'IV AMEYALCO'),
    (5, 'V LERMA'),
    (6, 'VI TULTEPEC'),
    (7, 'VII PERALTA-XOCHICUAUTLA-ANALCO'),
    (8, 'VIII PARTIDAS - PARQUE INDUSTRIAL')
";

if ($conexion->query($sql_insert_regiones) === TRUE) {
    echo "Regiones insertadas correctamente.<br>";
} else {
    die("Error al insertar regiones: " . $conexion->error);
}



$sql_insert_comunidades = "INSERT INTO comunidades
    (id_comunidad, nombre_comunidad, id_region)
VALUES

/* Región 1: HUITZIZILAPAN */

(1, 'COLONIA ADOLFO LÓPEZ MATEOS HUITZIZILAPAN', 1),
(2, 'COLONIA GUADALUPE VICTORIA HUITZIZILAPAN', 1),
(3, 'FLOR DE GALLO HUITZIZILAPAN', 1),
(4, 'LA UNIDAD HUITZIZILAPAN', 1),
(5, 'LAS MESAS HUITZIZILAPAN', 1),
(6, 'SAN AGUSTÍN HUITZIZILAPAN', 1),
(7, 'SAN LORENZO HUITZIZILAPAN', 1),
(8, 'SAN MARTÍN, LAS RAJAS HUITZIZILAPAN', 1),
(9, 'SAN PEDRO HUITZIZILAPAN', 1),
(10, 'SANTA CRUZ HUITZIZILAPAN', 1),
(11, 'ZACAMULPA HUITZIZILAPAN', 1),
(12, 'COLONIA N´DEXI HUITZIZILAPAN', 1),

/* Región 2: TLALMIMILOLPAN */

(13, 'BARRANCA GRANDE TLALMIMILOLPAN', 2),
(14, 'COLONIA ÁLVARO OBREGÓN TLALMIMILOLPAN', 2),
(15, 'LA REFORMA TLALMIMILOLPAN', 2),
(16, 'METATE VIEJO TLALMIMILOLPAN', 2),
(17, 'PUEBLO NUEVO TLALMIMILOLPAN', 2),
(18, 'SANTA CATARINA TLALMIMILOLPAN', 2),
(19, 'SANTA MARÍA TLALMIMILOLPAN', 2),
(20, 'ZACAMULPA TLALMIMILOLPAN', 2),

/* Región 3: ATARASQUILLO */

(21, 'SAN MATEO ATARASQUILLO', 3),
(22, 'SANTA CRUZ CHIGNAHUAPAN', 3),
(23, 'SANTA MARÍA ATARASQUILLO', 3),

/* Región 4: AMEYALCO */

(24, 'AMOMOLULCO', 4),
(25, 'CAÑADA DE ALFÉREZ', 4),
(26, 'SALAZAR', 4),
(27, 'SAN JOSÉ EL LLANITO', 4),
(28, 'SAN MIGUEL AMEYALCO', 4),

/* Región 5: LERMA */

(29, 'COLONIA AURIS', 5),
(30, 'COLONIA CIDECO', 5),
(31, 'COLONIA CENTRO', 5),
(32, 'COLONIA EL CALVARIO, LA MERCED', 5),
(33, 'COLONIA EL PANTEÓN', 5),
(34, 'COLONIA GUADALUPE LA CIÉNEGA', 5),
(35, 'CONJUNTO URBANO HABITACIONAL VALLE DE ENCINOS', 5),
(36, 'COLONIA LA ESTACIÓN', 5),
(37, 'CONJUNTO URBANO HABITACIONAL HACIENDA LERMA', 5),
(38, 'COLONIA LA MOTA', 5),
(39, 'COLONIA VALLE DE LERMA', 5),
(40, 'COLONIA INFONAVIT', 5),

/* Región 6: TULTEPEC */

(41, 'COLONIA ALFREDO DEL MAZO', 6),
(42, 'COLONIA TOMAPA', 6),
(43, 'SAN PEDRO TULTEPEC', 6),
(44, 'SANTA CRUZ TULTEPEC', 6),

/* Región 7: PERALTA-XOCHICUAUTLA-ANALCO */

(45, 'LA CONCEPCIÓN XOCHICUAUTLA', 7),
(46, 'EL ESPINO PERALTA', 7),
(47, 'SAN FRANCISCO XOCHICUAUTLA', 7),
(48, 'COLONIA AGRÍCOLA ANALCO', 7),
(49, 'SAN NICOLÁS PERALTA', 7),
(50, 'SANTIAGO ANALCO', 7),

/* Región 8: PARTIDAS - PARQUE INDUSTRIAL */

(51, 'COLONIA LA BOMBA', 8),
(52, 'CONJUNTO URBANO HABITACIONAL BOSQUES DE LERMA', 8),
(53, 'CONJUNTO URBANO HABITACIONAL PASEOS DE LERMA', 8),
(54, 'CONJUNTO URBANO HABITACIONAL GALAXIAS LERMA', 8),
(55, 'CONJUNTO URBANO HABITACIONAL CEDROS 4000', 8),
(56, 'CONJUNTO URBANO HABITACIONAL EL PORVENIR II', 8),
(57, 'CONJUNTO URBANO HABITACIONAL REAL SANTA CLARA I', 8),
(58, 'COLONIA EMILIANO ZAPATA', 8),
(59, 'CONJUNTO URBANO HABITACIONAL HACIENDA SANTA CLARA', 8),
(60, 'COLONIA ISIDRO FABELA', 8)
";

if ($conexion->query($sql_insert_comunidades) === TRUE) {
    echo "Comunidades insertadas correctamente.<br>";
} else {
    die("Error al insertar comunidades: " . $conexion->error);
}

$sql_material_solicitado = "CREATE TABLE IF NOT EXISTS material_solicitado (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitante INT NOT NULL,

    cantidad_1 INT NOT NULL,
    descripcion_1 VARCHAR(32) NOT NULL,
    categoria_1 VARCHAR(20) NOT NULL,
    metros_1 DECIMAL(10,2) NOT NULL,

    cantidad_2 INT DEFAULT 0,
    descripcion_2 VARCHAR(32) DEFAULT NULL,
    categoria_2 VARCHAR(20) DEFAULT NULL,
    metros_2 DECIMAL(10,2) DEFAULT NULL,

    CONSTRAINT fk_solicitante
    FOREIGN KEY (id_solicitante)
    REFERENCES solicitudes(id_solicitante)
)";

if ($conexion->query($sql_material_solicitado) === TRUE) {
    echo "Tabla 'material_solicitado' creada correctamente.<br>";
} else {
    die("Error al crear material_solicitado: " . $conexion->error);
}

echo "<br>";
echo "Base de datos configurada correctamente.<br>";
echo "<a href='formulario.php'>Ir al formulario</a>";

$conexion->close();

?>