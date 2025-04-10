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
    $pdf->SetFont('Arial', 'B', 16);
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
    $pdf->Ln(1);

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
    $pdf->Cell(95, 8, utf8_decode('Días Pagados: ' . $datos['dias_total']), 0, 1);
    $pdf->Ln(3);

    // Percepciones y Deducciones
    $puntualidad = $datos['puntualidad'];  // Asumo que este campo está en la base de datos, o puedes usar un valor fijo
    $sueldo_base = $datos['sueldo_base'];

    // Percepciones
    $asistencia = $puntualidad * 0.10;
    $bono = $puntualidad * 0.05;
    $vales = $puntualidad * 0.03;
    $compensaciones = $puntualidad * 0.07;
    $prima_antiguedad = $puntualidad * 0.02;

    $total_percepciones = $sueldo_base + $asistencia + $bono + $vales + $compensaciones + $prima_antiguedad;

    // Mostrar percepciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->Cell(160, 8, 'Percepciones', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(130, 8, 'Sueldo Base (100%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($sueldo_base, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Asistencia (10%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($asistencia, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Bono (5%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($bono, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Vales (3%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($vales, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Compensaciones (7%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($compensaciones, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Prima Antiguedad (2%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($prima_antiguedad, 2), 1, 1, 'R');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(230, 230, 230); // gris claro
    $pdf->Cell(130, 8, 'Total Percepciones', 1, 0, 'L', true);
    $pdf->Cell(30, 8, '$' . number_format($total_percepciones, 2), 1, 1, 'R', true);

    $pdf->Ln(4);

    // Deducciones
    $isr = $puntualidad * 0.12;
    $imss = $puntualidad * 0.08;
    $caja = $puntualidad * 0.05;
    $prestamos = $puntualidad * 0.04;
    $infonavit = $puntualidad * 0.06;
    $fonacot = $puntualidad * 0.03;
    $sindicato = $puntualidad * 0.01;

    $total_deducciones = $isr + $imss + $caja + $prestamos + $infonavit + $fonacot + $sindicato;

    // Mostrar deducciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(...$colorPrimario);
    $pdf->Cell(160, 8, 'Deducciones', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(130, 8, 'ISR (12%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($isr, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'IMSS (8%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($imss, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Caja (5%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($caja, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Prestamos (4%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($prestamos, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Infonavit (6%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($infonavit, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Fonacot (3%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($fonacot, 2), 1, 1, 'R');

    $pdf->Cell(130, 8, 'Sindicato (1%)', 1);
    $pdf->Cell(30, 8, '$' . number_format($sindicato, 2), 1, 1, 'R');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(230, 230, 230); // gris claro
    $pdf->Cell(130, 8, 'Total Deducciones', 1, 0, 'L', true);
    $pdf->Cell(30, 8, '$' . number_format($total_deducciones, 2), 1, 1, 'R', true);

    $pdf->Ln(5);

    // Calcular salario neto
    $salario_neto = $total_percepciones - $total_deducciones;

    // Mostrar salario neto
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(200, 255, 200); // verde claro
    $pdf->Cell(130, 8, 'Salario Neto', 1, 0, 'L', true);
    $pdf->Cell(30, 8, '$' . number_format($salario_neto, 2), 1, 1, 'R', true);

    // Salida PDF
    $pdf->Output('I', 'Reporte_Nomina_' . $id_nomina . '.pdf');
}
?>
