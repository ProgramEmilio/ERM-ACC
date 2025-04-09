<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

// Obtener ID desde URL
$id_nomina = $_GET['id_nomina'] ?? null;
if (!$id_nomina) {
    echo "ID de nómina no proporcionado.";
    exit;
}

// Obtener datos de nómina, persona, percepciones y deducciones
$sql = "SELECT n.*, p.nom_persona, p.apellido_paterno, p.apellido_materno, p.sueldo,
               per.*, d.*
        FROM nomina n
        INNER JOIN persona p ON n.id_persona = p.id_persona
        INNER JOIN percepciones per ON n.id_percepcion = per.id_percepcion
        INNER JOIN deducciones d ON n.id_deducciones = d.id_deducciones
        WHERE n.id_nomina = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_nomina);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Paso 1: Obtener periodo_inicio, periodo_final e id_persona desde la tabla nomina
$stmt = $conn->prepare("
    SELECT periodo_inicio, periodo_final, id_persona
    FROM nomina
    WHERE id_nomina = ?
");
$stmt->bind_param("i", $id_nomina);
$stmt->execute();
$stmt->bind_result($periodo_inicio, $periodo_final, $id_persona);
$stmt->fetch();
$stmt->close();

// Paso 2: Obtener días justificados dentro de ese rango
$stmt = $conn->prepare("
    SELECT SUM(DATEDIFF(
        LEAST(fecha_final, ?), 
        GREATEST(fecha_inicio, ?)
    ) + 1) AS dias_justificados
    FROM incapacidad
    WHERE id_persona = ?
    AND estatus = 'Activo'
    AND fecha_final >= ? 
    AND fecha_inicio <= ?
");
$stmt->bind_param("ssiss", $periodo_final, $periodo_inicio, $id_persona, $periodo_inicio, $periodo_final);
$stmt->execute();
$stmt->bind_result($dias_justificados);
$stmt->fetch();
$stmt->close();

$dias_justificados = $dias_justificados ?: 0;




if (!$data) {
    echo "Registro de nómina no encontrado.";
    exit;
}
?>

<h1 class="titulo">Modificar Nómina</h1>

<form class="form_reg_usuario" action="Editar_N.php" method="POST">
    <input type="hidden" name="id_nomina" value="<?= $data['id_nomina'] ?>">
    <input type="hidden" id="sueldo_base_oculto" value="<?= $data['sueldo'] ?>">

    <label>Empleado:</label>
    <input type="text" value="<?= $data['nom_persona'] . ' ' . $data['apellido_paterno'] . ' ' . $data['apellido_materno'] ?>" readonly><br><br>

    <label>Fecha Nómina:</label>
    <input type="date" name="fecha_nomina" value="<?= $data['fecha_nomina'] ?>" readonly><br><br>

    <label for="periodo_inicio">Periodo Inicio:</label>
    <input type="date" name="periodo_inicio" id="periodo_inicio" value="<?= $data['periodo_inicio'] ?>" required onchange="actualizarPeriodoFinal()"><br><br>

    <label for="periodo_final">Periodo Final:</label>
    <input type="date" name="periodo_final" id="periodo_final" value="<?= $data['periodo_final'] ?>" readonly><br><br>

    <label for="dias_justificados">Días Justificados:</label>
    <input type="number" name="dias_justificados" id="dias_justificados" value="<?= $dias_justificados ?>" readonly><br><br>

    <label for="dias_pagados">Días Trabajados:</label>
    <select name="dias_trabajados" id="dias_trabajados" required onchange="actualizarDatos()">
        <option value="">Selecciona días</option>
        <?php for ($i = 1; $i <= 15; $i++): ?>
            <option value="<?= $i ?>" <?= $data['dias_trabajados'] == $i ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <label for="dias_total">Total Días (máx 15):</label>
    <input type="text" name="dias_total" id="dias_total" value="<?= $data['dias_total'] ?>" readonly><br><br>

    <fieldset>
        <h2>Percepciones</h2>
        <label>Sueldo Base:</label>
        <input type="text" id="sueldo_base" name="sueldo_base" value="<?= $data['sueldo_base'] ?>" readonly><br>

        <label>Puntualidad:</label>
        <input type="text" id="puntualidad" name="puntualidad" value="<?= $data['puntualidad'] ?>" readonly><br>

        <label>Asistencia:</label>
        <input type="text" name="asistencia" value="<?= $data['asistencia'] ?>" readonly><br>

        <label>Bono:</label>
        <input type="text" name="bono" value="<?= $data['bono'] ?>" readonly><br>

        <label>Vales Despensa:</label>
        <input type="text" name="vales_despensa" value="<?= $data['vales_despensa'] ?>" readonly><br>

        <label>Compensaciones:</label>
        <input type="text" name="compensaciones" value="<?= $data['compensaciones'] ?>" readonly><br>

        <label>Prima Antigüedad:</label>
        <input type="text" name="prima_antiguedad" value="<?= $data['prima_antiguedad'] ?>" readonly><br>
    </fieldset>

    <fieldset>
        <h2>Deducciones</h2>
        <label>ISR:</label>
        <input type="text" name="isr" value="<?= $data['isr'] ?>" readonly><br>

        <label>IMSS:</label>
        <input type="text" name="imss" value="<?= $data['imss'] ?>" readonly><br>

        <label>Caja Ahorro:</label>
        <input type="text" name="caja_ahorro" value="<?= $data['caja_ahorro'] ?>" readonly><br>

        <label>Préstamos:</label>
        <input type="text" name="prestamos" value="<?= $data['prestamos'] ?>" readonly><br>

        <label>INFONAVIT:</label>
        <input type="text" name="infonavit" value="<?= $data['infonavit'] ?>" readonly><br>

        <label>FONACOT:</label>
        <input type="text" name="fonacot" value="<?= $data['fonacot'] ?>" readonly><br>

        <label>Cuota Sindical:</label>
        <input type="text" name="cuota_sindical" value="<?= $data['cuota_sindical'] ?>" readonly><br>
    </fieldset>

    <input type="submit" value="Guardar Cambios">
</form>

<a href="../Nomina.php" class="regresar">Regresar</a>

<script>
function actualizarPeriodoFinal() {
    const inicio = document.getElementById('periodo_inicio').value;
    if (inicio) {
        const fechaInicio = new Date(inicio);
        fechaInicio.setDate(fechaInicio.getDate() + 13);
        document.getElementById('periodo_final').value = fechaInicio.toISOString().split('T')[0];
    }
}

function actualizarDatos() {
    const sueldoBase = parseFloat(document.getElementById("sueldo_base_oculto").value) || 0;
    const dias = parseInt(document.getElementById("dias_trabajados").value) || 0;
    const justificados = parseInt(document.getElementById("dias_justificados").value) || 0;
    const total = dias + justificados;

    if (total > 15) {
        alert("La suma de días trabajados y justificados no puede exceder 15.");
        document.getElementById("dias_trabajados").value = "";
        document.getElementById("dias_total").value = "";
        return;
    }

    document.getElementById("dias_total").value = total;

    const puntualidad = (total * sueldoBase / 15).toFixed(2);
    document.getElementById("sueldo_base").value = sueldoBase.toFixed(2);
    document.getElementById("puntualidad").value = puntualidad;

    const p = parseFloat(puntualidad) || 0;

    document.getElementsByName("asistencia")[0].value = (p * 0.10).toFixed(2);
    document.getElementsByName("bono")[0].value = (p * 0.05).toFixed(2);
    document.getElementsByName("vales_despensa")[0].value = (p * 0.08).toFixed(2);
    document.getElementsByName("compensaciones")[0].value = (p * 0.03).toFixed(2);
    document.getElementsByName("prima_antiguedad")[0].value = (p * 0.02).toFixed(2);

    document.getElementsByName("isr")[0].value = (p * 0.16).toFixed(2);
    document.getElementsByName("imss")[0].value = (p * 0.02375).toFixed(2);
    document.getElementsByName("caja_ahorro")[0].value = (p * 0.07).toFixed(2);
    document.getElementsByName("prestamos")[0].value = (p * 0.05).toFixed(2);
    document.getElementsByName("infonavit")[0].value = (p * 0.05).toFixed(2);
    document.getElementsByName("fonacot")[0].value = (p * 0.02).toFixed(2);
    document.getElementsByName("cuota_sindical")[0].value = (p * 0.01).toFixed(2);
}
actualizarDatos();
</script>

<?php include('../../Nav/footer.php'); ?>
