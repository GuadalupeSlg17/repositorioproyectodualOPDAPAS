<?php
require 'conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ver_solicitudes.php');
    exit;
}

$id = intval($_GET['id']);

/* =========================================================
   FUNCIÓN PARA CALCULAR METROS
   ========================================================= */

function calcularMetros($cantidad, $categoria)
{
    $cantidad = floatval($cantidad);
    $categoria = strtolower(trim($categoria));

    if ($categoria === 'agua potable') {
        return $cantidad * 0.60;
    }

    if ($categoria === 'drenaje') {
        return $cantidad * 0.90;
    }

    return 0;
}

/* =========================================================
   OBTENER SOLICITUD
   ========================================================= */

$stmt = $conexion->prepare(
    "SELECT * FROM solicitudes WHERE id_solicitante = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Solicitud no encontrada.");
}

$solicitud = $resultado->fetch_assoc();
$stmt->close();

/* =========================================================
   OBTENER TODOS LOS MATERIALES DEL FOLIO
   ========================================================= */

$folioOriginal = $solicitud['folio'];

$stmtMateriales = $conexion->prepare(
    "SELECT
        id_material,
        id_solicitante,
        folio,
        cantidad,
        descripcion,
        categoria,
        metros
     FROM material_solicitado
     WHERE id_solicitante = ?
     AND folio = ?
     ORDER BY id_material ASC"
);

$stmtMateriales->bind_param(
    "is",
    $id,
    $folioOriginal
);

$stmtMateriales->execute();

$resultadoMateriales = $stmtMateriales->get_result();

$materiales = [];

while ($material = $resultadoMateriales->fetch_assoc()) {
    $materiales[] = $material;
}

$stmtMateriales->close();

/* =========================================================
   COMPATIBILIDAD CON SOLICITUDES ANTIGUAS
   Si no existe material_solicitado, usamos mat1
   ========================================================= */

if (count($materiales) === 0) {

    $materiales[] = [
        'id_material'   => 0,
        'id_solicitante'=> $id,
        'folio'         => $folioOriginal,
        'cantidad'      => $solicitud['cantidad_mat1'] ?? '',
        'descripcion'   => $solicitud['descripcion_mat1'] ?? '',
        'categoria'     => $solicitud['categoria_mat1'] ?? '',
        'metros'        => $solicitud['metros_mat1'] ?? ''
    ];
}

$mensaje = '';

/* =========================================================
   GUARDAR CAMBIOS
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre        = trim($_POST['nombre_solicitante'] ?? '');
    $puesto        = trim($_POST['puesto'] ?? '');
    $telefono      = trim($_POST['telefono'] ?? '');
    $calle         = trim($_POST['calle_beneficiada'] ?? '');
    $region        = trim($_POST['nombre_region'] ?? '');
    $comunidad     = trim($_POST['nombre_comunidad'] ?? '');

    /*
     * El folio NO se cambia.
     * Esto es importante porque los materiales están relacionados
     * con ese mismo folio.
     */
    $folio = $folioOriginal;

    $materialesPost = $_POST['materiales'] ?? [];

    try {

        $conexion->begin_transaction();

        /* =====================================================
           ACTUALIZAR DATOS GENERALES DE LA SOLICITUD
           ===================================================== */

        $update = $conexion->prepare(
            "UPDATE solicitudes SET
                nombre_solicitante = ?,
                puesto = ?,
                telefono = ?,
                calle_beneficiada = ?,
                nombre_region = ?,
                nombre_comunidad = ?
             WHERE id_solicitante = ?"
        );

        $update->bind_param(
            "ssssssi",
            $nombre,
            $puesto,
            $telefono,
            $calle,
            $region,
            $comunidad,
            $id
        );

        if (!$update->execute()) {
            throw new Exception(
                "Error al actualizar la solicitud."
            );
        }

        $update->close();

        /* =====================================================
           PREPARAR CONSULTAS DE MATERIALES
           ===================================================== */

        $updateMaterial = $conexion->prepare(
            "UPDATE material_solicitado SET
                cantidad = ?,
                descripcion = ?,
                categoria = ?,
                metros = ?
             WHERE id_material = ?
             AND id_solicitante = ?
             AND folio = ?"
        );

        $insertMaterial = $conexion->prepare(
            "INSERT INTO material_solicitado
            (
                id_solicitante,
                folio,
                cantidad,
                descripcion,
                categoria,
                metros
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $deleteMaterial = $conexion->prepare(
            "DELETE FROM material_solicitado
             WHERE id_material = ?
             AND id_solicitante = ?
             AND folio = ?"
        );

        /* =====================================================
           VARIABLES PARA EL PRIMER MATERIAL
           Mantienen compatibilidad con solicitudes
           ===================================================== */

        $primerMaterial = null;

        /* =====================================================
           RECORRER TODOS LOS MATERIALES
           ===================================================== */

        foreach ($materialesPost as $material) {

            $idMaterial = intval(
                $material['id_material'] ?? 0
            );

            $cantidad = intval(
                $material['cantidad'] ?? 0
            );

            $descripcion = trim(
                $material['descripcion'] ?? ''
            );

            $categoria = trim(
                $material['categoria'] ?? ''
            );

            $eliminar = intval(
                $material['eliminar'] ?? 0
            );

            /* ===============================================
               SI SE MARCÓ PARA ELIMINAR
               =============================================== */

            if ($eliminar === 1 && $idMaterial > 0) {

                $deleteMaterial->bind_param(
                    "iis",
                    $idMaterial,
                    $id,
                    $folio
                );

                $deleteMaterial->execute();

                continue;
            }

            /* ===============================================
               IGNORAR MATERIAL VACÍO
               =============================================== */

            if (
                $cantidad <= 0 ||
                empty($descripcion) ||
                empty($categoria)
            ) {
                continue;
            }

            /* ===============================================
               CALCULAR METROS
               =============================================== */

            $metros = calcularMetros(
                $cantidad,
                $categoria
            );

            /* ===============================================
               GUARDAR EL PRIMER MATERIAL
               =============================================== */

            if ($primerMaterial === null) {

                $primerMaterial = [
                    'cantidad' => $cantidad,
                    'descripcion' => $descripcion,
                    'categoria' => $categoria,
                    'metros' => $metros
                ];
            }

            /* ===============================================
               ACTUALIZAR MATERIAL EXISTENTE
               =============================================== */

            if ($idMaterial > 0) {

                $updateMaterial->bind_param(
                    "issdiis",
                    $cantidad,
                    $descripcion,
                    $categoria,
                    $metros,
                    $idMaterial,
                    $id,
                    $folio
                );

                $updateMaterial->execute();

            }

            /* ===============================================
               INSERTAR MATERIAL NUEVO
               =============================================== */

            else {

                $insertMaterial->bind_param(
                    "isissd",
                    $id,
                    $folio,
                    $cantidad,
                    $descripcion,
                    $categoria,
                    $metros
                );

                $insertMaterial->execute();
            }
        }

        $updateMaterial->close();
        $insertMaterial->close();
        $deleteMaterial->close();

        /* =====================================================
           ACTUALIZAR CAMPOS ANTIGUOS mat1
           ===================================================== */

        if ($primerMaterial !== null) {

            $cantidad1 = $primerMaterial['cantidad'];
            $descripcion1 = $primerMaterial['descripcion'];
            $categoria1 = $primerMaterial['categoria'];
            $metros1 = $primerMaterial['metros'];

            $updateLegacy = $conexion->prepare(
                "UPDATE solicitudes SET
                    cantidad_mat1 = ?,
                    descripcion_mat1 = ?,
                    categoria_mat1 = ?,
                    metros_mat1 = ?
                 WHERE id_solicitante = ?"
            );

            $updateLegacy->bind_param(
                "issdi",
                $cantidad1,
                $descripcion1,
                $categoria1,
                $metros1,
                $id
            );

            $updateLegacy->execute();
            $updateLegacy->close();
        }

        /* =====================================================
           CONFIRMAR
           ===================================================== */

        $conexion->commit();

        $mensaje = '
        <div class="mensaje-exito">
            ✅ Solicitud y materiales actualizados correctamente.
        </div>';

        /* Volver a consultar la solicitud */

        $stmt = $conexion->prepare(
            "SELECT * FROM solicitudes
             WHERE id_solicitante = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $solicitud = $stmt
            ->get_result()
            ->fetch_assoc();

        $stmt->close();

        /* Volver a consultar materiales */

        $stmtMateriales = $conexion->prepare(
            "SELECT
                id_material,
                id_solicitante,
                folio,
                cantidad,
                descripcion,
                categoria,
                metros
             FROM material_solicitado
             WHERE id_solicitante = ?
             AND folio = ?
             ORDER BY id_material ASC"
        );

        $stmtMateriales->bind_param(
            "is",
            $id,
            $folioOriginal
        );

        $stmtMateriales->execute();

        $resultadoMateriales =
            $stmtMateriales->get_result();

        $materiales = [];

        while (
            $material = $resultadoMateriales->fetch_assoc()
        ) {
            $materiales[] = $material;
        }

        $stmtMateriales->close();

    } catch (Exception $e) {

        $conexion->rollback();

        $mensaje = '
        <div class="mensaje-error">
            ❌ Error al actualizar: '
            . htmlspecialchars($e->getMessage())
            . '
        </div>';
    }
}

$folioMostrar = !empty($solicitud['folio'])
    ? $solicitud['folio']
    : 'SIN FOLIO';
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>
Editar Solicitud - <?= htmlspecialchars($folioMostrar) ?>
</title>

<link rel="icon" type="image/png" href="favicon.png">

<style>

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(
        135deg,
        #f8d4e8 0%,
        #a0c9eb 30%,
        #b7cce7 60%,
        #cac2f3 100%
    );

    min-height: 100vh;

    display: flex;
    justify-content: center;
    align-items: center;

    padding: 20px;
}

.container {

    max-width: 820px;
    width: 100%;

    background: white;

    padding: 30px;

    border-radius: 12px;

    box-shadow:
        0 4px 15px rgba(0,0,0,0.05);
}

h2 {

    color: #333;

    border-bottom:
        3px solid #1257b1;

    padding-bottom: 10px;

    margin-bottom: 20px;
}

.folio-titulo {

    color: #1257b1;

    font-family:
        'Courier New', monospace;

    font-size: 18px;

    background: #e8f0fe;

    padding: 4px 12px;

    border-radius: 6px;

    display: inline-block;
}

.form-group {

    margin-bottom: 15px;
}

label {

    display: block;

    font-weight: bold;

    margin-bottom: 5px;

    color: #444;

    font-size: 14px;
}

input[type="text"],
input[type="number"],
select,
textarea {

    width: 100%;

    padding: 10px;

    border:
        1px solid #ccc;

    border-radius: 6px;

    font-size: 14px;

    box-sizing: border-box;

    font-family: inherit;
}

textarea {

    resize: vertical;

    min-height: 60px;
}

select {

    background: white;
}

.row {

    display: flex;

    gap: 15px;
}

.row .form-group {

    flex: 1;
}

.logo {

    width: 100px;

    height: auto;

    margin-bottom: 10px;
}

/* =========================================================
   MATERIALES
   ========================================================= */

.material {

    border:
        2px solid #d9e2f3;

    border-radius: 10px;

    padding: 18px;

    margin-bottom: 18px;

    background: #f8f9ff;

    position: relative;
}

.material h3 {

    margin-top: 0;

    color: #1257b1;

    border-bottom:
        2px solid #d9e2f3;

    padding-bottom: 8px;
}

.btn-agregar {

    background-color: #356cc0;

    color: white;

    border: none;

    padding: 11px 18px;

    border-radius: 6px;

    font-weight: bold;

    cursor: pointer;

    margin-bottom: 20px;
}

.btn-agregar:hover {

    background-color: #2b59bb;
}

.btn-eliminar-material {

    background-color: #dc3545;

    color: white;

    border: none;

    padding: 8px 12px;

    border-radius: 6px;

    font-weight: bold;

    cursor: pointer;

    float: right;
}

.btn-eliminar-material:hover {

    background-color: #bd2130;
}

.btn-guardar {

    background-color: #28a745;

    color: white;

    padding: 12px 24px;

    border: none;

    border-radius: 6px;

    font-weight: bold;

    cursor: pointer;

    font-size: 15px;
}

.btn-guardar:hover {

    background-color: #218838;
}

.btn-volver {

    display: inline-block;

    background-color: #6c757d;

    color: white;

    padding: 10px 18px;

    text-decoration: none;

    border-radius: 6px;

    font-weight: bold;

    margin-left: 10px;

    font-size: 14px;
}

.btn-volver:hover {

    background-color: #5a6268;
}

.mensaje-exito {

    background: #d4edda;

    color: #155724;

    padding: 12px;

    border-radius: 6px;

    margin-bottom: 15px;
}

.mensaje-error {

    background: #f8d7da;

    color: #721c24;

    padding: 12px;

    border-radius: 6px;

    margin-bottom: 15px;
}

.folio-bloque {

    background: #f1f5fb;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 20px;
}

.folio-bloque strong {

    color: #1257b1;
}

@media (max-width: 700px) {

    .row {
        flex-direction: column;
        gap: 0;
    }

    .container {
        padding: 20px;
    }
}

</style>

</head>

<body>

<div class="container">

<img
    src="logo.png"
    alt="Logo"
    class="logo"
>

<h2>

Editar Solicitud<br>

<span class="folio-titulo">
<?= htmlspecialchars($folioMostrar) ?>
</span>

</h2>

<?= $mensaje ?>

<form method="POST" action="">

<!-- =====================================================
     DATOS GENERALES
===================================================== -->

<div class="folio-bloque">

    <strong>Folio:</strong>
    <?= htmlspecialchars($folioMostrar) ?>

    <input
        type="hidden"
        name="folio"
        value="<?= htmlspecialchars($folioMostrar) ?>"
    >

</div>

<div class="row">

    <div class="form-group">

        <label>
            Nombre del Solicitante
        </label>

        <input
            type="text"
            name="nombre_solicitante"
            value="<?= htmlspecialchars(
                $solicitud['nombre_solicitante'] ?? ''
            ) ?>"
            required
        >

    </div>

    <div class="form-group">

        <label>
            Cargo / Puesto
        </label>

        <input
            type="text"
            name="puesto"
            value="<?= htmlspecialchars(
                $solicitud['puesto'] ?? ''
            ) ?>"
            required
        >

    </div>

</div>

<div class="row">

    <div class="form-group">

        <label>
            Teléfono
        </label>

        <input
            type="text"
            name="telefono"
            value="<?= htmlspecialchars(
                $solicitud['telefono'] ?? ''
            ) ?>"
            required
        >

    </div>

    <div class="form-group">

        <label>
            Calle Beneficiada
        </label>

        <input
            type="text"
            name="calle_beneficiada"
            value="<?= htmlspecialchars(
                $solicitud['calle_beneficiada'] ?? ''
            ) ?>"
            required
        >

    </div>

</div>

<!-- =====================================================
     MATERIALES
===================================================== -->

<h3>
Materiales solicitados
</h3>

<div id="contenedorMateriales">

<?php foreach ($materiales as $indice => $material): ?>

<div
    class="material"
    data-indice="<?= $indice ?>"
>

    <button
        type="button"
        class="btn-eliminar-material"
        onclick="eliminarMaterial(this)"
    >
        🗑 Eliminar
    </button>

    <h3>
        Material <?= $indice + 1 ?>
    </h3>

    <input
        type="hidden"
        name="materiales[<?= $indice ?>][id_material]"
        value="<?= htmlspecialchars(
            $material['id_material'] ?? 0
        ) ?>"
    >

    <input
        type="hidden"
        name="materiales[<?= $indice ?>][eliminar]"
        value="0"
        class="campo-eliminar"
    >

    <div class="row">

        <div class="form-group">

            <label>
                Cantidad de Material
            </label>

            <input
                type="number"
                name="materiales[<?= $indice ?>][cantidad]"
                value="<?= htmlspecialchars(
                    $material['cantidad'] ?? ''
                ) ?>"
                min="1"
                required
                oninput="calcularMetros(this.closest('.material'))"
            >

        </div>

        <div class="form-group">

            <label>
                Metros
            </label>

            <input
                type="text"
                name="materiales[<?= $indice ?>][metros]"
                value="<?= number_format(
                    (float)($material['metros'] ?? 0),
                    2,
                    '.',
                    ''
                ) ?>"
                readonly
            >

        </div>

    </div>

    <div class="form-group">

        <label>
            Descripción del Material
        </label>

        <textarea
            name="materiales[<?= $indice ?>][descripcion]"
            required
        ><?= htmlspecialchars(
            $material['descripcion'] ?? ''
        ) ?></textarea>

    </div>

    <div class="form-group">

        <label>
            Categoría
        </label>

        <select
            name="materiales[<?= $indice ?>][categoria]"
            onchange="calcularMetros(this.closest('.material'))"
            required
        >

            <option
                value="agua potable"
                <?= strtolower(
                    trim($material['categoria'] ?? '')
                ) === 'agua potable'
                    ? 'selected'
                    : '' ?>
            >
                Agua Potable
            </option>

            <option
                value="drenaje"
                <?= strtolower(
                    trim($material['categoria'] ?? '')
                ) === 'drenaje'
                    ? 'selected'
                    : '' ?>
            >
                Drenaje
            </option>

        </select>

    </div>

</div>

<?php endforeach; ?>

</div>

<button
    type="button"
    class="btn-agregar"
    onclick="agregarMaterial()"
>
    ➕ Agregar Material
</button>

<!-- =====================================================
     REGIÓN Y COMUNIDAD
===================================================== -->

<div class="row">

    <div class="form-group">

        <label>
            Región
        </label>

        <input
            type="text"
            name="nombre_region"
            value="<?= htmlspecialchars(
                $solicitud['nombre_region'] ?? ''
            ) ?>"
            required
        >

    </div>

    <div class="form-group">

        <label>
            Comunidad
        </label>

        <input
            type="text"
            name="nombre_comunidad"
            value="<?= htmlspecialchars(
                $solicitud['nombre_comunidad'] ?? ''
            ) ?>"
            required
        >

    </div>

</div>

<!-- =====================================================
     BOTONES
===================================================== -->

<button
    type="submit"
    class="btn-guardar"
>
    💾 Guardar Cambios
</button>

<a
    href="ver_solicitudes.php"
    class="btn-volver"
>
    ← Cancelar y Volver
</a>

</form>

</div>

<script>

/* =========================================================
   ÍNDICE PARA NUEVOS MATERIALES
========================================================= */

let siguienteIndice =
    <?= count($materiales) ?>;


/* =========================================================
   AGREGAR MATERIAL
========================================================= */

function agregarMaterial() {

    const contenedor =
        document.getElementById(
            'contenedorMateriales'
        );

    const material =
        document.createElement('div');

    material.className = 'material';

    material.innerHTML = `

        <button
            type="button"
            class="btn-eliminar-material"
            onclick="eliminarMaterial(this)"
        >
            🗑 Eliminar
        </button>

        <h3>
            Material ${siguienteIndice + 1}
        </h3>

        <input
            type="hidden"
            name="materiales[${siguienteIndice}][id_material]"
            value="0"
        >

        <input
            type="hidden"
            name="materiales[${siguienteIndice}][eliminar]"
            value="0"
            class="campo-eliminar"
        >

        <div class="row">

            <div class="form-group">

                <label>
                    Cantidad de Material
                </label>

                <input
                    type="number"
                    name="materiales[${siguienteIndice}][cantidad]"
                    min="1"
                    required
                    oninput="calcularMetros(this.closest('.material'))"
                >

            </div>

            <div class="form-group">

                <label>
                    Metros
                </label>

                <input
                    type="text"
                    name="materiales[${siguienteIndice}][metros]"
                    readonly
                >

            </div>

        </div>

        <div class="form-group">

            <label>
                Descripción del Material
            </label>

            <textarea
                name="materiales[${siguienteIndice}][descripcion]"
                required
            ></textarea>

        </div>

        <div class="form-group">

            <label>
                Categoría
            </label>

            <select
                name="materiales[${siguienteIndice}][categoria]"
                onchange="calcularMetros(this.closest('.material'))"
                required
            >

                <option value="">
                    Selecciona una categoría
                </option>

                <option value="agua potable">
                    Agua Potable
                </option>

                <option value="drenaje">
                    Drenaje
                </option>

            </select>

        </div>
    `;

    contenedor.appendChild(material);

    siguienteIndice++;
}


/* =========================================================
   ELIMINAR MATERIAL
========================================================= */

function eliminarMaterial(boton) {

    const material =
        boton.closest('.material');

    const idMaterial =
        material.querySelector(
            'input[name*="[id_material]"]'
        ).value;

    /*
     * Si es un material que ya existe en la BD,
     * no lo quitamos solamente del HTML.
     * Marcamos que debe eliminarse.
     */

    if (idMaterial != "0") {

        const campoEliminar =
            material.querySelector(
                '.campo-eliminar'
            );

        campoEliminar.value = "1";

        material.style.display = "none";

    } else {

        /*
         * Si es un material nuevo que todavía
         * no se ha guardado, simplemente lo quitamos.
         */

        material.remove();
    }
}


/* =========================================================
   CALCULAR METROS
========================================================= */

function calcularMetros(material) {

    const cantidadInput =
        material.querySelector(
            'input[name*="[cantidad]"]'
        );

    const categoriaInput =
        material.querySelector(
            'select[name*="[categoria]"]'
        );

    const metrosInput =
        material.querySelector(
            'input[name*="[metros]"]'
        );

    const cantidad =
        parseFloat(
            cantidadInput.value
        ) || 0;

    const categoria =
        categoriaInput.value.toLowerCase();

    let metros = 0;

    if (categoria === 'agua potable') {

        metros =
            cantidad * 0.60;

    } else if (categoria === 'drenaje') {

        metros =
            cantidad * 0.90;
    }

    metrosInput.value =
        metros.toFixed(2);
}

</script>

</body>

</html>

<?php
$conexion->close();
?>