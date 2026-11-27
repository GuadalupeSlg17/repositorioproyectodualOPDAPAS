<?php
require('fpdf.php');
require('conexion.php');

$folio = $_GET['folio'] ?? '';

// Consulta de datos a la BD
$query = "SELECT s.*, COALESCE(e.estatus, s.estatus) AS estatus_real 
          FROM solicitudes s 
          LEFT JOIN estatus_solicitud e ON s.id_solicitante = e.id_solicitante 
          WHERE s.folio = '$folio'";
$resultado = mysqli_query($conexion, $query);
$row = mysqli_fetch_assoc($resultado);

if (!$row) {
    die("Solicitud no encontrada.");
}

class PDF extends FPDF {
    function Header() {
        if (file_exists('logo.png')) {
            $this->Image('logo.png', 15, 8, 38);
        }
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetXY(55, 12);
        $this->Cell(95, 4, utf8_decode('"2026. Año del Humanismo Mexicano en el Estado de México"'), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 8);
        $this->SetXY(150, 10);
        $this->Cell(45, 3.5, utf8_decode('opdapaslerma.gob.mx'), 0, 1, 'R');
        $this->SetXY(150, 14);
        $this->Cell(45, 3.5, utf8_decode('722 625 0548'), 0, 1, 'R');

        $this->SetDrawColor(180, 180, 180);
        $this->SetLineWidth(0.3);
        $this->Line(15, 25, 195, 25);
        $this->Ln(12);
    }

    function Footer() {
        $this->SetY(-18);
        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell(0, 4, utf8_decode('C.c.p. Dirección de Infraestructura Hidráulica, Departamento de Drenaje y Archivo.'), 0, 1, 'L');
        $this->SetFont('Arial', '', 7.5);
        $this->Cell(0, 4, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$pdf->AliasNbPages();
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(false);

// PÁGINA 1

$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(135, 27);
$pdf->Cell(60, 4, utf8_decode('CCC/DIH/011/2026.'), 0, 1, 'R');
$pdf->SetXY(135, 31);
$pdf->Cell(60, 4, utf8_decode('FOLIO: ' . $row['folio']), 0, 1, 'R');

$pdf->Ln(4);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, utf8_decode('CONVENIO DE COLABORACIÓN'), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 12);
$preambulo = utf8_decode('QUE CELEBRAN , POR UNA PARTE, EL ORGANISMO PÚBLICO DESCENTRALIZADO PARA LA PRESTACIÓN DE LOS SERVICIOS DE AGUA POTABLE, ALCANTARILLADO Y SANEAMIENTO DEL MUNICIPIO DE LERMA, ESTADO DE MÉXICO, REPRESENTADO EN ESTE ACTO POR EL DIRECTOR GENERAL NAYITH ARIAN ARMENTA RUIZ A QUIÉN EN LO SUCESIVO SE LE DENOMINARÁ COMO "EL ORGANISMO" Y POR OTRA PARTE AUTORIDADES AUXILIARES Y VECINOS DE LA COMUNIDAD DE ' . mb_strtoupper($row['nombre_comunidad']) . ' REPRESENTADO EN ESTE ACTO POR LOS C.C. ' . mb_strtoupper($row['nombre_solicitante']) . ' EN SU CALIDAD DE ' . mb_strtoupper($row['puesto']) . ', QUIEN EN LO SUCESIVO SE LES DENOMINARÁ "EL BENEFICIARIO", CUANDO INTERVENGAN DE MANERA CONJUNTA SE LES DENOMINARÁ "LAS PARTES" QUIENES SE OBLIGAN EN TÉRMINOS DE LO DISPUESTO EN LOS ARTÍCULOS 7.30, 7.31, 7.33, 7.34, 7.39, Y 7.43 DEL CÓDIGO CIVIL VIGENTE EN EL ESTADO DE MÉXICO, AL TENOR DE LAS DECLARACIONES Y CLÁUSULAS SIGUIENTES:');
$pdf->MultiCell(0, 4.2, $preambulo, 0, 'J');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, utf8_decode('D E C L A R A C I O N E S'), 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 4.5, utf8_decode('A. DECLARA "EL ORGANISMO"'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->MultiCell(0, 4, utf8_decode('I.   QUE EN FECHA 29 DE ABRIL DE 1992, LA "LI" LEGISLATURA DEL ESTADO DE MÉXICO EMITIÓ EL DECRETO NÚMERO 80 POR LO QUE SE CREA EL ORGANISMO PÚBLICO DESCENTRALIZADO PARA LA PRESTACIÓN DE LOS SERVICIOS DE AGUA POTABLE, ALCANTARILLADO Y SANEAMIENTO DE LERMA, ESTADO DE MÉXICO, DEBIDAMENTE PUBLICADO EN LA GACETA DE GOBIERNO NÚMERO 81.'), 0, 'J');
$pdf->Ln(1);
$pdf->MultiCell(0, 4, utf8_decode('II.  QUE DE CONFORMIDAD EN LO DISPUESTO POR LOS ARTÍCULOS 33, 34 FRACCIÓN I Y 37 DE LA LEY DEL AGUA PARA EL ESTADO DE MÉXICO Y MUNICIPIOS; "EL ORGANISMO" TIENE PERSONALIDAD JURÍDICA, PATRIMONIO PROPIO Y AUTONOMÍA TÉCNICA Y ADMINISTRATIVA PARA CONVENIR Y CONTRATAR.'), 0, 'J');
$pdf->Ln(1);
$pdf->MultiCell(0, 4, utf8_decode('III. QUE NAYITH ARIAN ARMENTA RUIZ, EN SU CARÁCTER DE DIRECTOR GENERAL DEL ORGANISMO PÚBLICO DESCENTRALIZADO PARA LA PRESTACIÓN DE LOS SERVICIOS DE AGUA POTABLE, ALCANTARILLADO Y SANEAMIENTO DE LERMA, ESTADO DE MÉXICO, CUENTA CON PERSONALIDAD Y CAPACIDAD LEGAL PARA CONVENIR Y CONTRATAR A FAVOR DEL ORGANISMO.'), 0, 'J');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 4.5, utf8_decode('B. DECLARA "EL BENEFICIARIO"'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 4, utf8_decode('I.   QUE POR SUS GENERALES MANIFIESTA SER ORIGINARIO DE ' . mb_strtoupper($row['nombre_comunidad']) . ' DEL MUNICIPIO DE LERMA, ESTADO DE MÉXICO Y SEÑALA COMO DOMICILIO PARA OÍR Y RECIBIR NOTIFICACIONES EN ' . mb_strtoupper($row['calle_beneficiada']) . ' MISMO QUE SE COTEJA CON LA IDENTIFICACIÓN OFICIAL VIGENTE (INE/IFE), ANEXO 1.'), 0, 'J');


// PÁGINA 2
$pdf->AddPage();

$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 4, utf8_decode('II.  "EL BENEFICIARIO" CUENTA CON LAS FACULTADES PARA CELEBRAR EL PRESENTE CONVENIO EN SU CALIDAD DE ' . mb_strtoupper($row['puesto']) . ' DE LA COMUNIDAD DE ' . mb_strtoupper($row['nombre_comunidad']) . ' DEL MUNICIPIO DE LERMA, ESTADO DE MÉXICO.'), 0, 'J');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 4.5, utf8_decode('C. DECLARAN "LAS PARTES"'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 4, utf8_decode('"LAS PARTES", RECONOCEN MUTUAMENTE SU PERSONALIDAD JURÍDICA Y ACEPTAN LA CAPACIDAD LEGAL CON LA QUE SE OSTENTAN, MANIFESTANDO CONOCER EL ALCANCE Y CONTENIDO DEL PRESENTE CONVENIO Y QUE, UNA VEZ HECHAS LAS DECLARACIONES ANTERIORES, MANIFIESTAN SU LIBRE VOLUNTAD PARA CELEBRAR EL PRESENTE CONVENIO, EN LOS TÉRMINOS QUE CONSIGNAN Y AL TENOR DE LAS SIGUIENTES:'), 0, 'J');
$pdf->Ln(4);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, utf8_decode('C L Á U S U L A S'), 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 4, utf8_decode('PRIMERA. EN ESTE ACTO, SIN RESERVA NI LIMITACIÓN ALGUNA, "EL BENEFICIARIO" RATIFICA SU PETICIÓN, RELATIVA AL OFICIO DE SOLICITUD CON FECHA _____ DE __________________ DEL 2026 RECIBIDO POR "EL ORGANISMO" EN FECHA _____ DE __________________ DEL 2026.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4, utf8_decode('SEGUNDA. CONVIENEN "LAS PARTES" QUE EL PRESENTE CONVENIO LO CELEBRAN CON APEGO A LO DISPUESTO EN LOS ARTÍCULOS 37, 38 Y 69 FRACCIÓN VII Y 101 FRACCIÓN I DE LA LEY DEL AGUA DEL ESTADO DE MÉXICO.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4, utf8_decode('TERCERA. EL OBJETO DEL PRESENTE CONVENIO CONSISTENTE EN: A) PLANEACIÓN, B) CONSTRUCCIÓN, C) MEJORAMIENTO, D) AMPLIACIÓN, E) REHABILITACIÓN, F) MANTENIMIENTO, G) ADMINISTRACIÓN, H) PRESTACIÓN DE LOS SERVICIOS DE AGUA POTABLE. (ESPECIFICAR)'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4, utf8_decode('CUARTA. "LAS PARTES" CONVIENEN QUE LOS MATERIALES, OBRA Y/O SERVICIOS PROPORCIONADOS POR "EL ORGANISMO", SERÁN PARA LA OBRA DESCRITA EN LA CLÁUSULA ANTERIOR Y QUE SE LLEVARÁN A ACABO EN LA SIGUIENTE UBICACIÓN: CALLE ' . mb_strtoupper($row['calle_beneficiada']) . ' DE LA COLONIA/COMUNIDAD ' . mb_strtoupper($row['nombre_comunidad']) . ' DEL MUNICIPIO DE LERMA, ESTADO DE MÉXICO, PARA LO CUAL SE ANEXA CROQUIS.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4, utf8_decode('QUINTA. "EL BENEFICIARIO" DEBERÁ COMPLEMENTAR EL TRÁMITE CON LA DOCUMENTACIÓN ENLISTADA EN EL ANEXO 2, PARA PROGRAMAR EL DÍA Y LA HORA DE ENTREGA DEL MATERIAL; PARA EL CASO DE NO HACERLO, SE CANCELARÁ AUTOMÁTICAMENTE EL MISMO, SIN NINGUNA RESPONSABILIDAD PARA "EL ORGANISMO" POR LO QUE "EL BENEFICIARIO" SE OBLIGA A GESTIONAR LA SOLICITUD NUEVAMENTE.'), 0, 'J');


// ==========================================
// PÁGINA 3
// ==========================================
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8.5);
$pdf->MultiCell(0, 4.1, utf8_decode('SEXTA. "EL BENEFICIARIO" SE ENCARGARA DEL TRASLADO DEL MATERIAL ENTREGADO, ASI COMO DE TODAS Y CADA UNA DE LAS MANIOBRAS QUE SEAN NECESARIAS DESDE EL LUGAR DE ENTREGA HASTA EL LUGAR DONDE SERÁN UTILIZADOS; EXIMIENDO AL "ORGANISMO" DE CUALQUIER RESPONSABILIDAD QUE PUDIERA SURGIR DURANTE EL TRASLADO Y LA EJECUCIÓN DE MANIOBRAS.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4.1, utf8_decode('SÉPTIMA. "EL BENEFICIARIO" SE COMPROMETE A ENTREGAR EL AGRADECIMIENTO AL "ORGANISMO" DENTRO DE LOS CINCO DÍAS HÁBILES SIGUIENTES DE HABER RECIBIDO EL MATERIAL, ASIMISMO SE COMPROMETE A ENTREGAR EL ARCHIVO FOTOGRAFICO EN FORMATO DIGITAL COMO EVIDENCIA DE QUE EL MATERIAL ENTREGADO POR "EL ORGANISMO" HA SIDO APLICADO EN EL LUGAR PARA EL QUE FUE SOLICITADO.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4.1, utf8_decode('OCTAVA. UNA VEZ ENTREGADO EL MATERIAL, "EL BENEFICIARIO" SE COMPROMETE A REALIZAR LA APLICACIÓN DE LOS MISMOS EN UN MAXIMO DE 30 DIAS NATURALES, CASO CONTRARIO, "EL ORGANISMO" PODRÁ REALIZAR EL RETIRO DE LOS MATERIALES NO UTILIZADOS, LOS CUALES DEBERÁN ESTAR EN LAS MISMAS CONDICIONES Y CARACTERISTICAS DE CUANDO LES FUE ENTREGADO.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4.1, utf8_decode('NOVENA. PARA LA SUPERVICIÓN DE LOS TRABAJOS DE COLOCACIÓN DE LOS MATERIALES, "EL ORGANISMO" SOLICITARA AL "BENEFICIARIO" LA PARTICIPACIÓN DE VECINOS PARA LA CONFORMACIÓN DEL COCICOVI, CON EL OBJETIVO ÚNICO DE OBSERVAR Y VIGILAR EL AVANCE O CONCLUSIÓN DE LA COLOCACIÓN DE LOS MATERIALES HIDRÁULICOS Y/O SANITARIOS OTORGADOS, MEDIANTE VISITAS DE INSPECCIÓN CONFORME A LO ESTABLECIDO EN EL ARTICULO 113 INCISO A, B, C Y D DE LA LEY ÓRGANICA MUNICIPAL DEL ESTADO DE MÉXICO.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4.1, utf8_decode('DÉCIMA. PARA TODOS LOS EFECTOS DEL PRESENTE CONVENIO Y PARA CUALQUIER NOTIFICACIÓN O COMUNICADO QUE LAS PARTES DEBAN DARSE CONFORME AL MISMO, DEBERÁN HACERSE POR ESCRITO.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4.1, utf8_decode('DÉCIMO PRIMERA. CON BASE EN LO ESTABLECIDO EN EL ARTÍCULO 47, 48, 49 Y 51 DE LA LEY DEL AGUA PARA EL ESTADO DE MÉXICO Y MUNICIPIOS "LAS PARTES" COADYUVARÁN EN LA REALIZACIÓN DE LAS OBRAS, "EL ORGANISMO" PROPORCIONARÁ EL MATERIAL SOLICITADO EN OFICIO DE SOLICITUD Y "EL BENEFICIARIO" REALIZARÁ ACCIONES RELACIONADAS CON LA MANO DE OBRA, EN RAZÓN DE QUE LOS BIENES INHERENTES SERÁN UTILIZADOS Y APROVECHADOS POR LOS PARTICULARES, NO OBSTANTE ESTÁS SERÁN PROPIEDAD DE "EL ORGANISMO" Y QUEDARÁN BAJO LA DISPOSICIÓN, SUPERVISIÓN Y CONTROL DEL MISMO.'), 0, 'J');
$pdf->Ln(2);

$pdf->MultiCell(0, 4.1, utf8_decode('DÉCIMO SEGUNDA. "EL BENEFICIARIO" SERÁ RESPONSABLE DE LOS DAÑOS OCASIONADOS A LA INFRAESTRUCTURA HIDRÁULICA DE "EL ORGANISMO" POR PERMISOS OTORGADOS DE CONEXIÓN A LA RED DE AGUA POTABLE, DRENAJE Y/O ALCANTARILLADO; SIENDO ESTE EL MISMO QUE SE COMPROMETE A REPARAR EL DAÑO CONFORME A LO ESTABLECIDO EN EL ARTICULO 70 DE LA LEY FEDERAL DE PROCEDIMIENTOS ADMINISTRATIVOS Y DEMÁS DISPOSICIONES LEGALES Y REGLAMENTARIAS APLICABLES.'), 0, 'J');


// ==========================================
// PÁGINA 4: Cierre con Amplio Espacio y Separación Perfecta
// ==========================================
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8.5);
$pdf->MultiCell(0, 4.2, utf8_decode('UNA VEZ LEÍDAS Y ENTERADAS DEL VALOR Y ALCANCE DE TODAS Y CADA UNA DE LAS CLÁUSULAS ANTERIORES TENIENDO PLENO CONOCIMIENTO DE LAS OBLIGACIONES A SU CARGO QUE EN EL MISMO SE CITAN, POR SER EL ACUERDO TOTAL Y DEFINITIVO ENTRE LAS PARTES, FIRMAN AL MARGEN Y AL CALCE Y POR DUPLICADO TODOS Y CADA UNO DE LOS QUE INTERVIENEN EN EL PRESENTE CONVENIO, EN EL MUNICIPIO DE LERMA, ESTADO DE MÉXICO, EL DÍA ______ DEL MES DE ___________________ DEL AÑO 2026.'), 0, 'J');
$pdf->Ln(10);

// 1. ORGANISMO
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, utf8_decode('POR "EL ORGANISMO"'), 0, 1, 'C');
$pdf->Ln(18); // Amplio espacio vertical para firma
$pdf->SetX(62.5);
$pdf->Cell(90, 0, '', 'T', 1);
$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, utf8_decode('NAYITH ARIAN ARMENTA RUIZ'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 7.5);
$pdf->Cell(0, 4, utf8_decode('DIRECTOR GENERAL'), 0, 1, 'C');

$pdf->Ln(8);

// 2. BENEFICIARIO
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, utf8_decode('POR "EL BENEFICIARIO"'), 0, 1, 'C');
$pdf->Ln(18); // Amplio espacio vertical para firma
$pdf->SetX(62.5);
$pdf->Cell(90, 0, '', 'T', 1);
$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, utf8_decode('NOMBRE Y FIRMA'), 0, 1, 'C');

$pdf->Ln(8);

// 3. TÍTULO TESTIGOS
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(0, 4, utf8_decode('TESTIGOS'), 0, 1, 'C');
$pdf->Ln(16);

// 4. TESTIGOS PARALELOS
$pdf->SetX(20);
$pdf->Cell(75, 0, '', 'T', 0);
$pdf->Cell(25, 0, '', 0, 0);
$pdf->Cell(75, 0, '', 'T', 1);
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 7.5);
$pdf->SetX(20);
$pdf->Cell(75, 4, utf8_decode('JEFE DEL DEPARTAMENTO'), 0, 0, 'C');
$pdf->Cell(25, 4, '', 0, 0);
$pdf->Cell(75, 4, utf8_decode('NOMBRE Y FIRMA'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 7);
$pdf->SetX(20);
$pdf->Cell(75, 3.5, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(25, 3.5, '', 0, 0);
$pdf->Cell(75, 3.5, utf8_decode('(COMUNIDAD)'), 0, 1, 'C');

$pdf->Ln(14);

// 5. DIRECCIÓN DE INFRAESTRUCTURA HIDRÁULICA
$pdf->SetX(20);
$pdf->Cell(75, 0, '', 'T', 1);
$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 7.5);
$pdf->SetX(20);
$pdf->Cell(75, 4, utf8_decode('DIRECCIÓN DE INFRAESTRUCTURA HIDRÁULICA'), 0, 1, 'C');

$pdf->Output('I', 'Convenio_' . $folio . '.pdf');
?>