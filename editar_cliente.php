<?php
require_once 'conexion.php';

$mensaje = "";
$cliente = null;

// 1. CARGAR DATOS DEL CLIENTE SELECCIONADO (GET)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT id_cliente, nombres, apellidos, telefono, correo, direccion FROM CLIENTE WHERE id_cliente = :id");
        $stmt->execute([':id' => $id]);
        $cliente = $stmt->fetch();
        
        if (!$cliente) { 
            die("El cliente seleccionado no existe."); 
        }
    } catch (\PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: clientes.php");
    exit;
}

// 2. PROCESAR ACTUALIZACIÓN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $nombres   = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $telefono  = trim($_POST['telefono']);
    $correo    = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $id_actualizar = (int)$_POST['id_cliente'];
    
    if (!empty($nombres) && !empty($apellidos) && !empty($telefono)) {
        try {
            $sql = "UPDATE CLIENTE SET nombres = :nombres, apellidos = :apellidos, telefono = :telefono, correo = :correo, direccion = :direccion 
                    WHERE id_cliente = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombres'   => $nombres,
                ':apellidos' => $apellidos,
                ':telefono'  => $telefono,
                ':correo'    => $correo,
                ':direccion' => $direccion,
                ':id'        => $id_actualizar
            ]);
            
            header("Location: clientes.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "Error al actualizar el cliente: " . $e->getMessage();
        }
    } else {
        $mensaje = "Los campos Nombres, Apellidos y Teléfono son requeridos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SysVet - Editar Cliente</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .btn-regresar { display: inline-block; margin-bottom: 25px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .form-group-full { grid-column: span 2; }
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; font-family: inherit; }
        .btn-guardar { background-color: #d97706; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; display: block; margin-left: auto; }
        .btn-guardar:hover { background-color: #b45309; }
        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; }
    </style>
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
        <a href="clientes.php" class="btn-regresar">← Volver al Listado</a>

        <?php if (!empty($mensaje)): ?>
            <div class="alerta-error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="card-custom">
            <h2 class="card-title">Modificar Cliente (ID: <?php echo $cliente['id_cliente']; ?>)</h2>
            <form action="editar_cliente.php" method="POST">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombres">Nombres:</label>
                        <input type="text" name="nombres" id="nombres" class="form-control" value="<?php echo htmlspecialchars($cliente['nombres']); ?>" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" value="<?php echo htmlspecialchars($cliente['apellidos']); ?>" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" name="correo" id="correo" class="form-control" value="<?php echo htmlspecialchars($cliente['correo']); ?>" autocomplete="off">
                    </div>
                    <div class="form-group form-group-full">
                        <label for="direccion">Dirección:</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" autocomplete="off">
                    </div>
                </div>
                
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
            </form>
        </div>
    </div>

</body>
</html>
