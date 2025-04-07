<?php
include('../BD/ConexionBD.php');
include('../Nav/header.php');

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta SQL que une nomina con persona, percepciones y deducciones
$sql = "SELECT 
            nomina.id_nomina,
            persona.nom_persona AS Nombre,
            persona.apellido_paterno AS Apellido_Paterno,
            persona.apellido_materno AS Apellido_Materno,
            nomina.fecha_nomina,
            nomina.periodo_inicio,
            nomina.periodo_final,
            nomina.dias_pagados,

            -- Sumatoria de percepciones
            (percepciones.sueldo_base + percepciones.puntualidad + percepciones.asistencia + 
             IFNULL(percepciones.bono, 0) + IFNULL(percepciones.vales_despensa, 0) + 
             IFNULL(percepciones.compensaciones, 0) + IFNULL(percepciones.vacaciones, 0) + 
             IFNULL(percepciones.prima_antiguedad, 0)) AS total_percepciones,

            -- Sumatoria de deducciones
            (deducciones.isr + deducciones.imss + deducciones.caja_ahorro + 
             deducciones.prestamos + deducciones.infonavit + 
             deducciones.fonacot + deducciones.cuota_sindical) AS total_deducciones

        FROM nomina
        JOIN persona ON nomina.id_persona = persona.id_persona
        JOIN percepciones ON nomina.id_percepcion = percepciones.id_percepcion
        JOIN deducciones ON nomina.id_deducciones = deducciones.id_deducciones";

$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<body>
    <h1 class="titulo">Registro de Nómina</h1>
    <br>
    <table class='tabla'>
        <thead>
            <tr class='cont'>
                <th>ID Nómina</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Fecha Nómina</th>
                <th>Inicio del Periodo</th>
                <th>Final del Periodo</th>
                <th>Días Pagados</th>
                <th>Total Percepciones</th>
                <th>Total Deducciones</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($fila['id_nomina']) ?></td>
                    <td><?= htmlspecialchars($fila['Nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['Apellido_Paterno']) ?></td>
                    <td><?= htmlspecialchars($fila['Apellido_Materno']) ?></td>
                    <td><?= htmlspecialchars($fila['fecha_nomina']) ?></td>
                    <td><?= htmlspecialchars($fila['periodo_inicio']) ?></td>
                    <td><?= htmlspecialchars($fila['periodo_final']) ?></td>
                    <td><?= htmlspecialchars($fila['dias_pagados']) ?></td>
                    <td>$<?= number_format($fila['total_percepciones'], 2) ?></td>
                    <td>$<?= number_format($fila['total_deducciones'], 2) ?></td>
                    <td><a href='Editar/Modificar.php?id_nomina=<?= htmlspecialchars($fila['id_nomina']) ?>' class='editar'>Editar</a></td>
                    <td><a href='Eliminar_N/Eliminar_N.php?id_nomina=<?= htmlspecialchars($fila['id_nomina']) ?>' class='eliminar'>Eliminar</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

<?php include('../Nav/footer.php'); ?>
</html>
