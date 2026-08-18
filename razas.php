<?php
require_once 'conexion.php';

$mensaje = "";

// ==========================================
// 1. PROCESAR: AGREGAR NUEVA RAZA (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = trim($_POST['nombre']);
    $id_especie = (int)$_POST['id_especie'];
    
    if (!empty($nombre) && $id_especie > 0) {
        try {
            // Obtener el siguiente ID secuencial para la raza
            $stmtId = $pdo->query("SELECT MAX(id_raza) as max_id FROM RAZA");
            $rowId = $stmtId->fetch();
            $nuevo_id = $rowId['max_id'] + 1;

            $sql = "INSERT INTO RAZA (id_raza, id_especie, nombre) VALUES (:id, :id_esp, :nombre)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $nuevo_id, ':id_esp' => $id_especie, ':nombre' => $nombre]);
            
            header("Location: razas.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "Error al agregar la raza: " . $e->getMessage();
        }
    }
}

// ==========================================
// 2. PROCESAR: ELIMINAR RAZA (GET)
// ==========================================
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    try {
        $sql = "DELETE FROM RAZA WHERE id_raza = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_eliminar]);
        
        header("Location: razas.php");
        exit;
    } catch (\PDOException $e) {
        $mensaje = "No se puede eliminar: Esta raza está asignada a una mascota registrada.";
    }
}

// ==========================================
// 3. CONSULTAR: LISTADO DE ESPECIES (Para el selector)
// ==========================================
try {
    $stmtEsp = $pdo->query("SELECT id_especie, nombre FROM ESPECIE ORDER BY nombre ASC");
    $especies = $stmtEsp->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar especies: " . $e->getMessage());
}

// ==========================================
// 4. CONSULTAR: LISTADO DE RAZAS CON SU ESPECIE
// ==========================================
try {
    $sql = "SELECT R.id_raza, R.nombre AS raza_nombre, E.nombre AS especie_nombre 
            FROM RAZA R
            INNER JOIN ESPECIE E ON R.id_especie = E.id_especie
            ORDER BY R.id_raza ASC";
    $stmt = $pdo->query($sql);
    $razas = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysVet - Administrar Razas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Barra de navegación SysVet */
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .navbar-menu a:hover { color: #bdc3c7; }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        
        /* Enlace regresar */
        .btn-regresar { display: inline-block; margin-bottom: 25px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }

        /* Tarjetas */
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; }

        /* Formulario */
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; margin-bottom: 15px; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #0d6efd; }
        
        .btn-agregar { background-color: #0d6efd; color: #ffffff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .btn-agregar:hover { background-color: #0b5ed7; }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; font-size: 15px; vertical-align: middle; }
        th { background-color: #34495e; color: #ffffff; font-weight: 600; }
        tr { border-bottom: 1px solid #edf2f7; }
        tr:hover { background-color: #f8fafd; }
        
        /* Botones acciones */
        .btn-editar { background-color: #d97706; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 14px; margin-right: 5px; display: inline-block; font-weight: 500; }
        .btn-editar:hover { background-color: #b45309; }

        .btn-eliminar { background-color: #991b1b; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 14px; display: inline-block; cursor: pointer; border: none; font-weight: 500; }
        .btn-eliminar:hover { background-color: #7f1d1d; }

        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; font-weight: 500; }
    </style>

    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Está seguro de eliminar esta raza?")) {
                window.location.href = "razas.php?eliminar=" + id;
            }
        }
    </script>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">SysVet</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="especies.php">Especies</a></li>
            <li><a href="razas.php" style="font-weight: bold;">Razas</a></li>
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <div class="container">
        
        <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alerta-error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- TARJETA: AGREGAR NUEVA RAZA -->
        <div class="card-custom">
            <h2 class="card-title">Agregar nueva raza</h2>
            <form action="razas.php" method="POST">
                <input type="hidden" name="accion" value="agregar">
                
                <label for="nombre">Nombre de la Raza:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej. Pitbull, Persa" autocomplete="off" required>
                
                <label for="id_especie">Asociar a Especie:</label>
                <select name="id_especie" id="id_especie" class="form-control" required>
                    <option value="">-- Seleccione una especie --</option>
                    <?php foreach ($especies as $esp): ?>
                        <option value="<?php echo $esp['id_especie']; ?>"><?php echo htmlspecialchars($esp['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn-agregar">Agregar Raza</button>
            </form>
        </div>

        <!-- TARJETA: LISTA DE RAZAS -->
        <div class="card-custom">
            <h2 class="card-title">Lista de Razas</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">No.</th>
                        <th style="width: 35%;">Raza</th>
                        <th style="width: 25%;">Especie</th>
                        <th style="width: 25%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($razas as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['id_raza']); ?></td>
                            <td><strong><?php echo htmlspecialchars($r['raza_nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['especie_nombre']); ?></td>
                            <td>
                                <a href="editar_raza.php?id=<?php echo $r['id_raza']; ?>" class="btn-editar">Editar</a>
                                <button type="button" class="btn-eliminar" onclick="confirmarEliminacion(<?php echo $r['id_raza']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
