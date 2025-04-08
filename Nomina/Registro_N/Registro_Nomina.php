<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');

// Obtener personas
$personas = $conn->query("SELECT id_persona, sueldo, CONCAT(nom_persona, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo FROM persona");
$fechaActual = date("Y-m-d");
?>

<body>
<h1 class="titulo">Registro de Nómina</h1>

<form class="form_reg_usuario" action="Registra_N.php" method="POST">
    <!-- NOMINA -->
    <h2>Datos de Nómina</h2>
    <label for="id_persona">Empleado:</label>
    <select name="id_persona" id="id_persona" required onchange="actualizarDatos()">
        <option value="">Selecciona un empleado</option>
        <?php while($row = $personas->fetch_assoc()): ?>
            <option value="<?= $row['id_persona'] ?>" data-sueldo="<?= $row['sueldo'] ?>">
                <?= $row['nombre_completo'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="fecha_nomina">Fecha de Nómina:</label>
    <input type="date" id="fecha_nomina" name="fecha_nomina" value="<?= $fechaActual ?>" readonly required><br><br>

    <label for="periodo_inicio">Periodo Inicio:</label>
    <input type="date" id="periodo_inicio" name="periodo_inicio" required onchange="calcularPeriodoFinal()"><br><br>

    <label for="periodo_final">Periodo Final:</label>
    <input type="date" id="periodo_final" name="periodo_final" readonly required><br><br>

    <label for="dias_pagados">Días Trabajados:</label>
    <select name="dias_pagados" id="dias_pagados" required onchange="actualizarDatos()">
        <option value="">Selecciona días</option>
        <?php for ($i = 1; $i <= 15; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <!-- PERCEPCIONES -->
    <h2>Percepciones</h2>
    <label for="sueldo_base">Sueldo Base:</label>
    <input type="number" step="0.01" name="sueldo_base" id="sueldo_base" readonly required><br><br>

    <label for="puntualidad">Puntualidad:</label>
    <input type="number" step="0.01" name="puntualidad" id="puntualidad" readonly required><br><br>

    <label for="asistencia">Asistencia:</label>
    <input type="number" step="0.01" name="asistencia" readonly required><br><br>

    <label for="bono">Bono:</label>
    <input type="number" step="0.01" name="bono" readonly><br><br>

    <label for="vales_despensa">Vales de Despensa:</label>
    <input type="number" step="0.01" name="vales_despensa" readonly><br><br>

    <label for="compensaciones">Compensaciones:</label>
    <input type="number" step="0.01" name="compensaciones" readonly><br><br>

    <label for="vacaciones">Vacaciones:</label>
    <input type="number" step="0.01" name="vacaciones" readonly><br><br>

    <label for="prima_antiguedad">Prima de Antigüedad:</label>
    <input type="number" step="0.01" name="prima_antiguedad" readonly><br><br>

    <!-- DEDUCCIONES -->
    <h2>Deducciones</h2>
    <label for="isr">ISR:</label>
    <input type="number" step="0.01" name="isr" readonly required><br><br>

    <label for="imss">IMSS:</label>
    <input type="number" step="0.01" name="imss" readonly required><br><br>

    <label for="caja_ahorro">Caja de Ahorro:</label>
    <input type="number" step="0.01" name="caja_ahorro" readonly required><br><br>

    <label for="prestamos">Préstamos:</label>
    <input type="number" step="0.01" name="prestamos" readonly required><br><br>

    <label for="infonavit">INFONAVIT:</label>
    <input type="number" step="0.01" name="infonavit" readonly required><br><br>

    <label for="fonacot">FONACOT:</label>
    <input type="number" step="0.01" name="fonacot" readonly required><br><br>

    <label for="cuota_sindical">Cuota Sindical:</label>
    <input type="number" step="0.01" name="cuota_sindical" readonly required><br><br>

    <input type="submit" value="Registrar Nómina">
</form>

<script>
function calcularPeriodoFinal() {
    const inicio = document.getElementById("periodo_inicio").value;
    if (inicio) {
        const fechaInicio = new Date(inicio);
        fechaInicio.setDate(fechaInicio.getDate() + 14);
        const fechaFinal = fechaInicio.toISOString().split('T')[0];
        document.getElementById("periodo_final").value = fechaFinal;
    }
}

function actualizarDatos() {
    const select = document.getElementById("id_persona");
    const sueldoBase = parseFloat(select.options[select.selectedIndex]?.getAttribute("data-sueldo")) || 0;
    const dias = parseInt(document.getElementById("dias_pagados").value) || 0;

    document.getElementById("sueldo_base").value = sueldoBase.toFixed(2);

    const puntualidad = (dias * sueldoBase / 15).toFixed(2);
    document.getElementById("puntualidad").value = puntualidad;

    // Todos los demás cálculos se hacen con puntualidad
    const p = parseFloat(puntualidad) || 0;
    document.getElementsByName("asistencia")[0].value = (p * 0.10).toFixed(2);
    document.getElementsByName("bono")[0].value = (p * 0.05).toFixed(2);
    document.getElementsByName("vales_despensa")[0].value = (p * 0.08).toFixed(2);
    document.getElementsByName("compensaciones")[0].value = (p * 0.03).toFixed(2);
    document.getElementsByName("vacaciones")[0].value = (p * 0.06).toFixed(2);
    document.getElementsByName("prima_antiguedad")[0].value = (p * 0.02).toFixed(2);

    // Deducciones (también con puntualidad)
    document.getElementsByName("isr")[0].value = (p * 0.16).toFixed(2);
    document.getElementsByName("imss")[0].value = (p * 0.02375).toFixed(2);
    document.getElementsByName("caja_ahorro")[0].value = (p * 0.07).toFixed(2);
    document.getElementsByName("prestamos")[0].value = (p * 0.05).toFixed(2);
    document.getElementsByName("infonavit")[0].value = (p * 0.05).toFixed(2);
    document.getElementsByName("fonacot")[0].value = (p * 0.02).toFixed(2);
    document.getElementsByName("cuota_sindical")[0].value = (p * 0.01).toFixed(2);
}
</script>

<a href="../Nomina.php" class="regresar">Regresar</a>
</body>
<?php include('../../Nav/footer.php'); ?>
</html>
