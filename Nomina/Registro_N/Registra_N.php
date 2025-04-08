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

    // Calcular días justificados (sumar incapacidades activas en el período)
    $stmt = $conn->prepare("SELECT SUM(total_dias) FROM incapacidad WHERE id_persona = ? AND estatus = 'Activo'");
    $stmt->bind_param("i", $id_persona);
    $stmt->execute();
    $stmt->bind_result($dias_justificados);
    $stmt->fetch();
    $stmt->close();
    $dias_justificados = $dias_justificados ?: 0;

    // Total días pagados
    $dias_pagados = $dias_trabajados + $dias_justificados;

    // ⚠️ Validación: días pagados no deben superar 15
    if ($dias_pagados > 15) {
        echo "<script>alert('Error: La suma de días trabajados e incapacidades no puede superar 15 días.'); window.history.back();</script>";
        exit;
    }

    // Fecha actual y final
    $fecha_nomina = date("Y-m-d");
    $periodo_final = date('Y-m-d', strtotime($periodo_inicio . ' + 14 days'));

    // Puntualidad = (dias_trabajados * sueldo_base) / 15
    $puntualidad = ($dias_trabajados * $sueldo_base) / 15;

    // Calcular percepciones
    $asistencia = $puntualidad * 0.1;
    $bono = $puntualidad * 0.05;
    $vales = $puntualidad * 0.03;
    $compensaciones = $puntualidad * 0.07;
    $vacaciones = $puntualidad * 0.06;
    $prima_antiguedad = $puntualidad * 0.02;

    $stmt = $conn->prepare("INSERT INTO percepciones (sueldo_base, puntualidad, asistencia, bono, vales_despensa, compensaciones, vacaciones, prima_antiguedad) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("dddddddd", $sueldo_base, $puntualidad, $asistencia, $bono, $vales, $compensaciones, $vacaciones, $prima_antiguedad);
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
