<?php
require('fpdf.php');

// Función para formatear nombres a estilo Título (ej. "SAN NICOLÁS" -> "San Nicolás")
function formatoTitulo($texto) {
    if (empty($texto)) return '';
    return mb_convert_case(mb_strtolower(trim($texto), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}
// Función inteligente para formatear la ubicación
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

// Función para obtener la fecha en español
function fechaEnEspanol($fecha) {
    $dias = array("domingo","lunes","martes","miércoles","jueves","viernes","sábado");
    $meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    
    $timestamp = strtotime($fecha);
    $dia_num = date('d', $timestamp);
    $mes_num = date('n', $timestamp) - 1;
    $anio = date('Y', $timestamp);
    
    return $dia_num . " de " . $meses[$mes_num] . " de " . $anio;
}

class PDF extends FPDF {
    // Cabecera de página institucional
    function Header() {
        if (file_exists('logo.png')) {
            $this->Image('logo.png', 12, 10, 35);
        }
        
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 5, utf8_decode('"2026. Año del Humanismo Mexicano en el Estado de México"'), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, utf8_decode('opdapaslerma.gob.mx'), 0, 1, 'R');
        $this->Cell(0, 4, utf8_decode('722 719 8775'), 0, 1, 'R');
        
        $this->Ln(8);
    }

    // Pie de página con C.c.p. institucional
    function Footer() {
        $this->SetY(-35);
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(0, 4, utf8_decode('C.c.p. Arq. Carlos Alberto Nieves Godinez.- Director de Infraestructura Hidráulica.- seguimiento'), 0, 1, 'L');
        $this->Cell(0, 4, utf8_decode('      Ing. Silvestre Hernández Martínez.- Jefe del Depto. De Drenaje de OPDAPAS Lerma.- seguimiento'), 0, 1, 'L');
        $this->Cell(0, 4, utf8_decode('      Archivo'), 0, 1, 'L');
        
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }
}

// Obtener parámetros por URL
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';
if (empty($folio)) {
    die("Folio no especificado.");
}

// Conexión a la base de datos
include("conexion.php");

// 1. Obtener datos generales de la solicitud
$query_solicitud = "SELECT * FROM solicitudes WHERE folio = '$folio'";
$res_solicitud = mysqli_query($conexion, $query_solicitud);
$data = mysqli_fetch_assoc($res_solicitud);

if (!$data) {
    die("Solicitud no encontrada.");
}

// Variables directas formateadas
$comunidad = $data['nombre_comunidad'];
$tipo_asentamiento = isset($data['tipo_asentamiento']) ? $data['tipo_asentamiento'] : '';
$calle = formatoTitulo($data['calle_beneficiada']);

// 2. Obtener materiales solicitados
$query_materiales = "SELECT cantidad, descripcion FROM material_solicitado WHERE folio = '$folio'";
$res_materiales = mysqli_query($conexion, $query_materiales);

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// --- FORMATO DE OFICIO LIBERADO ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode('Dirección General'), 0, 1, 'R');
$pdf->Cell(0, 5, utf8_decode($data['folio']), 0, 1, 'R');
$pdf->SetFont('Arial', '', 10);

// Fecha formateada en español
$fecha_formateada = fechaEnEspanol($data['fecha_solicitud']);
$pdf->Cell(0, 5, utf8_decode('Lerma de Villada, México a ' . $fecha_formateada), 0, 1, 'R');
$pdf->Ln(8);

// Destinatario
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->Cell(0, 4, utf8_decode('C. CARLOS HERNÁNDEZ RODRÍGUEZ'), 0, 1, 'L');
$pdf->MultiCell(0, 4.5, utf8_decode("JEFE DEL DEPARTAMENTO DE RECURSOS MATERIALES\nY SERVICIOS DEL OPDAPAS DEL MUNICIPIO DE LERMA\nP R E S E N T E"));
$pdf->Ln(8);

// Párrafo principal dinámico con la función formatearUbicacion()
$ubicacion_formateada = formatearUbicacion($comunidad, $tipo_asentamiento);
$texto_oficio = "Sirva el presente medio para hacerle llegar cordial saludo, al mismo tiempo que de derivado de la solicitud número " . $data['folio'] . " ingresada por las autoridades auxiliares " . $ubicacion_formateada . "; se le informa que el próximo martes se estará haciendo entrega de materiales a las diferentes regiones del municipio; para lo que solicito de su apreciable apoyo para que se lleve a cabo la entrega de:";

$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5.5, utf8_decode($texto_oficio));
$pdf->Ln(5);

// Listar materiales
$romanos = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
$index = 0;
$materiales_encontrados = false;

if ($res_materiales && mysqli_num_rows($res_materiales) > 0) {
    while ($mat = mysqli_fetch_assoc($res_materiales)) {
        $num_romano = $romanos[$index] ?? ($index + 1);
        $pdf->Cell(15);
        $pdf->Cell(10, 6, utf8_decode($num_romano . '.'), 0, 0, 'L');
        $pdf->Cell(0, 6, utf8_decode($mat['cantidad'] . " piezas de " . $mat['descripcion']), 0, 1, 'L');
        $index++;
        $materiales_encontrados = true;
    }
}

if (!$materiales_encontrados && !empty($data['cantidad_mat1'])) {
    $pdf->Cell(15);
    $pdf->Cell(10, 6, utf8_decode('I.'), 0, 0, 'L');
    $pdf->Cell(0, 6, utf8_decode($data['cantidad_mat1'] . " piezas de " . $data['descripcion_mat1']), 0, 1, 'L');
}

$pdf->Ln(5);

// Párrafo de cierre
$texto_cierre = "Material que será instalado en la Calle " . $calle . " de la misma comunidad. Sin más por el momento agradezco la atención que sirva darle al presente, quedando de usted para cualquier duda o aclaración.";
$pdf->MultiCell(0, 5.5, utf8_decode($texto_cierre));
$pdf->Ln(15);

// Firma y Atentamente
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Atentamente'), 0, 1, 'L');
$pdf->Ln(12);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 4, utf8_decode('Nayith Arian Armenta Ruiz'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 4, utf8_decode('Director General del OPDAPAS'), 0, 1, 'L');
$pdf->Cell(0, 4, utf8_decode('del municipio de Lerma'), 0, 1, 'L');

// Salida del PDF al navegador
$pdf->Output('I', 'Oficio_' . $folio . '.pdf');
?>