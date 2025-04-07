<?php
include('../../BD/ConexionBD.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_persona = $_POST['id_persona'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_final = $_POST['fecha_final'];
    $motivo = $_POST['motivo'];

    // Validación básica de fechas
    if (strtotime($fecha_final) < strtotime($fecha_inicio)) {
        echo "Error: La fecha final no puede ser menor que la fecha inicial.";
        exit;
    }

    // Cálculo de días
    $fecha1 = new DateTime($fecha_inicio);
    $fecha2 = new DateTime($fecha_final);
    $diferencia = $fecha1->diff($fecha2);
    $total_dias = $diferencia->days + 1;

    // Insertar en la tabla incapacidad
    $stmt = $conn->prepare("INSERT INTO incapacidad (id_persona, fecha_inicio, fecha_final, total_dias, motivo, estatus) VALUES (?, ?, ?, ?, ?, 'Activo')");
    $stmt->bind_param("issis", $id_persona, $fecha_inicio, $fecha_final, $total_dias, $motivo);

    if ($stmt->execute()) {
        echo "<script>alert('Incapacidad registrada correctamente.'); window.location.href='../Usuario.php';</script>";
    } else {
        echo "Error al registrar la incapacidad: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Acceso denegado.";
}
?>
