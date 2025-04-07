<?php
include('../../BD/ConexionBD.php');

// Verifica si llegaron los datos requeridos
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Insertar percepciones
    $sql_percepciones = "INSERT INTO percepciones (sueldo_base, puntualidad, asistencia, bono, vales_despensa, compensaciones, vacaciones, prima_antiguedad)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql_percepciones);
    $stmt1->bind_param("dddddddd",
        $_POST['sueldo_base'],
        $_POST['puntualidad'],
        $_POST['asistencia'],
        $_POST['bono'],
        $_POST['vales_despensa'],
        $_POST['compensaciones'],
        $_POST['vacaciones'],
        $_POST['prima_antiguedad']
    );
    $stmt1->execute();
    $id_percepcion = $conn->insert_id;
    $stmt1->close();

    // 2. Insertar deducciones
    $sql_deducciones = "INSERT INTO deducciones (isr, imss, caja_ahorro, prestamos, infonavit, fonacot, cuota_sindical)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql_deducciones);
    $stmt2->bind_param("ddddddd",
        $_POST['isr'],
        $_POST['imss'],
        $_POST['caja_ahorro'],
        $_POST['prestamos'],
        $_POST['infonavit'],
        $_POST['fonacot'],
        $_POST['cuota_sindical']
    );
    $stmt2->execute();
    $id_deducciones = $conn->insert_id;
    $stmt2->close();

    // 3. Insertar nómina (con id_persona ahora)
    $sql_nomina = "INSERT INTO nomina (id_persona, fecha_nomina, periodo_inicio, periodo_final, dias_pagados, id_deducciones, id_percepcion)
    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt3 = $conn->prepare($sql_nomina);
    $stmt3->bind_param("isssddd",
    $_POST['id_persona'],
    $_POST['fecha_nomina'],
    $_POST['periodo_inicio'],
    $_POST['periodo_final'],
    $_POST['dias_pagados'],
    $id_deducciones,
    $id_percepcion
    );
    $stmt3->execute();
    $stmt3->close();


    echo "<script>alert('Nómina registrada correctamente'); window.location.href='../Nomina.php';</script>";
} else {
    echo "Error en el envío del formulario.";
}

$conn->close();
?>
