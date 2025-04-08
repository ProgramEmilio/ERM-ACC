<?php
include('../BD/ConexionBD.php');
include('../Nav/header.php');

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta SQL actualizada (asegúrate que los campos existen en tu nueva BD)
$sql = "
    SELECT 
        n.id_nomina,
        p.nom_persona AS Nombre,
        p.apellido_paterno AS Apellido_Paterno,
        p.apellido_materno AS Apellido_Materno,
        n.fecha_nomina,
        n.periodo_inicio,
        n.periodo_final,
        n.dias_total as dias_pagados,

        -- Percepciones
        (pr.sueldo_base + pr.puntualidad + pr.asistencia + 
         IFNULL(pr.bono, 0) + IFNULL(pr.vales_despensa, 0) + 
         IFNULL(pr.compensaciones, 0) + IFNULL(pr.vacaciones, 0) + 
         IFNULL(pr.prima_antiguedad, 0)) AS total_percepciones,

        -- Deducciones
        (d.isr + d.imss + d.caja_ahorro + 
         d.prestamos + d.infonavit + 
         d.fonacot + d.cuota_sindical) AS total_deducciones

    FROM nomina n
    INNER JOIN persona p ON n.id_persona = p.id_persona
    INNER JOIN percepciones pr ON n.id_percepcion = pr.id_percepcion
    INNER JOIN deducciones d ON n.id_deducciones = d.id_deducciones
";

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
                    <td><a href='Editar/Modificar.php?id_nomina=<?= urlencode($fila['id_nomina']) ?>' class='editar'>Editar</a></td>
                    <td><a href='Eliminar_N/Eliminar_N.php?id_nomina=<?= urlencode($fila['id_nomina']) ?>' class='eliminar' onclick="return confirm('¿Estás seguro de eliminar esta nómina?');">Eliminar</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

<?php include('../Nav/footer.php'); ?>
</html>
