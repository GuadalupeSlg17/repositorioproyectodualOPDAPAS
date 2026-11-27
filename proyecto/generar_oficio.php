<?php

require 'conexion.php';
require 'fpdf.php';

/**
 * Convierte un texto a Formato Título (Primera Letra De Cada Palabra En Mayúscula),
 * respetando acentos y caracteres especiales en UTF-8.
 */
function formatoTitulo($texto) {
    if (empty($texto)) return '';
    return mb_convert_case(mb_strtolower(trim($texto), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}

/**
 * Formatea la comunidad/ubicación agregando inteligentemente 'de', 'de la', 'del', etc.
 */
function formatearUbicacion($comunidad, $tipoAsentamiento = '') {
    $comunidad = trim($comunidad);
    $tipo = strtolower(trim($tipoAsentamiento));

    // 1. Si la BD especifica el tipo de asentamiento explícitamente
    if ($tipo === 'colonia') {
        return "de la Colonia " . formatoTitulo($comunidad);
    } elseif ($tipo === 'pueblo' || $tipo === 'barrio' || $tipo === 'ejido') {
        return "del " . formatoTitulo($tipo) . " " . formatoTitulo($comunidad);
    }

    // 2. Si el propio nombre de la comunidad ya empieza con el tipo
    if (preg_match('/^(colonia|col\.)/i', $comunidad)) {
        return "de la " . formatoTitulo($comunidad);
    } elseif (preg_match('/^(barrio|pueblo|ejido|ranchería|fraccionamiento)/i', $comunidad)) {
        return "del " . formatoTitulo($comunidad);
    }

    // 3. Si empieza por "San", "Santa", "Santo" o nombres propios directos (Ej. San Pedro)
    if (preg_match('/^(san|santa|santo)\b/i', $comunidad)) {
        return "de " . formatoTitulo($comunidad);
    }

    // 4. Por defecto para nombres sin tipo explícito
    return "de " . formatoTitulo($comunidad);
}

// OBTENCIÓN DEL FOLIO Y CONSULTAS BD 
$folio = $_GET['folio'] ?? '';

if (empty($folio)) {
    die("Folio no especificado.");
}

// Consulta 1: Información general de la solicitud
$stmt = $conexion->prepare("SELECT * FROM solicitudes WHERE folio = ?");
$stmt->bind_param("s", $folio);
$stmt->execute();
$solicitud = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$solicitud) {
    die("Solicitud no encontrada.");
}

// Consulta 2: Materiales asociados al folio
$stmtMat = $conexion->prepare("SELECT * FROM material_solicitado WHERE folio = ?");
$stmtMat->bind_param("s", $folio);
$stmtMat->execute();
$materiales = $stmtMat->get_result();


$nombreSolicitante = formatoTitulo($solicitud['nombre_solicitante']);
$puestoSolicitante = formatoTitulo($solicitud['puesto']);
$calleBeneficiada  = formatoTitulo($solicitud['calle_beneficiada']);
$nombreComunidad   = formatoTitulo($solicitud['nombre_comunidad']);

// Generar el texto dinámico de la ubicación (Ej: "de la Colonia Adolfo López", "de San Pedro", etc.)
$ubicacionComunidad = formatearUbicacion($solicitud['nombre_comunidad'], $solicitud['tipo_asentamiento'] ?? '');


class PDF_Oficio extends FPDF {
    function Header() {
        if ($this->PageNo() == 1) {
            if (file_exists('logo.png')) {
                $this->Image('logo.png', 145, 10, 50);
            }
            $this->Ln(28);
        }
    }
}

$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];

$pdf = new PDF_Oficio('P', 'mm', 'Letter');
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();
$pdf->SetMargins(20, 15, 20);

// ENCABEZADO Y FECHA 

if (!empty($solicitud['fecha_solicitud'])) {
    $time = strtotime($solicitud['fecha_solicitud']);
    $dia = date('d', $time);
    $mesNum = (int)date('m', $time);
    $anio = date('Y', $time);
    $fechaTexto = $dia . " de " . ($meses[$mesNum] ?? '') . " de " . $anio;
} else {
    $fechaTexto = date('d') . " de " . ($meses[(int)date('m')] ?? '') . " de " . date('Y');
}

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, utf8_decode($nombreComunidad . ", Lerma, Mex; a " . $fechaTexto), 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode("Asunto: Solicitud de apoyo con material hidráulico"), 0, 1, 'R');
$pdf->Ln(10);

//  DESTINATARIO 

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, utf8_decode("MTRO. NAYITH ARIAN ARMENTA RUIZ"), 0, 1, 'L');
$pdf->Ln(2);

$pdf->MultiCell(0, 5, utf8_decode("DIRECTOR GENERAL DEL ORGANISMO PÚBLICO DESCENTRALIZADO DE AGUA POTABLE, ALCANTARILLADO Y SANEAMIENTO DE LERMA (OPDAPAS)"), 0, 'L');
$pdf->Ln(2);

$pdf->Cell(0, 5, utf8_decode("P R E S E N T E."), 0, 1, 'L');
$pdf->Ln(8);

// CUERPO DEL OFICIO 

$pdf->SetFont('Arial', '', 11);

$textoIntro = "Por medio del presente, reciba un cordial saludo. A nombre de la Delegación " . $ubicacionComunidad . ", nos dirigimos a usted de la manera más respetuosa para solicitar su valioso apoyo para la gestión de material hidráulico necesario para la conclusión de un proyecto que actualmente se ejecuta en nuestra comunidad.";
$pdf->MultiCell(0, 6, utf8_decode($textoIntro), 0, 'J');
$pdf->Ln(5);

$pdf->MultiCell(0, 6, utf8_decode("En específico, se solicita atentamente la gestión de los siguientes materiales:"), 0, 'J');
$pdf->Ln(4);

//  LISTA DE MATERIALES 

$pdf->SetFont('Arial', '', 11);

if ($materiales && $materiales->num_rows > 0) {
    while ($m = $materiales->fetch_assoc()) {
        $mtsFormateados = number_format((float)$m['metros'], 1, '.', '');
        $descripcionMat = formatoTitulo($m['descripcion']);
        
        $pdf->Cell(10, 6, '', 0, 0);
        $pdf->Cell(5, 6, iconv('UTF-8', 'windows-1252', '•'), 0, 0, 'L');
        $textoMat = $m['cantidad'] . " " . $descripcionMat . " (" . $mtsFormateados . " metros lineales)";
        $pdf->Cell(0, 6, utf8_decode($textoMat), 0, 1, 'L');
    }
} else {
    $mtsFormateados = number_format((float)($solicitud['metros_mat1'] ?? 0), 1, '.', '');
    $descripcionMat = formatoTitulo($solicitud['descripcion_mat1'] ?? '');
    
    $pdf->Cell(10, 6, '', 0, 0);
    $pdf->Cell(5, 6, iconv('UTF-8', 'windows-1252', '•'), 0, 0, 'L');
    $textoMat = ($solicitud['cantidad_mat1'] ?? '') . " " . $descripcionMat . " (" . $mtsFormateados . " metros lineales)";
    $pdf->Cell(0, 6, utf8_decode($textoMat), 0, 1, 'L');
}
$pdf->Ln(6);

// ========== PÁRRAFOS DE CIERRE Y JUSTIFICACIÓN ==========

$parrafo1 = "Dichos insumos serán empleados para finalizar correctamente el proyecto mencionado, el cual tiene como objetivo principal beneficiar a los habitantes de la Calle " . $calleBeneficiada . " " . $ubicacionComunidad . ", mejorando de manera significativa el servicio y la infraestructura hidráulica de la comunidad.";
$pdf->MultiCell(0, 6, utf8_decode($parrafo1), 0, 'J');
$pdf->Ln(4);

$parrafo2 = "Confiamos en su sensibilidad y compromiso con el bienestar de la ciudadanía, por lo que reiteramos nuestra disposición para colaborar y proporcionar cualquier información adicional que sea necesaria para dar seguimiento a esta solicitud.";
$pdf->MultiCell(0, 6, utf8_decode($parrafo2), 0, 'J');
$pdf->Ln(4);

$parrafo3 = "Sin otro particular, agradecemos de antemano la atención brindada y quedamos atentos a su amable respuesta.";
$pdf->MultiCell(0, 6, utf8_decode($parrafo3), 0, 'J');
$pdf->Ln(10);

// ========== SECCIÓN DE FIRMA Y SOLICITANTE ==========

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, utf8_decode("A T E N T A M E N T E"), 0, 1, 'C');
$pdf->Ln(18);

$xCentro = ($pdf->GetPageWidth() - 80) / 2;
$yLinea = $pdf->GetY();
$pdf->Line($xCentro, $yLinea, $xCentro + 80, $yLinea);
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode("C. " . mb_strtoupper($solicitud['nombre_solicitante'], 'UTF-8')), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode($puestoSolicitante), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("Cel. " . $solicitud['telefono']), 0, 1, 'C');

// ========== SALIDA DEL PDF ==========

$pdf->Output('I', 'Oficio_' . $solicitud['folio'] . '.pdf');
?>