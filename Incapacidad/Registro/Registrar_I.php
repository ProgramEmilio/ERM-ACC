<?php
include('../../BD/ConexionBD.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_persona = $_POST['id_persona'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_final = $_POST['fecha_final'];
    $motivo = $_POST['motivo'];

    if (strtotime($fecha_final) < strtotime($fecha_inicio)) {
        echo "Error: La fecha final no puede ser menor que la fecha inicial.";
        exit;
    }

    $fecha1 = new DateTime($fecha_inicio);
    $fecha2 = new DateTime($fecha_final);
    $total_dias = $fecha1->diff($fecha2)->days + 1;

    if ($total_dias > 86) {
        echo "Error: El máximo de días permitidos por incapacidad es 86.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO incapacidad (id_persona, fecha_inicio, fecha_final, total_dias, motivo, estatus) VALUES (?, ?, ?, ?, ?, 'Activo')");
    $stmt->bind_param("issis", $id_persona, $fecha_inicio, $fecha_final, $total_dias, $motivo);

    if ($stmt->execute()) {
        echo "<script>alert('Incapacidad registrada correctamente.'); window.location.href='../Incapacidades.php';</script>";
    } else {
        echo "Error al registrar incapacidad: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
