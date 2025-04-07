<?php
include('../../BD/ConexionBD.php');

// Verifica si se ha pasado un id válido
if (isset($_GET['id_nomina'])) {
    $id_nomina = intval($_GET['id_nomina']);

    // Buscar los IDs de percepcion y deduccion relacionados a esta nómina
    $consulta = "SELECT id_percepcion, id_deducciones FROM nomina WHERE id_nomina = ?";
    $stmt = $conn->prepare($consulta);
    $stmt->bind_param("i", $id_nomina);
    $stmt->execute();
    $stmt->bind_result($id_percepcion, $id_deducciones);
    $stmt->fetch();
    $stmt->close();

    // Verificar que existan datos relacionados
    if ($id_percepcion && $id_deducciones) {
        // Eliminar de la tabla nomina
        $stmt_nomina = $conn->prepare("DELETE FROM nomina WHERE id_nomina = ?");
        $stmt_nomina->bind_param("i", $id_nomina);
        $stmt_nomina->execute();
        $stmt_nomina->close();

        // Eliminar de la tabla percepciones
        $stmt_percepciones = $conn->prepare("DELETE FROM percepciones WHERE id_percepcion = ?");
        $stmt_percepciones->bind_param("i", $id_percepcion);
        $stmt_percepciones->execute();
        $stmt_percepciones->close();

        // Eliminar de la tabla deducciones
        $stmt_deducciones = $conn->prepare("DELETE FROM deducciones WHERE id_deducciones = ?");
        $stmt_deducciones->bind_param("i", $id_deducciones);
        $stmt_deducciones->execute();
        $stmt_deducciones->close();

        // Redireccionar de nuevo a la página principal
        header("Location: ../Nomina.php?eliminado=ok");
        exit();
    } else {
        echo "Error: No se encontraron datos relacionados.";
    }
} else {
    echo "Error: ID de nómina no válido.";
}
?>
