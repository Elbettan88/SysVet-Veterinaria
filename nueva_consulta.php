<?php
require_once 'conexion.php';

$mensaje = "";

// ==========================================
// 1. CARGAR SELECTS DESDE LA BASE DE DATOS
// ==========================================
try {
    // Cargar las mascotas ordenadas por nombre
    $stmtMascotas = $pdo->query("SELECT id_mascota, nombre FROM MASCOTA ORDER BY nombre ASC");
    $mascotas = $stmtMascotas->fetchAll();

    // Cargar los veterinarios ordenados por nombre
    $stmtVets = $pdo->query("SELECT id_veterinario, nombres, apellidos FROM VETERINARIO ORDER BY nombres ASC");
    $veterinarios = $stmtVets->fetchAll();
} catch (\PDOException $e) {
    die("Error al cargar dependencias de la base de datos: " . $e->getMessage());
}

// ==========================================
// 2. PROCESAR EL FORMULARIO (MÉTODO POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
    $id_mascota     = (int)$_POST['id_mascota'];
    $id_veterinario = (int)$_POST['id_veterinario'];
    $fecha          = $_POST['fecha'];
    $hora           = $_POST['hora'];
    $motivo         = trim($_POST['motivo']);
    $diagnostico    = trim($_POST['diagnostico']);
    $observaciones  = trim($_POST['observaciones']);

    if ($id_mascota > 0 && $id_veterinario > 0 && !empty($motivo)) {
        try {
            // Calcular el siguiente ID de consulta correlativo
            $stmtId = $pdo->query("SELECT MAX(id_consulta) as max_id FROM CONSULTA");
            $rowId = $stmtId->fetch();
            $nuevo_id = $rowId['max_id'] + 1;

            // Inserción segura con sentencias preparadas
            $sql = "INSERT INTO CONSULTA (id_consulta, id_mascota, id_veterinario, fecha, hora, motivo, diagnostico, observaciones) 
                    VALUES (:id, :id_mascota, :id_vet, :fecha, :hora, :motivo, :diagnostico, :observaciones)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id'            => $nuevo_id,
                ':id_mascota'    => $id_mascota,
                ':id_vet'        => $id_veterinario,
                ':fecha'         => $fecha,
                ':hora'          => $hora,
                ':motivo'        => $motivo,
                ':diagnostico'   => $diagnostico,
                ':observaciones' => $observaciones
            ]);

            // Redireccionar al historial de consultas para ver el nuevo registro
            header("Location: consulta.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "<div class='alerta-error'>Error al registrar la consulta: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alerta-error'>Por favor, complete todos los campos obligatorios.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SysVet - Nueva Consulta</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }
        
        /* Barra superior */
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; text-decoration: none; }
        .navbar-menu { display: flex; list-style: none; gap: 25px; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 16px; }
        
        .container { max-width: 650px; margin: 40px auto; padding: 0 20px; }
        
        /* Enlace regresar */
        .btn-regresar { display: inline-block; margin-bottom: 25px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }
        
        /* Tarjeta blanca */
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 25px; text-align: center; }
        
        /* Formulario */
        .form-group { margin-bottom: 18px; }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px; font-size: 14px; }
        
        .form-control { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #0d6efd; }
        
        textarea.form-control { resize: vertical; }
        
        /* Botón Guardar Azul */
        .btn-guardar { background-color: #0d6efd; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; display: block; width: 100%; margin-top: 10px; }
        .btn-guardar:hover { background-color: #0b5ed7; }
        
        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; font-weight: 500; }
    </style>
</head>
<body>

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

    <div class="container">
        <a href="consulta.php" class="btn-regresar">← Volver al Historial</a>

        <?php echo $mensaje; ?>

        <div class="card-custom">
            <h2 class="card-title">Registrar Nueva Consulta</h2>
            
            <form action="nueva_consulta.php" method="POST">
                <input type="hidden" name="accion" value="guardar">
                
                <!-- Selector de Mascota -->
                <div class="form-group">
                    <label for="id_mascota">Paciente (Mascota):</label>
                    <select name="id_mascota" id="id_mascota" class="form-control" required>
                        <option value="">-- Seleccione una mascota --</option>
                        <?php foreach ($mascotas as $m): ?>
                            <option value="<?php echo $m['id_mascota']; ?>"><?php echo htmlspecialchars($m['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Selector de Veterinario -->
                <div class="form-group">
                    <label for="id_veterinario">Médico Veterinario:</label>
                    <select name="id_veterinario" id="id_veterinario" class="form-control" required>
                        <option value="">-- Seleccione el médico tratante --</option>
                        <?php foreach ($veterinarios as $v): ?>
                            <option value="<?php echo $v['id_veterinario']; ?>">
                                <?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apellidos']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fecha y Hora combinadas en línea -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hora">Hora:</label>
                        <input type="time" name="hora" id="hora" class="form-control" value="<?php echo date('H:i'); ?>" required>
                    </div>
                </div>

                <!-- Motivo -->
                <div class="form-group">
                    <label for="motivo">Motivo de la Consulta:</label>
                    <input type="text" name="motivo" id="motivo" class="form-control" placeholder="Ej. Control de peso, Vómitos, Vacunación" autocomplete="off" required>
                </div>

                <!-- Diagnóstico -->
                <div class="form-group">
                    <label for="diagnostico">Diagnóstico Preliminar:</label>
                    <textarea name="diagnostico" id="diagnostico" class="form-control" rows="2" placeholder="Describa el diagnóstico observado..."></textarea>
                </div>

                <!-- Observaciones -->
                <div class="form-group">
                    <label for="observaciones">Observaciones / Tratamiento:</label>
                    <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Ej. Medicamento recetado, dieta blanda..."></textarea>
                </div>

                <button type="submit" class="btn-guardar">Guardar Registro Clínico</button>
            </form>
        </div>
    </div>

</body>
</html>
