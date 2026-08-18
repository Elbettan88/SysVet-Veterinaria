<?php
require_once 'conexion.php';

$mensaje = "";

// 1. PROCESAR: AGREGAR NUEVO CLIENTE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombres   = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $telefono  = trim($_POST['telefono']);
    $correo    = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    
    if (!empty($nombres) && !empty($apellidos) && !empty($telefono)) {
        try {
            $stmtId = $pdo->query("SELECT MAX(id_cliente) as max_id FROM CLIENTE");
            $rowId = $stmtId->fetch();
            $nuevo_id = $rowId['max_id'] + 1;

            $sql = "INSERT INTO CLIENTE (id_cliente, nombres, apellidos, telefono, correo, direccion) 
                    VALUES (:id, :nombres, :apellidos, :telefono, :correo, :direccion)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'        => $nuevo_id,
                ':nombres'   => $nombres,
                ':apellidos' => $apellidos,
                ':telefono'  => $telefono,
                ':correo'    => $correo,
                ':direccion' => $direccion
            ]);
            
            header("Location: clientes.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "Error al agregar el cliente: " . $e->getMessage();
        }
    } else {
        $mensaje = "Los campos Nombres, Apellidos y Teléfono son obligatorios.";
    }
}

// 2. PROCESAR: ELIMINAR CLIENTE (GET)
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    try {
        $sql = "DELETE FROM CLIENTE WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_eliminar]);
        
        header("Location: clientes.php");
        exit;
    } catch (\PDOException $e) {
        $mensaje = "No se puede eliminar: Este cliente tiene mascotas asociadas en la veterinaria.";
    }
}

// 3. CONSULTAR: LISTADO DE CLIENTES
try {
    $sql = "SELECT id_cliente, nombres, apellidos, telefono, correo, direccion FROM CLIENTE ORDER BY id_cliente ASC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SysVet - Administrar Clientes</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .btn-regresar { display: inline-block; margin-bottom: 25px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; text-align: center; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .form-group-full { grid-column: span 2; }
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 6px; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; }
        .btn-container { text-align: right; }
        .btn-agregar { background-color: #0d6efd; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; font-size: 14px; border-bottom: 1px solid #edf2f7; }
        th { background-color: #34495e; color: #ffffff; }
        .mi-registro { background-color: #e8f4fd; font-weight: bold; }
        .btn-editar { background-color: #d97706; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; margin-right: 5px; }
        .btn-eliminar { background-color: #991b1b; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; }
        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; }
    </style>
    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Está seguro de eliminar este cliente?")) {
                window.location.href = "clientes.php?eliminar=" + id;
            }
        }
    </script>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">SysVet</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="clientes.php" style="font-weight: bold;">Clientes</a></li>
            <li><a href="especies.php">Especies</a></li>
            <li><a href="razas.php">Razas</a></li>
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <div class="container">
        <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alerta-error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="card-custom">
            <h2 class="card-title">Registrar Nuevo Cliente</h2>
            <form action="clientes.php" method="POST">
                <input type="hidden" name="accion" value="agregar">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombres">Nombres:</label>
                        <input type="text" name="nombres" id="nombres" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" name="correo" id="correo" class="form-control" autocomplete="off">
                    </div>
                    <div class="form-group form-group-full">
                        <label for="direccion">Dirección:</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" autocomplete="off">
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-agregar">Guardar Cliente</button>
                </div>
            </form>
        </div>

        <div class="card-custom">
            <h2 class="card-title">Lista de Clientes Registrados</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                            <tr class="<?php echo ($c['id_cliente'] == 6) ? 'mi-registro' : ''; ?>">
                                <td><?php echo htmlspecialchars($c['id_cliente']); ?></td>
                                <td><?php echo htmlspecialchars($c['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($c['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($c['correo']); ?></td>
                                <td><?php echo htmlspecialchars($c['direccion']); ?></td>
                                <td>
                                    <a href="editar_cliente.php?id=<?php echo $c['id_cliente']; ?>" class="btn-editar">Editar</a>
                                    <button type="button" class="btn-eliminar" onclick="confirmarEliminacion(<?php echo $c['id_cliente']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
