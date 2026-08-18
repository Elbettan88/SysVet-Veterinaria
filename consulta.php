<?php
require_once 'conexion.php';

try {
    // Consulta avanzada uniendo la Consulta con la Mascota y el Veterinario que la atendió
    $sql = "SELECT C.id_consulta, C.fecha, C.motivo, C.diagnostico, 
                   M.nombre AS mascota_nombre, 
                   V.nombres AS vet_nombre, V.apellidos AS vet_apellido
            FROM CONSULTA C
            INNER JOIN MASCOTA M ON C.id_mascota = M.id_mascota
            INNER JOIN VETERINARIO V ON C.id_veterinario = V.id_veterinario
            ORDER BY C.fecha DESC, C.id_consulta DESC";
            
    $stmt = $pdo->query($sql);
    $consultas = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar el historial médico: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysVet - Consultas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .navbar-menu a:hover { color: #bdc3c7; }

        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }
        .title { font-size: 36px; font-weight: bold; color: #2c3e50; margin-bottom: 30px; text-align: center; }
        
        /* Contenedor de herramientas superior alineado a la izquierda */
        .tools-container { text-align: left; margin-bottom: 15px; }
        
        /* Botón de acción azul */
        .btn-nueva-consulta { 
            background-color: #0d6efd; 
            color: #ffffff; 
            text-decoration: none; 
            padding: 10px 20px; 
            border-radius: 6px; 
            display: inline-block; 
            font-weight: 600; 
            font-size: 14px; 
            transition: background 0.2s;
        }
        .btn-nueva-consulta:hover { background-color: #0b5ed7; }
        
        .table-container { background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e0e0e0; font-size: 15px; }
        th { background-color: #34495e; color: #ffffff; font-weight: 600; }
        tr:hover { background-color: #f8fafd; }
        
        .badge-motivo { background-color: #e2e8f0; color: #4a5568; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 500; }
        
        .footer-container { text-align: center; }
        .btn-regresar { display: inline-block; margin-top: 25px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">SysVet</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="especies.php">Especies</a></li>
            <li><a href="razas.php">Razas</a></li>
            <li><a href="consulta.php" style="font-weight: bold;">Consulta</a></li>
        </ul>
    </nav>

    <!-- CUERPO PRINCIPAL -->
    <div class="container">
        <h1 class="title">Historial de Consultas Médicas</h1>

        <!-- BOTÓN ACCESO EN LÍNEA SUPERIOR -->
        <div class="tools-container">
            <a href="nueva_consulta.php" class="btn-nueva-consulta">+ Registrar Consulta Nueva</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Paciente / Mascota</th>
                        <th>Médico Veterinario</th>
                        <th>Motivo de la Visita</th>
                        <th>Diagnóstico preliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($consultas)): ?>
                        <?php foreach ($consultas as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['id_consulta']); ?></td>
                                <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($c['fecha']))); ?></td>
                                <td><strong><?php echo htmlspecialchars($c['mascota_nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($c['vet_nombre'] . ' ' . $c['vet_apellido']); ?></td>
                                <td><span class="badge-motivo"><?php echo htmlspecialchars($c['motivo']); ?></span></td>
                                <td><?php echo htmlspecialchars($c['diagnostico'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No hay consultas médicas registradas en este momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-container">
            <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
        </div>
    </div>

</body>
</html>
