<?php
require_once 'conexion.php';

$mensaje = "";

// ==========================================
// 1. PROCESAR: AGREGAR NUEVA ESPECIE (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = trim($_POST['nombre']);
    
    if (!empty($nombre)) {
        try {
            $stmtId = $pdo->query("SELECT MAX(id_especie) as max_id FROM ESPECIE");
            $rowId = $stmtId->fetch();
            $nuevo_id = $rowId['max_id'] + 1;

            $sql = "INSERT INTO ESPECIE (id_especie, nombre) VALUES (:id, :nombre)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $nuevo_id, ':nombre' => $nombre]);
            
            header("Location: especies.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "Error al agregar la especie: " . $e->getMessage();
        }
    }
}

// ==========================================
// 2. PROCESAR: ELIMINAR ESPECIE (GET)
// ==========================================
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    try {
        $sql = "DELETE FROM ESPECIE WHERE id_especie = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_eliminar]);
        
        header("Location: especies.php");
        exit;
    } catch (\PDOException $e) {
        $mensaje = "No se puede eliminar: Esta especie tiene razas asociadas en el sistema.";
    }
}

// ==========================================
// 3. CONSULTAR: LISTA DE ESPECIES
// ==========================================
try {
    $sql = "SELECT id_especie, nombre FROM ESPECIE ORDER BY id_especie ASC";
    $stmt = $pdo->query($sql);
    $especies = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysVet - Administrar Especies</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background-color: #d1f0ff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        
        /* Barra de navegación SysVet */
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .navbar-menu a:hover { color: #bdc3c7; }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        
        /* Enlace de regreso idéntico a las páginas anteriores */
        .btn-regresar { 
            display: inline-block; 
            margin-bottom: 25px; 
            color: #0d6efd; 
            text-decoration: none; 
            font-weight: 500; 
        }
        .btn-regresar:hover { 
            text-decoration: underline; 
        }

        /* Tarjetas Blancas Independientes */
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 12px; 
            padding: 30px; 
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .card-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Formulario */
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px; font-size: 14px; }
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            font-size: 15px;
            background-color: #f8fafc;
            margin-bottom: 15px;
            font-family: inherit;
        }
        .form-control:focus { outline: none; border-color: #0d6efd; }
        
        /* Botones de acción */
        .btn-agregar {
            background-color: #0d6efd; 
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-agregar:hover { background-color: #0b5ed7; }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; font-size: 15px; vertical-align: middle; }
        th { background-color: #34495e; color: #ffffff; font-weight: 600; }
        tr { border-bottom: 1px solid #edf2f7; }
        tr:hover { background-color: #f8fafd; }
        
        .btn-editar {
            background-color: #d97706; 
            color: #ffffff;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-right: 5px;
            display: inline-block;
            font-weight: 500;
        }
        .btn-editar:hover { background-color: #b45309; }

        .btn-eliminar {
            background-color: #991b1b; 
            color: #ffffff;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            cursor: pointer;
            border: none;
            font-weight: 500;
        }
        .btn-eliminar:hover { background-color: #7f1d1d; }

        .alerta-error {
            background-color: #fde8e8;
            color: #9b1c1c;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-weight: 500;
            text-align: center;
        }
    </style>

    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Está seguro de eliminar esta especie?")) {
                window.location.href = "especies.php?eliminar=" + id;
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
            <li><a href="especies.php" style="font-weight: bold;">Especies</a></li>
            <li><a href="razas.php">Razas</a></li>
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <div class="container">
        
        <!-- ENLACE DE REGRESO ESTILIZADO -->
        <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alerta-error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- TARJETA: AGREGAR NUEVA ESPECIE -->
        <div class="card-custom">
            <h2 class="card-title">Agregar nueva especie</h2>
            <form action="especies.php" method="POST">
                <input type="hidden" name="accion" value="agregar">
                
                <label for="nombre">Nombre de la Especie:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej. Ave, Reptil" autocomplete="off" required>
                
                <button type="submit" class="btn-agregar">Agregar Especie</button>
            </form>
        </div>

        <!-- TARJETA: LISTADO DE ESPECIES -->
        <div class="card-custom">
            <h2 class="card-title">Lista de Especies</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">No.</th>
                        <th style="width: 55%;">Nombre</th>
                        <th style="width: 30%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($especies as $e): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($e['id_especie']); ?></td>
                            <td><strong><?php echo htmlspecialchars($e['nombre']); ?></strong></td>
                            <td>
                                <a href="editar_especie.php?id=<?php echo $e['id_especie']; ?>" class="btn-editar">Editar</a>
                                <button type="button" class="btn-eliminar" onclick="confirmarEliminacion(<?php echo $e['id_especie']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
