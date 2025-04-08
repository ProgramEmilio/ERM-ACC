<?php
include('../BD/ConexionBD.php');
include('../Nav/header.php');


$query = "
SELECT 
    n.id_nomina,
    p.rfc,
    per.sueldo_base,
    per.puntualidad,
    per.asistencia,
    per.bono,
    per.vales_despensa,
    per.compensaciones,
    per.vacaciones,
    per.prima_antiguedad,
    d.isr,
    d.imss,
    d.caja_ahorro,
    d.prestamos,
    d.infonavit,
    d.fonacot,
    d.cuota_sindical,
    n.dias_trabajados,
    n.dias_justificados,
    n.dias_total,
    n.periodo_inicio,
    n.periodo_final
FROM nomina n
JOIN persona p ON n.id_persona = p.id_persona
JOIN percepciones per ON n.id_percepcion = per.id_percepcion
JOIN deducciones d ON n.id_deducciones = d.id_deducciones
";

$resultado = $conn->query($query);
?>

<h1 class="titulo">Lista de Nóminas</h1>
<table class='tabla'>
    <tr>
        <th>RFC</th>
        <th>Periodo</th>
        <th>Días Trabajados</th>
        <th>Días Justificados</th>
        <th>Días Pagados</th>
        <th>Sueldo Base</th>
        <th>Puntualidad</th>
        <th>ISR</th>
        <th>IMSS</th>
        <th>Total a Pagar</th>
        <th>Acciones</th>
    </tr>

    <?php while ($row = $resultado->fetch_assoc()): 
        $total_percepciones = $row['sueldo_base'] + $row['puntualidad'] + $row['asistencia'] + $row['bono'] + $row['vales_despensa'] + $row['compensaciones'] + $row['vacaciones'] + $row['prima_antiguedad'];
        $total_deducciones = $row['isr'] + $row['imss'] + $row['caja_ahorro'] + $row['prestamos'] + $row['infonavit'] + $row['fonacot'] + $row['cuota_sindical'];
        $total_pagar = $total_percepciones - $total_deducciones;
    ?>
        <tr>
            <td><?= $row['rfc'] ?></td>
            <td><?= $row['periodo_inicio'] ?> a <?= $row['periodo_final'] ?></td>
            <td><?= $row['dias_trabajados'] ?></td>
            <td><?= $row['dias_justificados'] ?></td>
            <td><?= $row['dias_total'] ?></td>
            <td>$<?= number_format($row['sueldo_base'], 2) ?></td>
            <td>$<?= number_format($row['puntualidad'], 2) ?></td>
            <td>$<?= number_format($row['isr'], 2) ?></td>
            <td>$<?= number_format($row['imss'], 2) ?></td>
            <td><strong>$<?= number_format($total_pagar, 2) ?></strong></td>
            <td>
                <form method="post" action="GenerarPDF_Nomina.php" target="_blank">
                    <input type="hidden" name="id_nomina" value="<?= $row['id_nomina'] ?>">
                    <button type="submit">Calcular Nómina</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="../Usuario.php" class="regresar">Regresar</a>
<?php include('../Nav/footer.php'); ?>
