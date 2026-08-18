<?php
require_once 'conexion.php';

$mensaje = "";

// ==========================================
// 1. PROCESAR: AGREGAR NUEVA VACUNA (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre      = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if (!empty($nombre)) {
        try {
            // Calcular el siguiente ID correlativo secuencial
            $stmtId = $pdo->query("SELECT MAX(id_vacuna) as max_id FROM VACUNA");
            $rowId = $stmtId->fetch();
            $nuevo_id = $rowId['max_id'] + 1;

            $sql = "INSERT INTO VACUNA (id_vacuna, nombre, descripcion) VALUES (:id, :nombre, :descripcion)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'          => $nuevo_id,
                ':nombre'      => $nombre,
                ':descripcion' => $descripcion
            ]);

            header("Location: vacunas.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "<div class='alerta-error'>Error al registrar la vacuna: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alerta-error'>El nombre de la vacuna es obligatorio.</div>";
    }
}

// ==========================================
// 2. PROCESAR: ELIMINAR VACUNA (GET)
// ==========================================
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    try {
        $sql = "DELETE FROM VACUNA WHERE id_vacuna = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_eliminar]);
        
        header("Location: vacunas.php");
        exit;
    } catch (\PDOException $e) {
        $mensaje = "<div class='alerta-error'>No se puede eliminar: Esta vacuna ya ha sido aplicada a mascotas.</div>";
    }
}

// ==========================================
// 3. CONSULTAR: LISTADO GLOBAL DE VACUNAS
// ==========================================
$vacunas = [];
try {
    $sql = "SELECT id_vacuna, nombre, descripcion FROM VACUNA ORDER BY id_vacuna ASC";
    $stmt = $pdo->query($sql);
    $vacunas = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar vacunas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SysVet - Administrar Vacunas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        .navbar-menu a:hover { color: #bdc3c7; }
        
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        
        /* Barra de acciones superior */
        .top-action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-regresar { color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }
        
        /* Botón Guardar en la parte superior derecha */
        .btn-agregar { background-color: #0d6efd; color: #ffffff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-agregar:hover { background-color: #0b5ed7; }
        
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; text-align: center; }
        
        /* Cuadrícula para vacunas (Nombre y Descripción amplia) */
        .form-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 6px; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #0d6efd; }
        
        .table-container { overflow-x: auto; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; font-size: 14px; border-bottom: 1px solid #edf2f7; }
        th { background-color: #34495e; color: #ffffff; font-weight: 600; }
        tr:hover { background-color: #f8fafd; }
        
        .btn-editar { background-color: #d97706; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; margin-right: 5px; display: inline-block; }
        .btn-editar:hover { background-color: #b45309; }
        .btn-eliminar { background-color: #991b1b; color: #ffffff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; display: inline-block; }
        .btn-eliminar:hover { background-color: #7f1d1d; }
        
        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; font-weight: 500; }
    </style>
    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Está seguro de eliminar esta vacuna del catálogo?")) {
                window.location.href = "vacunas.php?eliminar=" + id;
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
            <li><a href="mascotas.php">Mascotas</a></li>
            <li><a href="vacunas.php" style="font-weight: bold;">Vacunas</a></li>
            <li><a href="especies.php">Especies</a></li>
            <li><a href="razas.php">Razas</a></li>
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <div class="container">
        
        <!-- BARRA SUPERIOR DE ACCIONES -->
        <div class="top-action-bar">
            <a href="index.php" class="btn-regresar">← Volver al Panel Principal</a>
            <button type="submit" form="formVacuna" class="btn-agregar">Guardar Vacuna</button>
        </div>
        
        <?php echo $mensaje; ?>

        <!-- TARJETA: FORMULARIO -->
        <div class="card-custom">
            <h2 class="card-title">Agregar Nueva Vacuna al Catálogo</h2>
            <form action="vacunas.php" method="POST" id="formVacuna">
                <input type="hidden" name="accion" value="agregar">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre de la Vacuna:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej. Parvovirus, Antirrábica" autocomplete="off" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción / Propósito:</label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Ej. Protección contra el virus de la rabia canina" autocomplete="off">
                    </div>
                </div>
            </form>
        </div>

        <!-- TARJETA: LISTADO -->
        <div class="card-custom">
            <h2 class="card-title">Catálogo de Vacunas Disponibles</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">No.</th>
                            <th style="width: 30%;">Nombre Vacuna</th>
                            <th style="width: 45%;">Descripción</th>
                            <th style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vacunas)): ?>
                            <?php foreach ($vacunas as $vac): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vac['id_vacuna']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($vac['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($vac['descripcion'] ?? 'Sin descripción'); ?></td>
                                    <td>
                                        <a href="editar_vacuna.php?id=<?php echo $vac['id_vacuna']; ?>" class="btn-editar">Editar</a>
                                        <button type="button" class="btn-eliminar" onclick="confirmarEliminacion(<?php echo $vac['id_vacuna']; ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No hay vacunas dadas de alta en el sistema.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
