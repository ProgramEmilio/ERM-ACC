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

    // Limpia cualquier salida previa para evitar errores
    if (ob_get_length()) {
        ob_end_clean();
    }

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Logo y título
    if (file_exists('../imagenes/acc_logo.png')) {
        $pdf->Image('../imagenes/acc_logo.png', 10, 10, 30);
    }
    $pdf->Ln(10);

    $pdf->SetXY(20, 10);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('REPORTE DE NÓMINA'), 0, 1, 'C');
    $pdf->Ln(25);

    // Datos del empleado
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, utf8_decode('Información del Empleado'), 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(95, 8, utf8_decode('Nombre: ' . $datos['nom_persona'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno']), 0, 0);
    $pdf->Cell(95, 8, 'RFC: ' . $datos['rfc'], 0, 1);
    $pdf->Cell(95, 8, 'CURP: ' . $datos['curp'], 0, 0);
    $pdf->Cell(95, 8, utf8_decode('Teléfono: ' . $datos['telefono']), 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Modo de Pago: ' . $datos['modo_pago']), 0, 1);
    $pdf->Ln(5);

    // Periodo
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, utf8_decode('Periodo de Nómina'), 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(95, 8, 'Inicio: ' . $datos['periodo_inicio'], 0, 0);
    $pdf->Cell(95, 8, 'Final: ' . $datos['periodo_final'], 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Fecha de Registro: ' . $datos['fecha_nomina']), 0, 0);
    $pdf->Cell(95, 8, utf8_decode('Días Trabajados: ' . $datos['dias_trabajados']), 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Días Justificados: ' . $datos['dias_justificados']), 0, 1);
    $pdf->Cell(95, 8, utf8_decode('Días Pagados: ' . $datos['dias_total']), 0, 1);
    $pdf->Ln(5);
    // Percepciones y deducciones
    $percepciones = ['sueldo_base', 'puntualidad', 'asistencia', 'bono', 'vales_despensa', 'compensaciones', 'prima_antiguedad'];
    $deducciones = ['isr', 'imss', 'caja_ahorro', 'prestamos', 'infonavit', 'fonacot', 'cuota_sindical'];

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(95, 8, 'Percepciones', 1, 0, 'C');
    $pdf->Cell(95, 8, 'Deducciones', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    for ($i = 0; $i < max(count($percepciones), count($deducciones)); $i++) {
        $per = $i < count($percepciones) ? utf8_decode(ucwords(str_replace('_', ' ', $percepciones[$i]))) . ': $' . number_format($datos[$percepciones[$i]], 2) : '';
        $ded = $i < count($deducciones) ? utf8_decode(ucwords(str_replace('_', ' ', $deducciones[$i]))) . ': $' . number_format($datos[$deducciones[$i]], 2) : '';
        $pdf->Cell(95, 8, $per, 1, 0);
        $pdf->Cell(95, 8, $ded, 1, 1);
    }

    // Totales
    $pdf->Ln(5);
    $total_percepciones = array_sum(array_map(fn($p) => $datos[$p], $percepciones));
    $total_deducciones = array_sum(array_map(fn($d) => $datos[$d], $deducciones));
    $total_neto = $total_percepciones - $total_deducciones;

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, utf8_decode('Resumen de Nómina'), 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(95, 8, 'Total Percepciones: $' . number_format($total_percepciones, 2), 0, 0);
    $pdf->Cell(95, 8, 'Total Deducciones: $' . number_format($total_deducciones, 2), 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Pago Neto: $' . number_format($total_neto, 2), 0, 1, 'C');

    // Nombre del archivo
    $nombre_archivo = 'Nomina_' . strtoupper($datos['rfc']) . '.pdf';
    $pdf->Output('I', $nombre_archivo);
    exit;
}
?>
