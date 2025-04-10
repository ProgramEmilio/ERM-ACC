<?php
require('../../libs/fpdf.php');
include('../../BD/ConexionBD.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_nomina'])) {
    $id_nomina = $_POST['id_nomina'];

    $query = "
        SELECT 
            per.*, d.*, n.*, 
            p.rfc, p.curp, p.telefono, p.nom_persona, p.apellido_paterno, p.apellido_materno, p.modo_pago,
            n.dias_trabajados, n.dias_justificados , n.dias_total
        FROM nomina n
        JOIN persona p ON n.id_persona = p.id_persona
        JOIN percepciones per ON n.id_percepcion = per.id_percepcion
        JOIN deducciones d ON n.id_deducciones = d.id_deducciones
        WHERE n.id_nomina = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_nomina);
    $stmt->execute();
    $result = $stmt->get_result();
    $datos = $result->fetch_assoc();

    if (ob_get_length()) ob_end_clean();

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    // Colores personalizados
    $colorPrimario = [52, 152, 219]; // Azul
    $colorSecundario = [236, 240, 241]; // Gris claro
    $colorTitulo = [44, 62, 80]; // Azul oscuro
    $colorTexto = [0, 0, 0];

    // Estilo general
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(...$colorTexto);

    // Logo y título
    if (file_exists('../../imagenes/acc_logo.png')) {
        $pdf->Image('../../imagenes/acc_logo.png', 10, 10, 30);
    }

    $pdf->SetXY(20, 20);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetTextColor(...$colorTitulo);
    $pdf->Cell(0, 10, utf8_decode('REPORTE DE NÓMINA'), 0, 1, 'C');
    $pdf->Ln(15);

    // Sección: Información del Empleado
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, utf8_decode('Información del Empleado'), 0, 1, 'L', true);

    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(...$colorTexto);
    $pdf->Cell(95, 8, utf8_decode('Nombre: ' . $datos['nom_persona'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno']), 0, 0);
    $pdf->Cell(95, 8, 'RFC: ' . $datos['rfc'], 0, 1);
    $pdf->Cell(95, 8, 'CURP: ' . $datos['curp'], 0, 0);
    $pdf->Cell(95, 8, utf8_decode('Teléfono: ' . $datos['telefono']), 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Modo de Pago: ' . $datos['modo_pago']), 0, 1);
    $pdf->Ln(2);

    // Sección: Periodo de Nómina
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 8, utf8_decode('Periodo de Nómina'), 0, 1, 'L', true);

    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(...$colorTexto);
    $pdf->Cell(95, 8, 'Inicio: ' . $datos['periodo_inicio'], 0, 0);
    $pdf->Cell(95, 8, 'Final: ' . $datos['periodo_final'], 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Fecha de Registro: ' . $datos['fecha_nomina']), 0, 0);
    $pdf->Cell(95, 8, utf8_decode('Días Trabajados: ' . $datos['dias_trabajados']), 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Días Justificados: ' . $datos['dias_justificados']), 0, 1);
    $pdf->Ln(2);

    // Percepciones y Deducciones
    $percepciones = ['sueldo_base', 'puntualidad', 'asistencia', 'bono', 'vales_despensa', 'compensaciones', 'prima_antiguedad'];
    $deducciones = ['isr', 'imss', 'caja_ahorro', 'prestamos', 'infonavit', 'fonacot', 'cuota_sindical'];

    // Porcentajes para percepciones
    $porcentajes_percepciones = [
        'sueldo_base' => '100%',
        'puntualidad' => '5%',
        'asistencia' => '3%',
        'bono' => '10%',
        'vales_despensa' => '5%',
        'compensaciones' => '7%',
        'prima_antiguedad' => '2%',
    ];

    // Porcentajes para deducciones
    $porcentajes_deducciones = [
        'isr' => '5%',
        'imss' => '8%',
        'caja_ahorro' => '3%',
        'prestamos' => '2%',
        'infonavit' => '5%',
        'fonacot' => '1%',
        'cuota_sindical' => '1%',
    ];

    // Calcular percepciones (excluyendo sueldo_base para sumarla explícitamente luego)
    $sueldo_base = $datos['sueldo_base'];
    $otras_percepciones = array_filter($percepciones, fn($p) => $p !== 'sueldo_base');
    $total_otras_percepciones = array_sum(array_map(fn($p) => $datos[$p], $otras_percepciones));

    // Total de percepciones (sueldo_base + otras)
    $total_percepciones = $sueldo_base + $total_otras_percepciones;

    // Mostrar tabla de percepciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->Cell(0, 8, 'Percepciones', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);
    foreach ($percepciones as $p) {
        $nombre = utf8_decode(ucwords(str_replace('_', ' ', $p)));
        $monto = number_format($datos[$p], 2);
        $porcentaje = isset($porcentajes_percepciones[$p]) ? ' (' . $porcentajes_percepciones[$p] . ')' : '';
        $pdf->Cell(150, 8, $nombre . $porcentaje, 1);
        $pdf->Cell(40, 8, '$' . $monto, 1, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(230, 230, 230); // gris claro
    $pdf->Cell(150, 8, 'Total Percepciones', 1, 0, 'L', true);
    $pdf->Cell(40, 8, '$' . number_format($total_percepciones, 2), 1, 1, 'R', true);

    // Espacio entre tablas
    $pdf->Ln(2);

    // Deducciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->Cell(0, 8, 'Deducciones', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);
    foreach ($deducciones as $d) {
        $nombre = utf8_decode(ucwords(str_replace('_', ' ', $d)));
        $monto = number_format($datos[$d], 2);
        $porcentaje = isset($porcentajes_deducciones[$d]) ? ' (' . $porcentajes_deducciones[$d] . ')' : '';
        $pdf->Cell(150, 8, $nombre . $porcentaje, 1);
        $pdf->Cell(40, 8, '$' . $monto, 1, 1, 'R');
    }

    $total_deducciones = array_sum(array_map(fn($d) => $datos[$d], $deducciones));
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(230, 230, 230); // gris claro
    $pdf->Cell(150, 8, 'Total Deducciones', 1, 0, 'L', true);
    $pdf->Cell(40, 8, '$' . number_format($total_deducciones, 2), 1, 1, 'R', true);

    // Espacio antes del total neto
    $pdf->Ln(2);

    // Pago neto = Sueldo base + otras percepciones - deducciones
    $total_neto = $sueldo_base + $total_otras_percepciones - $total_deducciones;
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->Cell(190, 12, 'Pago Neto: $' . number_format($total_neto, 2), 1, 1, 'C', true);

    // Sección de totales
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(192, 192, 192); // Gris claro para la tabla de totales
    $pdf->Cell(95, 8, 'Total Sueldo + Percepciones', 1, 0, 'L', true);
    $pdf->Cell(40, 8, '$' . number_format($total_percepciones, 2), 1, 1, 'R');

    $pdf->Cell(95, 8, 'Total Deducciones', 1, 0, 'L', true);
    $pdf->Cell(40, 8, '$' . number_format($total_deducciones, 2), 1, 1, 'R');

    $pdf->Cell(95, 8, 'Pago Neto Final', 1, 0, 'L', true);
    $pdf->Cell(40, 8, '$' . number_format($total_neto, 2), 1, 1, 'R');

    $nombre_archivo = 'Nomina_' . strtoupper($datos['rfc']) . '.pdf';
    $pdf->Output('I', $nombre_archivo);
    exit;
}
?>
