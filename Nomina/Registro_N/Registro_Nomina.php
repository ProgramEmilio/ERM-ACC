<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

// Obtener personas
$personas = $conn->query("SELECT id_persona, nom_persona, apellido_paterno, apellido_materno FROM persona");

$conn->close();
?>

<h1 class="titulo">Registro de Nómina</h1>

<form class="form_reg_usuario" action="Registra_N.php" method="POST">
    <label for="id_persona">Empleado:</label>
    <select name="id_persona" required>
        <option value="">Seleccionar persona</option>
        <?php while($row = $personas->fetch_assoc()): ?>
            <option value="<?= $row['id_persona'] ?>">
                <?= $row['nom_persona'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="periodo_inicio">Periodo Inicio:</label>
    <input type="date" name="periodo_inicio" required><br><br>

    <label for="dias_trabajados">Días Trabajados:</label>
    <select name="dias_trabajados" required>
        <?php for($i = 1; $i <= 15; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <input type="submit" value="Registrar Nómina">
</form>

<a href="../Usuario.php" class="regresar">Regresar</a>
<?php include('../../Nav/footer.php'); ?>
