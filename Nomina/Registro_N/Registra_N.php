<?php
include('../../BD/ConexionBD.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_persona = $_POST['id_persona'];
    $periodo_inicio = $_POST['periodo_inicio'];
    $dias_trabajados = (int)$_POST['dias_trabajados'];

    // Obtener sueldo base
    $stmt = $conn->prepare("SELECT sueldo FROM persona WHERE id_persona = ?");
    $stmt->bind_param("i", $id_persona);
    $stmt->execute();
    $stmt->bind_result($sueldo_base);
    $stmt->fetch();
    $stmt->close();

    // Calcular periodo final (14 días después del inicio)
    $periodo_final = date('Y-m-d', strtotime($periodo_inicio . ' + 14 days'));

    // Calcular días justificados en el período (usando fechas de incapacidad activas)
    $stmt = $conn->prepare("
    SELECT SUM(
        DATEDIFF(
            LEAST(fecha_final, ?) ,
            GREATEST(fecha_inicio, ?)
        ) + 1
    ) AS dias_justificados
    FROM incapacidad
    WHERE id_persona = ?
    AND estatus = 'Activo'
    AND fecha_inicio <= ?
    AND fecha_final >= ?
    ");
    $stmt->bind_param("ssiss", $periodo_final, $periodo_inicio, $id_persona, $periodo_final, $periodo_inicio);
    $stmt->execute();
    $stmt->bind_result($dias_justificados);
    $stmt->fetch();
    $stmt->close();
    $dias_justificados = $dias_justificados ?: 0;


    // Total días pagados
    $dias_pagados = $dias_trabajados + $dias_justificados;

    // Validación: no más de 15 días pagados
    if ($dias_pagados > 15) {
        echo "<script>alert('Error: La suma de días trabajados e incapacidades no puede superar 15 días.'); window.history.back();</script>";
        exit;
    }

    // Fecha actual de la nómina
    $fecha_nomina = date("Y-m-d");

    // Puntualidad = proporcional al sueldo base
    $puntualidad = ($dias_trabajados * $sueldo_base) / 15;

    // Calcular percepciones
    $asistencia = $puntualidad * 0.1;
    $bono = $puntualidad * 0.05;
    $vales = $puntualidad * 0.03;
    $compensaciones = $puntualidad * 0.07;
    $prima_antiguedad = $puntualidad * 0.02;

    $stmt = $conn->prepare("INSERT INTO percepciones (sueldo_base, puntualidad, asistencia, bono, vales_despensa, compensaciones, prima_antiguedad) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ddddddd", $sueldo_base, $puntualidad, $asistencia, $bono, $vales, $compensaciones, $prima_antiguedad);
    $stmt->execute();
    $id_percepcion = $stmt->insert_id;
    $stmt->close();

    // Calcular deducciones
    $isr = $puntualidad * 0.12;
    $imss = $puntualidad * 0.08;
    $caja = $puntualidad * 0.05;
    $prestamos = $puntualidad * 0.04;
    $infonavit = $puntualidad * 0.06;
    $fonacot = $puntualidad * 0.03;
    $sindicato = $puntualidad * 0.01;

    $stmt = $conn->prepare("INSERT INTO deducciones (isr, imss, caja_ahorro, prestamos, infonavit, fonacot, cuota_sindical) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ddddddd", $isr, $imss, $caja, $prestamos, $infonavit, $fonacot, $sindicato);
    $stmt->execute();
    $id_deduccion = $stmt->insert_id;
    $stmt->close();

    // Insertar en nómina
    $stmt = $conn->prepare("INSERT INTO nomina (id_persona, fecha_nomina, periodo_inicio, periodo_final, dias_trabajados, dias_justificados, dias_total, id_deducciones, id_percepcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssiiii", $id_persona, $fecha_nomina, $periodo_inicio, $periodo_final, $dias_trabajados, $dias_justificados, $dias_pagados, $id_deduccion, $id_percepcion);

    if ($stmt->execute()) {
        echo "<script>alert('Nómina registrada correctamente.'); window.location.href='../Nomina.php';</script>";
    } else {
        echo "Error al registrar nómina: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
