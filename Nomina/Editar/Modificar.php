<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

$id_nomina = $_GET['id_nomina'];

// Obtener datos actuales de la nómina, percepciones y deducciones
$sql = "SELECT n.*, p.*, d.*, per.sueldo, per.nom_persona, per.apellido_paterno, per.apellido_materno
        FROM nomina n
        JOIN deducciones d ON n.id_deducciones = d.id_deducciones
        JOIN percepciones p ON n.id_percepcion = p.id_percepcion
        JOIN persona per ON n.id_persona = per.id_persona
        WHERE n.id_nomina = $id_nomina";

$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<body>
<h1 class="titulo">Modificar Nómina</h1>

<form class="form_reg_usuario" action="Editar_N.php" method="POST">
    <!-- IDs ocultos -->
    <input type="hidden" name="id_nomina" value="<?= $data['id_nomina'] ?>">
    <input type="hidden" name="id_percepcion" value="<?= $data['id_percepcion'] ?>">
    <input type="hidden" name="id_deducciones" value="<?= $data['id_deducciones'] ?>">

    <h2>Datos de Nómina</h2>
    <label>Empleado:</label>
    <input type="text" value="<?= $data['nom_persona'] . ' ' . $data['apellido_paterno'] . ' ' . $data['apellido_materno'] ?>" readonly><br><br>

    <label for="fecha_nomina">Fecha de Nómina:</label>
    <input type="date" name="fecha_nomina" value="<?= $data['fecha_nomina'] ?>" required><br><br>

    <label for="periodo_inicio">Periodo Inicio:</label>
    <input type="date" name="periodo_inicio" value="<?= $data['periodo_inicio'] ?>" required><br><br>

    <label for="periodo_final">Periodo Final:</label>
    <input type="date" name="periodo_final" value="<?= $data['periodo_final'] ?>" required><br><br>

    <label for="dias_trabajados">Días Trabajados:</label>
    <input type="number" step="0.01" name="dias_trabajados" value="<?= $data['dias_trabajados'] ?>" required><br><br>

    <label for="dias_justificados">Días Justificados:</label>
    <input type="number" step="0.01" name="dias_justificados" value="<?= $data['dias_justificados'] ?>" required><br><br>

    <h2>Percepciones</h2>
    <label>Sueldo Base:</label>
    <input type="number" name="sueldo_base" step="0.01" value="<?= $data['sueldo_base'] ?>" readonly><br><br>

    <label>Puntualidad:</label>
    <input type="number" step="0.01" name="puntualidad" value="<?= $data['puntualidad'] ?>" required><br><br>

    <label>Asistencia:</label>
    <input type="number" step="0.01" name="asistencia" value="<?= $data['asistencia'] ?>" required><br><br>

    <label>Bono:</label>
    <input type="number" step="0.01" name="bono" value="<?= $data['bono'] ?>"><br><br>

    <label>Vales de Despensa:</label>
    <input type="number" step="0.01" name="vales_despensa" value="<?= $data['vales_despensa'] ?>"><br><br>

    <label>Compensaciones:</label>
    <input type="number" step="0.01" name="compensaciones" value="<?= $data['compensaciones'] ?>"><br><br>

    <label>Vacaciones:</label>
    <input type="number" step="0.01" name="vacaciones" value="<?= $data['vacaciones'] ?>"><br><br>

    <label>Prima de Antigüedad:</label>
    <input type="number" step="0.01" name="prima_antiguedad" value="<?= $data['prima_antiguedad'] ?>"><br><br>

    <h2>Deducciones</h2>
    <label>ISR:</label>
    <input type="number" step="0.01" name="isr" value="<?= $data['isr'] ?>" required><br><br>

    <label>IMSS:</label>
    <input type="number" step="0.01" name="imss" value="<?= $data['imss'] ?>" required><br><br>

    <label>Caja de Ahorro:</label>
    <input type="number" step="0.01" name="caja_ahorro" value="<?= $data['caja_ahorro'] ?>" required><br><br>

    <label>Préstamos:</label>
    <input type="number" step="0.01" name="prestamos" value="<?= $data['prestamos'] ?>" required><br><br>

    <label>INFONAVIT:</label>
    <input type="number" step="0.01" name="infonavit" value="<?= $data['infonavit'] ?>" required><br><br>

    <label>FONACOT:</label>
    <input type="number" step="0.01" name="fonacot" value="<?= $data['fonacot'] ?>" required><br><br>

    <label>Cuota Sindical:</label>
    <input type="number" step="0.01" name="cuota_sindical" value="<?= $data['cuota_sindical'] ?>" required><br><br>

    <input type="submit" value="Actualizar Nómina">
</form>

<a href="../Nomina.php" class="regresar">Regresar</a>

<script>
document.querySelector('form').addEventListener('submit', function (e) {
    const diasTrabajados = parseFloat(document.querySelector('[name="dias_trabajados"]').value) || 0;
    const diasJustificados = parseFloat(document.querySelector('[name="dias_justificados"]').value) || 0;
    const diasPagados = diasTrabajados + diasJustificados;

    if (diasPagados > 15) {
        alert("Los días pagados no pueden ser mayores a 15.\nActualmente tienes: " + diasPagados);
        e.preventDefault();
    }
});
</script>

</body>
<?php include('../../Nav/footer.php'); ?>
