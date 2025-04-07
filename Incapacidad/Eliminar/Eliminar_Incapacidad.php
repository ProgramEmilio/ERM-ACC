<?php
include('../../BD/ConexionBD.php');

// Verificar si se recibió el ID de la incapacidad
if (isset($_GET['id_incapacidad'])) {
    $id_incapacidad = intval($_GET['id_incapacidad']);

    // Consulta para eliminar la incapacidad
    $sql = "DELETE FROM incapacidad WHERE id_incapacidad = $id_incapacidad";

    if (mysqli_query($conn, $sql)) {
        // Redirigir de nuevo a la lista con mensaje (opcional)
        header("Location: ../ListaIncapacidades.php?mensaje=eliminado");
        exit();
    } else {
        echo "Error al eliminar el registro: " . mysqli_error($conn);
    }
} else {
    echo "ID de incapacidad no proporcionado.";
}
?>
