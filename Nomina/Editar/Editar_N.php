<?php
include('../../BD/ConexionBD.php');

$id_nomina = $_POST['id_nomina'];
$id_percepcion = $_POST['id_percepcion'];
$id_deducciones = $_POST['id_deducciones'];

$dias_trabajados = floatval($_POST['dias_trabajados']);
$dias_justificados = floatval($_POST['dias_justificados']);
$dias_pagados = $dias_trabajados + $dias_justificados;

// Validación: no permitir más de 15 días pagados
if ($dias_pagados > 15) {
    echo "<script>
        alert('Error: los días pagados no pueden ser mayores a 15 (actual: $dias_pagados).');
        window.history.back();
    </script>";
    exit();
}

// Actualizar percepciones
$sql1 = "UPDATE percepciones SET
    puntualidad = ?,
    asistencia = ?,
    bono = ?,
    vales_despensa = ?,
    compensaciones = ?,
    vacaciones = ?,
    prima_antiguedad = ?
    WHERE id_percepcion = ?";

$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("dddddddi", $_POST['puntualidad'], $_POST['asistencia'], $_POST['bono'], $_POST['vales_despensa'], $_POST['compensaciones'], $_POST['vacaciones'], $_POST['prima_antiguedad'], $id_percepcion);
$stmt1->execute();

// Actualizar deducciones
$sql2 = "UPDATE deducciones SET
    isr = ?,
    imss = ?,
    caja_ahorro = ?,
    prestamos = ?,
    infonavit = ?,
    fonacot = ?,
    cuota_sindical = ?
    WHERE id_deducciones = ?";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("dddddddi", $_POST['isr'], $_POST['imss'], $_POST['caja_ahorro'], $_POST['prestamos'], $_POST['infonavit'], $_POST['fonacot'], $_POST['cuota_sindical'], $id_deducciones);
$stmt2->execute();

// Actualizar nomina con nuevos campos
$sql3 = "UPDATE nomina SET
    fecha_nomina = ?,
    periodo_inicio = ?,
    periodo_final = ?,
    dias_trabajados = ?,
    dias_justificados = ?,
    dias_total = ?
    WHERE id_nomina = ?";

$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("sssdddi", $_POST['fecha_nomina'], $_POST['periodo_inicio'], $_POST['periodo_final'], $dias_trabajados, $dias_justificados, $dias_pagados, $id_nomina);
$stmt3->execute();

header("Location: ../Nomina.php");
exit();
?>
