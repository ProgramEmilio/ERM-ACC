<?php
include('../../BD/ConexionBD.php');
include('../../Nav/header2.php');
// Obtener personas
$personas = $conn->query("SELECT id_persona, CONCAT(nom_persona, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo FROM persona");
$personas = $conn->query("SELECT id_persona, sueldo, CONCAT(nom_persona, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo FROM persona");

?>

<body>
<h1 class="titulo">Registro de Nómina</h1>

<form class="form_reg_usuario" action="Registra_N.php" method="POST">
    <!-- NOMINA -->
    <h2>Datos de Nómina</h2>
    <label for="id_persona">Empleado:</label>
    <select name="id_persona" id="id_persona" required onchange="actualizarSueldoBase()">
        <option value="">Selecciona un empleado</option>
        <?php while($row = $personas->fetch_assoc()): ?>
            <option value="<?= $row['id_persona'] ?>" data-sueldo="<?= $row['sueldo'] ?>">
                <?= $row['nombre_completo'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>


    <label for="fecha_nomina">Fecha de Nómina:</label>
    <input type="date" id="fecha_nomina" name="fecha_nomina" required><br><br>

    <label for="periodo_inicio">Periodo Inicio:</label>
    <input type="date" id="periodo_inicio" name="periodo_inicio" required><br><br>

    <label for="periodo_final">Periodo Final:</label>
    <input type="date" id="periodo_final" name="periodo_final" required><br><br>

    <label for="dias_pagados">Días Pagados:</label>
    <input type="number" step="0.01" id="dias_pagados" name="dias_pagados" required><br><br>

    <!-- PERCEPCIONES -->
    <h2>Percepciones</h2>
    <label for="sueldo_base">Sueldo Base:</label>
    <input type="number" step="0.01" name="sueldo_base" id="sueldo_base" readonly required><br><br>

    <label for="puntualidad">Puntualidad:</label>
    <input type="number" step="0.01" name="puntualidad" required><br><br>

    <label for="asistencia">Asistencia:</label>
    <input type="number" step="0.01" name="asistencia" required><br><br>

    <label for="bono">Bono:</label>
    <input type="number" step="0.01" name="bono"><br><br>

    <label for="vales_despensa">Vales de Despensa:</label>
    <input type="number" step="0.01" name="vales_despensa"><br><br>

    <label for="compensaciones">Compensaciones:</label>
    <input type="number" step="0.01" name="compensaciones"><br><br>

    <label for="vacaciones">Vacaciones:</label>
    <input type="number" step="0.01" name="vacaciones"><br><br>

    <label for="prima_antiguedad">Prima de Antigüedad:</label>
    <input type="number" step="0.01" name="prima_antiguedad"><br><br>

    <!-- DEDUCCIONES -->
    <h2>Deducciones</h2>
    <label for="isr">ISR:</label>
    <input type="number" step="0.01" name="isr" required><br><br>

    <label for="imss">IMSS:</label>
    <input type="number" step="0.01" name="imss" required><br><br>

    <label for="caja_ahorro">Caja de Ahorro:</label>
    <input type="number" step="0.01" name="caja_ahorro" required><br><br>

    <label for="prestamos">Préstamos:</label>
    <input type="number" step="0.01" name="prestamos" required><br><br>

    <label for="infonavit">INFONAVIT:</label>
    <input type="number" step="0.01" name="infonavit" required><br><br>

    <label for="fonacot">FONACOT:</label>
    <input type="number" step="0.01" name="fonacot" required><br><br>

    <label for="cuota_sindical">Cuota Sindical:</label>
    <input type="number" step="0.01" name="cuota_sindical" required><br><br>

    <input type="submit" value="Registrar Nómina">
</form>
<script>
function actualizarSueldoBase() {
    const select = document.getElementById("id_persona");
    const sueldo = select.options[select.selectedIndex].getAttribute("data-sueldo");
    document.getElementById("sueldo_base").value = sueldo || "";
}
</script>



<a href="../Nomina.php" class="regresar">Regresar</a>
</body>
<?php include('../../Nav/footer.php'); ?>
</html>
