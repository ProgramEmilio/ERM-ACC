<?php
include('../../BD/ConexionBD.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $id_incapacidad = $_POST['id_incapacidad'];
    $id_persona = $_POST['id_persona'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_final = $_POST['fecha_final'];
    $total_dias = $_POST['total_dias'];
    $motivo = $_POST['motivo'];
    $estatus = $_POST['estatus'];

    // Validar conexión
    if (!$conn) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Preparar sentencia SQL para actualizar
    $sql = "UPDATE incapacidad 
            SET fecha_inicio = ?, 
                fecha_final = ?, 
                total_dias = ?, 
                motivo = ?, 
                estatus = ?
            WHERE id_incapacidad = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $fecha_inicio, $fecha_final, $total_dias, $motivo, $estatus, $id_incapacidad);

    if ($stmt->execute()) {
        // Redirigir con éxito
        header("Location: ../Incapacidades.php?mensaje=actualizado");
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Acceso denegado.";
}
?>
