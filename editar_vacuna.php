<?php
require_once 'conexion.php';

$mensaje = "";
$vacuna = null;

// ==========================================
// 1. CARGAR DATOS DE LA VACUNA (MÉTODO GET)
// ==========================================
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT id_vacuna, nombre, descripcion FROM VACUNA WHERE id_vacuna = :id");
        $stmt->execute([':id' => $id]);
        $vacuna = $stmt->fetch();
        
        if (!$vacuna) { 
            die("La vacuna seleccionada no existe en el catálogo."); 
        }
    } catch (\PDOException $e) {
        die("Error al buscar la vacuna: " . $e->getMessage());
    }
} else {
    header("Location: vacunas.php");
    exit;
}

// ==========================================
// 2. PROCESAR ACTUALIZACIÓN (MÉTODO POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $nombre        = trim($_POST['nombre']);
    $descripcion   = trim($_POST['descripcion']);
    $id_actualizar = (int)$_POST['id_vacuna'];
    
    if (!empty($nombre)) {
        try {
            $sql = "UPDATE VACUNA SET nombre = :nombre, descripcion = :descripcion WHERE id_vacuna = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre'      => $nombre,
                ':descripcion' => $descripcion,
                ':id'          => $id_actualizar
            ]);
            
            // Redireccionar al catálogo principal para ver los cambios
            header("Location: vacunas.php");
            exit;
        } catch (\PDOException $e) {
            $mensaje = "<div class='alerta-error'>Error al actualizar la vacuna: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alerta-error'>El nombre de la vacuna es obligatorio.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysVet - Editar Vacuna</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }
        
        /* Barra de navegación responsiva con logo */
        .navbar { background-color: #2c3e50; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1000; }
        .navbar-brand-container { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .navbar-logo { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; }
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; }
        .navbar-menu { display: flex; list-style: none; gap: 20px; align-items: center; }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 500; }
        .navbar-menu a:hover { color: #bdc3c7; }

        .menu-toggle { display: none; flex-direction: column; gap: 5px; cursor: pointer; background: none; border: none; padding: 5px; }
        .menu-toggle .bar { width: 25px; height: 3px; background-color: #ffffff; border-radius: 2px; transition: 0.3s; }

        .container { max-width: 650px; margin: 40px auto; padding: 0 20px; }
        
        /* Enlace de regreso */
        .btn-regresar { display: inline-block; margin-bottom: 25px; color: #0d6efd; text-decoration: none; font-weight: 500; }
        .btn-regresar:hover { text-decoration: underline; }

        /* Tarjeta de edición */
        .card-custom { background-color: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .card-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 20px; text-align: center; }

        label { display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; background-color: #f8fafc; margin-bottom: 20px; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #0d6efd; }
        
        /* Botón Guardar Naranja */
        .btn-guardar { background-color: #d97706; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; display: block; margin-left: auto; transition: background 0.2s; }
        .btn-guardar:hover { background-color: #b45309; }
        .alerta-error { background-color: #fde8e8; color: #9b1c1c; padding: 12px; border-radius: 6px; margin-bottom: 25px; text-align: center; }

        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .menu-toggle { display: flex; }
            .navbar-menu { display: none; flex-direction: column; width: 100%; background-color: #2c3e50; position: absolute; top: 100%; left: 0; padding: 20px; gap: 15px; border-top: 1px solid #34495e; }
            .navbar-menu.active { display: flex; }
            .menu-toggle.open .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
            .menu-toggle.open .bar:nth-child(2) { opacity: 0; }
            .menu-toggle.open .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
        }
    </style>
</head>
<body>

    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand-container">
            <img src="gato_azul.jpg" alt="Logo Gato" class="navbar-logo">
            <span class="navbar-brand">SysVet</span>
        </a>
        
        <button class="menu-toggle" id="mobile-menu" aria-label="Abrir menú">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>

        <ul class="navbar-menu" id="nav-list">
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
        <a href="vacunas.php" class="btn-regresar">← Volver al Catálogo</a>

        <?php echo $mensaje; ?>

        <div class="card-custom">
            <h2 class="card-title">Modificar Vacuna (ID: <?php echo $vacuna['id_vacuna']; ?>)</h2>
            <form action="editar_vacuna.php" method="POST">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id_vacuna" value="<?php echo $vacuna['id_vacuna']; ?>">
                
                <label for="nombre">Nombre de la Vacuna:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo htmlspecialchars($vacuna['nombre']); ?>" required autocomplete="off">
                
                <label for="descripcion">Descripción / Propósito:</label>
                <input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo htmlspecialchars($vacuna['descripcion'] ?? ''); ?>" autocomplete="off">
                
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        const mobileMenu = document.getElementById('mobile-menu');
        const navList = document.getElementById('nav-list');
        mobileMenu.addEventListener('click', () => {
            navList.classList.toggle('active');
            mobileMenu.classList.toggle('open');
        });
    </script>
</body>
</html>

