<?php
require_once 'conexion.php';

try {
    // Consultamos los veterinarios registrados en el sistema
    $sql = "SELECT id_veterinario, nombres, apellidos, colegiado, telefono, correo FROM VETERINARIO ORDER BY id_veterinario ASC";
    $stmt = $pdo->query($sql);
    $veterinarios = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar veterinarios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysVet - Veterinarios</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Barra de navegación */
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .navbar-menu a:hover { color: #bdc3c7; }

        /* Contenido */
        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; text-align: center; }
        .title { font-size: 36px; font-weight: bold; color: #2c3e50; margin-bottom: 30px; }
        
        /* Tabla de Datos */
        .table-container { background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e0e0e0; font-size: 15px; }
        th { background-color: #34495e; color: #ffffff; font-weight: 600; }
        tr:hover { background-color: #f8fafd; }
        
        .badge-colegiado { background-color: #2ecc71; color: white; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: bold; }
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
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <!-- CUERPO PRINCIPAL -->
    <div class="container">
        <h1 class="title">Personal Médico Veterinario</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>No. Colegiado</th>
                        <th>Teléfono</th>
                        <th>Correo Electrónico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($veterinarios)): ?>
                        <?php foreach ($veterinarios as $v): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($v['id_veterinario']); ?></td>
                                <td><strong><?php echo htmlspecialchars($v['nombres']); ?></strong></td>
                                <td><?php echo htmlspecialchars($v['apellidos']); ?></td>
                                <td><span class="badge-colegiado"><?php echo htmlspecialchars($v['colegiado']); ?></span></td>
                                <td><?php echo htmlspecialchars($v['telefono'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($v['correo'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No hay veterinarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
    </div>

</body>
</html>
