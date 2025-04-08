<?php
ob_start(); // Inicia buffer de salida para evitar errores por output antes del PDF

require('../libs/fpdf.php');
include('../BD/ConexionBD.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_nomina'])) {
    $id_nomina = $_POST['id_nomina'];

    $query = "
    SELECT 
        per.*, d.*, n.*, 
        p.rfc, p.curp, p.telefono, p.nom_persona, p.apellido_paterno, p.apellido_materno, p.modo_pago
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

    $pdf = new FPDF();
    $pdf->AddPage();

    // Encabezado
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Reporte de Nómina', 0, 1, 'C');

    // Información del empleado
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'Empleado: ' . $datos['nom_persona'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno'], 0, 1);
    $pdf->Cell(0, 10, 'RFC: ' . $datos['rfc'], 0, 1);
    $pdf->Cell(0, 10, 'CURP: ' . $datos['curp'], 0, 1);
    $pdf->Cell(0, 10, 'Telefono: ' . $datos['telefono'], 0, 1);
    $pdf->Cell(0, 10, 'Modo de Pago: ' . $datos['modo_pago'], 0, 1);

    // Fechas y días
    $pdf->Ln(2);
    $pdf->Cell(0, 10, 'Periodo de Nómina: ' . $datos['periodo_inicio'] . ' a ' . $datos['periodo_final'], 0, 1);
    $pdf->Cell(0, 10, 'Fecha de Registro: ' . $datos['fecha_nomina'], 0, 1);
    $pdf->Cell(0, 10, 'Días Trabajados: ' . $datos['dias_trabajados'], 0, 1);
    $pdf->Cell(0, 10, 'Días Justificados: ' . $datos['dias_justificados'], 0, 1);
    $pdf->Cell(0, 10, 'Días Pagados: ' . $datos['dias_total'], 0, 1);

    // Percepciones
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Percepciones', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach (['sueldo_base', 'puntualidad', 'asistencia', 'bono', 'vales_despensa', 'compensaciones', 'vacaciones', 'prima_antiguedad'] as $campo) {
        $pdf->Cell(0, 8, ucfirst(str_replace('_', ' ', $campo)) . ': $' . number_format($datos[$campo], 2), 0, 1);
    }

    // Deducciones
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Deducciones', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    foreach (['isr', 'imss', 'caja_ahorro', 'prestamos', 'infonavit', 'fonacot', 'cuota_sindical'] as $campo) {
        $pdf->Cell(0, 8, ucfirst(str_replace('_', ' ', $campo)) . ': $' . number_format($datos[$campo], 2), 0, 1);
    }

    // Resumen
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Resumen', 0, 1);
    $total_percepciones = $datos['sueldo_base'] + $datos['puntualidad'] + $datos['asistencia'] + $datos['bono'] + $datos['vales_despensa'] + $datos['compensaciones'] + $datos['vacaciones'] + $datos['prima_antiguedad'];
    $total_deducciones = $datos['isr'] + $datos['imss'] + $datos['caja_ahorro'] + $datos['prestamos'] + $datos['infonavit'] + $datos['fonacot'] + $datos['cuota_sindical'];
    $neto = $total_percepciones - $total_deducciones;

    $pdf->Cell(0, 10, 'Total Percepciones: $' . number_format($total_percepciones, 2), 0, 1);
    $pdf->Cell(0, 10, 'Total Deducciones: $' . number_format($total_deducciones, 2), 0, 1);
    $pdf->Cell(0, 10, 'Pago Neto: $' . number_format($neto, 2), 0, 1);

    ob_end_clean(); // Limpia buffer de salida
    $pdf->Output(); // Genera el PDF
}
?>
