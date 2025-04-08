<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

// Obtener personas
$personas = $conn->query("SELECT id_persona, nom_persona, apellido_paterno, apellido_materno FROM persona");
?>

<h1 class="titulo">Registro de Incapacidad</h1>

<form class="form_reg_usuario" action="Registrar_I.php" method="POST">
    <label for="id_persona">Empleado:</label>
    <select id="id_persona" name="id_persona" required>
        <option value="">Seleccionar persona</option>
        <?php while($row = $personas->fetch_assoc()) : ?>
            <option value="<?= $row['id_persona'] ?>">
                <?= $row['nom_persona'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="fecha_inicio">Fecha de Inicio:</label>
    <input type="date" name="fecha_inicio" required><br><br>

    <label for="fecha_final">Fecha Final:</label>
    <input type="date" name="fecha_final" required><br><br>

    <label for="motivo">Motivo:</label>
    <select name="motivo" required>
        <option value="">Seleccionar motivo</option>
        <option value="Enfermedad">Enfermedad</option>
        <option value="Accidente">Accidente</option>
        <option value="Maternidad">Maternidad</option>
        <option value="Otros">Otros</option>
    </select><br><br>

    <input type="submit" value="Registrar Incapacidad">
</form>

<a href="../Usuario.php" class="regresar">Regresar</a>
<?php include('../../Nav/footer.php'); ?>
