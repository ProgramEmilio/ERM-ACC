<?php
include('../BD/ConexionBD.php');
include('../Nav/header.php');

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta SQL para mostrar solo incapacidades con nombre de persona
$sql = "SELECT 
            i.id_incapacidad,
            p.nom_persona AS Nombre,
            p.apellido_paterno AS Apellido_Paterno,
            p.apellido_materno AS Apellido_Materno,
            i.fecha_inicio,
            i.fecha_final,
            i.total_dias,
            i.motivo,
            i.estatus
        FROM incapacidad i
        JOIN persona p ON i.id_persona = p.id_persona
        ORDER BY i.id_incapacidad DESC";

$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<body>
    <h1 class="titulo">Listado de Incapacidades</h1>
    <br>
    <table class='tabla'>
        <thead>
            <tr class='cont'>
                <th>ID Incapacidad</th>
                <th>Nombre Completo</th>
                <th>Fecha Inicio</th>
                <th>Fecha Final</th>
                <th>Total de Días</th>
                <th>Motivo</th>
                <th>Estatus</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($fila['id_incapacidad']) ?></td>
                    <td><?= htmlspecialchars($fila['Nombre']) . ' ' . htmlspecialchars($fila['Apellido_Paterno']) . ' ' . htmlspecialchars($fila['Apellido_Materno']) ?></td>
                    <td><?= htmlspecialchars($fila['fecha_inicio']) ?></td>
                    <td><?= htmlspecialchars($fila['fecha_final']) ?></td>
                    <td><?= htmlspecialchars($fila['total_dias']) ?></td>
                    <td><?= htmlspecialchars($fila['motivo']) ?></td>
                    <td><?= htmlspecialchars($fila['estatus']) ?></td>
                    <td><a href='Editar/Modificar.php?id_incapacidad=<?= htmlspecialchars($fila['id_incapacidad']) ?>' class='editar'>Editar</a></td>
                    <td><a href='Eliminar/Eliminar_Incapacidad.php?id_incapacidad=<?= htmlspecialchars($fila['id_incapacidad']) ?>' class='eliminar'>Eliminar</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

<?php include('../Nav/footer.php'); ?>
</html>
