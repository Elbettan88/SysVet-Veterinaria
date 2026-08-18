<?php
require_once 'conexion.php';

$mensaje = "";

// ==========================================
// 1. PROCESAR: AGREGAR NUEVA MASCOTA (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $id_cliente       = (int)$_POST['id_cliente'];
    $id_raza          = (int)$_POST['id_raza'];
    $nombre           = trim($_POST['nombre']);
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $sexo             = $_POST['sexo'];
    $peso             = !empty($_POST['peso']) ? (float)$_POST['peso'] : 0.00;
    $color            = trim($_POST['color']);

    if (!empty($nombre) && $id_cliente > 0 && $id_raza > 0) {
        try {
            $stmtId = $pdo->query("SELECT MAX(id_mascota) as max_id FROM MASCOTA");
            $rowId = $stmtId->fetch();
            $nuevo_id = $rowId['max_id'] + 1;

            $sql = "INSERT INTO MASCOTA (id_mascota, id_cliente, id_raza, nombre, fecha_nacimiento, sexo, peso, color) 
                    VALUES (:id, :id_cliente, :id_raza, :nombre, :fecha_nac, :sexo, :peso, :color)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'         => $nuevo_id,
                ':id_cliente' => $id_cliente,
                ':id_raza'    => $id_raza,
                ':nombre'     => $nombre,
                ':fecha_nac'  => $fecha_nacimiento,
                ':sexo'       => $sexo,
                ':peso'       => $peso,
                ':color'      => $color
            ]);

            header("Location: mascotas.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "<div class='alerta-error'>Error al registrar: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alerta-error'>Por favor, complete todos los campos obligatorios.</div>";
    }
}

// ==========================================
// 2. PROCESAR: ELIMINAR MASCOTA (GET)
// ==========================================
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    try {
        $sql = "DELETE FROM MASCOTA WHERE id_mascota = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_eliminar]);
        
        header("Location: mascotas.php");
        exit;
    } catch (\PDOException $e) {
        $mensaje = "<div class='alerta-error'>No se puede eliminar: Esta mascota posee historial activo.</div>";
    }
}

// ==========================================
// 3. CONSULTAR DEPENDENCIAS (Selects)
// ==========================================
$clientes = [];
$razas = [];
try {
    $clientes = $pdo->query("SELECT id_cliente, nombres, apellidos FROM CLIENTE ORDER BY nombres ASC")->fetchAll();
    $razas    = $pdo->query("SELECT id_raza, nombre FROM RAZA ORDER BY nombre ASC")->fetchAll();
} catch (\PDOException $e) {
    $mensaje .= "<div class='alerta-error'>Error al cargar selectores: " . $e->getMessage() . "</div>";
}

// ==========================================
// 4. CONSULTAR LISTADO GLOBAL
// ==========================================
$mascotas = [];
try {
    $sql = "SELECT M.id_mascota, M.nombre AS mascota_nombre, M.color, M.peso, M.sexo,
                   R.nombre AS raza_nombre, 
                   C.nombres AS dueno_nombre, C.apellidos AS dueno_apellido
            FROM MASCOTA M
            LEFT JOIN RAZA R ON M.id_raza = R.id_raza
            LEFT JOIN CLIENTE C ON M.id_cliente = C.id_cliente
            ORDER BY M.id_mascota ASC";
    $stmt = $pdo->query($sql);
    $mascotas = $stmt->fetchAll();
} catch (\PDOException $e) {
    $mensaje .= "<div class='alerta-error'>Error al cargar listado: " . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SysVet - Administrar Mascotas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        
        /* Barra superior para agrupar link y botón */
        .top-action-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px; 
        }
        .btn-regresar { color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }
        
        /* Botón Guardar en barra superior */
        .btn-agregar { 
            background-color: #0d6efd; 
            color: #ffffff; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 6px; 
            font-size: 14px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: background 0.2s;
        }
        .btn-agregar:hover { background-color: #0b5ed7; }
        
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; text-align: center; }
        
        /* Formulario con grilla simétrica de 3 columnas sin interrupciones */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 6px; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #0d6efd; }
        
        .table-container { overflow-x: auto; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; font-size: 14px; border-bottom: 1px solid #edf2f7; }
        th { background-color: #34495e; color: #ffffff; font-weight: 600; }
        tr:hover { background-color: #f8fafd; }
        
        .mi-mascota { background-color: #e8f4fd; font-weight: bold; }
        .btn-editar { background-color: #d97706; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; margin-right: 5px; display: inline-block; }
        .btn-eliminar { background-color: #991b1b; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; }
        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; font-weight: 500; }
    </style>
    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Está seguro de eliminar esta mascota?")) {
                window.location.href = "mascotas.php?eliminar=" + id;
            }
        }
    </script>
</head>
<body>

    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">SysVet</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="mascotas.php" style="font-weight: bold;">Mascotas</a></li>
            <li><a href="especies.php">Especies</a></li>
            <li><a href="razas.php">Razas</a></li>
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <div class="container">
        
        <!-- BARRA SUPERIOR CON LINK Y BOTÓN -->
        <div class="top-action-bar">
            <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
            <!-- El atributo form="formMascota" lo conecta al formulario de abajo de forma remota -->
            <button type="submit" form="formMascota" class="btn-agregar">Guardar Mascota</button>
        </div>
        
        <?php echo $mensaje; ?>

        <!-- TARJETA BLANCA: FORMULARIO -->
        <div class="card-custom">
            <h2 class="card-title">Registrar Nueva Mascota</h2>
            
            <!-- Le asignamos el id="formMascota" para vincularlo al botón superior -->
            <form action="mascotas.php" method="POST" id="formMascota">
                <input type="hidden" name="accion" value="agregar">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre de la Mascota:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" autocomplete="off" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_cliente">Dueño / Cliente:</label>
                        <select name="id_cliente" id="id_cliente" class="form-control" required>
                            <option value="">-- Seleccione el dueño --</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?php echo $c['id_cliente']; ?>">
                                    <?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_raza">Raza:</label>
                        <select name="id_raza" id="id_raza" class="form-control" required>
                            <option value="">-- Seleccione la raza --</option>
                            <?php foreach ($razas as $r): ?>
                                <option value="<?php echo $r['id_raza']; ?>"><?php echo htmlspecialchars($r['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>