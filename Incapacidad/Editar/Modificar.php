<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$id_incapacidad = $_GET['id_incapacidad'];

// Consulta para obtener los datos de la incapacidad y el nombre de la persona
$query = "SELECT i.*, p.nom_persona, p.apellido_paterno, p.apellido_materno
          FROM incapacidad i
          JOIN persona p ON i.id_persona = p.id_persona
          WHERE i.id_incapacidad = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_incapacidad);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();
?>

<body>
<h1 class="titulo">Modificar Incapacidad</h1>

<form class="form_reg_usuario" action="Editar_U.php" method="POST" oninput="calcularDias()">
    <input type="hidden" name="id_incapacidad" value="<?= $datos['id_incapacidad'] ?>">
    <input type="hidden" name="id_persona" value="<?= $datos['id_persona'] ?>">

    <!-- Mostrar nombre de la persona solo como referencia -->
    <label>Empleado:</label>
    <input type="text" value="<?= $datos['nom_persona'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno'] ?>" disabled><br><br>

    <!-- Fechas -->
    <label>Fecha de Inicio:</label>
    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= $datos['fecha_inicio'] ?>" required><br><br>

    <label>Fecha Final:</label>
    <input type="date" id="fecha_final" name="fecha_final" value="<?= $datos['fecha_final'] ?>" required><br><br>

    <!-- Días calculados -->
    <label>Total de Días:</label>
    <input type="number" id="total_dias" name="total_dias" value="<?= $datos['total_dias'] ?>" readonly required><br><br>

    <!-- Motivo -->
    <label for="motivo">Motivo:</label>
    <select id="motivo" name="motivo" required>
        <option value="">Seleccionar motivo</option>
        <option value="Enfermedad" <?= ($datos['motivo'] == 'Enfermedad') ? 'selected' : '' ?>>Enfermedad</option>
        <option value="Accidente" <?= ($datos['motivo'] == 'Accidente') ? 'selected' : '' ?>>Accidente</option>
        <option value="Maternidad" <?= ($datos['motivo'] == 'Maternidad') ? 'selected' : '' ?>>Maternidad</option>
        <option value="Otros" <?= ($datos['motivo'] == 'Otros') ? 'selected' : '' ?>>Otros</option>
    </select><br><br>


    <!-- Estatus -->
    <label>Estatus:</label>
    <select name="estatus" required>
        <option value="Activo" <?= $datos['estatus'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
        <option value="Inactivo" <?= $datos['estatus'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
    </select><br><br>

    <input type="submit" value="Guardar Cambios">
</form>

<a href="../ListaIncapacidades.php" class="regresar">Regresar</a>

<script>
function calcularDias() {
    const inicio = document.getElementById('fecha_inicio').value;
    const final = document.getElementById('fecha_final').value;
    const totalDiasInput = document.getElementById('total_dias');

    if (inicio && final) {
        const fechaInicio = new Date(inicio);
        const fechaFinal = new Date(final);
        const diferencia = (fechaFinal - fechaInicio) / (1000 * 60 * 60 * 24) + 1; // +1 para incluir el día de inicio
        totalDiasInput.value = diferencia >= 0 ? diferencia : 0;
    } else {
        totalDiasInput.value = '';
    }
}
window.onload = calcularDias;
</script>

</body>
<?php include('../../Nav/footer.php'); ?>
</html>
