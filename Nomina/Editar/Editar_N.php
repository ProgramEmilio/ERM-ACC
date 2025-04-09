<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos desde el formulario
    $id_nomina = $_POST['id_nomina'];

    // Tabla nomina
    $periodo_inicio = $_POST['periodo_inicio'];
    $periodo_final = $_POST['periodo_final'];
    $dias_trabajados = (int) $_POST['dias_trabajados'];
    $dias_total = (float) $_POST['dias_total'];

    // Tabla percepciones
    $sueldo_base = (float) $_POST['sueldo_base'];
    $puntualidad = (float) $_POST['puntualidad'];
    $asistencia = (float) $_POST['asistencia'];
    $bono = (float) $_POST['bono'];
    $vales_despensa = (float) $_POST['vales_despensa'];
    $compensaciones = (float) $_POST['compensaciones'];
    $prima_antiguedad = (float) $_POST['prima_antiguedad'];

    // Tabla deducciones
    $isr = (float) $_POST['isr'];
    $imss = (float) $_POST['imss'];
    $caja_ahorro = (float) $_POST['caja_ahorro'];
    $prestamos = (float) $_POST['prestamos'];
    $infonavit = (float) $_POST['infonavit'];
    $fonacot = (float) $_POST['fonacot'];
    $cuota_sindical = (float) $_POST['cuota_sindical'];

    // Obtener IDs relacionados de percepcion y deduccion
    $sql_ids = "SELECT id_percepcion, id_deducciones FROM nomina WHERE id_nomina = ?";
    $stmt_ids = $conn->prepare($sql_ids);
    $stmt_ids->bind_param("i", $id_nomina);
    $stmt_ids->execute();
    $result = $stmt_ids->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "No se encontró la nómina.";
        exit;
    }

    $id_percepcion = $row['id_percepcion'];
    $id_deducciones = $row['id_deducciones'];

    // Actualizar nomina
    $sql_nomina = "UPDATE nomina SET 
        periodo_inicio = ?, 
        periodo_final = ?, 
        dias_trabajados = ?, 
        dias_total = ? 
        WHERE id_nomina = ?";
    $stmt_nomina = $conn->prepare($sql_nomina);
    $stmt_nomina->bind_param("ssidi", $periodo_inicio, $periodo_final, $dias_trabajados, $dias_total, $id_nomina);
    $stmt_nomina->execute();

    // Actualizar percepciones
    $sql_percepciones = "UPDATE percepciones SET 
        sueldo_base = ?, 
        puntualidad = ?, 
        asistencia = ?, 
        bono = ?, 
        vales_despensa = ?, 
        compensaciones = ?, 
        prima_antiguedad = ? 
        WHERE id_percepcion = ?";
    $stmt_per = $conn->prepare($sql_percepciones);
    $stmt_per->bind_param("dddddddi", $sueldo_base, $puntualidad, $asistencia, $bono, $vales_despensa, $compensaciones, $prima_antiguedad, $id_percepcion);
    $stmt_per->execute();

    // Actualizar deducciones
    $sql_deducciones = "UPDATE deducciones SET 
        isr = ?, 
        imss = ?, 
        caja_ahorro = ?, 
        prestamos = ?, 
        infonavit = ?, 
        fonacot = ?, 
        cuota_sindical = ? 
        WHERE id_deducciones = ?";
    $stmt_ded = $conn->prepare($sql_deducciones);
    $stmt_ded->bind_param("dddddddi", $isr, $imss, $caja_ahorro, $prestamos, $infonavit, $fonacot, $cuota_sindical, $id_deducciones);
    $stmt_ded->execute();

    echo "<script>alert('Nómina actualizada correctamente.'); window.location.href='../Nomina.php';</script>";
} else {
    echo "Acceso no autorizado.";
}

include('../../Nav/footer.php');
?>
