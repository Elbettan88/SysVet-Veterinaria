<?php
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysVet - Sistema de Gestión</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #d1f0ff; font-family: 'Segoe UI', sans-serif; }

        /* Barra de navegación fluida */
        .navbar { 
            background-color: #2c3e50; 
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: relative;
            z-index: 1000;
        }
        
        .navbar-brand-container { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        
        .navbar-logo { 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid #ffffff; 
        }
        
        .navbar-brand { color: #ffffff; font-size: 24px; font-weight: bold; }
        
        .navbar-menu { 
            display: flex; 
            list-style: none; 
            gap: 20px; 
            align-items: center; 
        }
        .navbar-menu a { color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 500; }
        .navbar-menu a:hover { color: #bdc3c7; }

        /* Botón de Hamburguesa (Oculto por defecto en computadoras) */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 5px;
        }
        .menu-toggle .bar {
            width: 25px;
            height: 3px;
            background-color: #ffffff;
            border-radius: 2px;
            transition: 0.3s;
        }

        /* Contenido principal auto-ajustable */
        .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; text-align: center; }
        .title { font-size: 42px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; transition: font-size 0.2s; }
        .subtitle { color: #7f8c8d; font-size: 18px; margin-bottom: 50px; }

        /* Cuadrícula de Tarjetas Inteligente */
        .cards-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 20px; 
            justify-content: center; 
            max-width: 1100px;
            margin: 0 auto;
        }
        
        .card-custom { 
            background-color: #ffffff; 
            border-radius: 15px; 
            padding: 25px 20px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            align-items: center; 
            min-height: 240px; 
        }
        .card-title { font-size: 20px; color: #2c3e50; font-weight: bold; margin-bottom: 10px; }
        .card-text { color: #7f8c8d; font-size: 13px; line-height: 1.4; margin-bottom: 15px; }
        
        .btn-custom { 
            background-color: #0d6efd; 
            color: #ffffff; 
            text-decoration: none; 
            padding: 10px 0; 
            border-radius: 6px; 
            font-weight: 500; 
            width: 100%; 
            display: block; 
            text-align: center; 
            font-size: 14px; 
        }
        .btn-custom:hover { background-color: #0b5ed7; }

        /* =========================================================
           REGLAS DE ADAPTACIÓN PARA DISPOSITIVOS MÓVILES (RESPONSIVE)
           ========================================================= */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            /* Muestra el icono de la hamburguesa */
            .menu-toggle {
                display: flex;
            }

            /* Transforma el menú en una persiana desplegable oculta */
            .navbar-menu { 
                display: none; 
                flex-direction: column; 
                width: 100%;
                background-color: #2c3e50;
                position: absolute;
                top: 100%;
                left: 0;
                padding: 20px;
                gap: 15px;
                border-top: 1px solid #34495e;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }

            /* Clase dinámica que activará JavaScript para mostrar el menú */
            .navbar-menu.active {
                display: flex;
            }

            /* Animación opcional de la hamburguesa transformándose en X */
            .menu-toggle.open .bar:nth-child(1) {
                transform: translateY(8px) rotate(45deg);
            }
            .menu-toggle.open .bar:nth-child(2) {
                opacity: 0;
            }
            .menu-toggle.open .bar:nth-child(3) {
                transform: translateY(-8px) rotate(-45deg);
            }

            .title { 
                font-size: 32px; 
            }
            .subtitle {
                font-size: 16px;
                margin-bottom: 35px;
            }
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
        
        <!-- BOTÓN HAMBURGUESA -->
        <button class="menu-toggle" id="mobile-menu" aria-label="Abrir menú">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>

        <ul class="navbar-menu" id="nav-list">
            <li><a href="index.php" style="font-weight: bold;">Inicio</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="mascotas.php">Mascotas</a></li>
            <li><a href="vacunas.php">Vacunas</a></li>
            <li><a href="especies.php">Especies</a></li>
            <li><a href="razas.php">Razas</a></li>
            <li><a href="consulta.php">Consulta</a></li>
        </ul>
    </nav>

    <!-- CUERPO PRINCIPAL -->
    <div class="container">
        <h1 class="title">Sistema de Gestión</h1>
        <p class="subtitle">Bienvenido al sistema. Seleccione una opción para administrar la información.</p>

        <div class="cards-grid">
            <!-- Tarjeta Clientes -->
            <div class="card-custom">
                <div>
                    <h2 class="card-title">Clientes</h2>
                    <p class="card-text">Administrar información de los clientes registrados en el sistema.</p>
                </div>
                <a href="clientes.php" class="btn-custom">Administrar clientes</a>
            </div>

            <!-- Tarjeta Mascotas -->
            <div class="card-custom">
                <div>
                    <h2 class="card-title">Mascotas</h2>
                    <p class="card-text">Administrar expedientes de los pacientes, razas y dueños.</p>
                </div>
                <a href="mascotas.php" class="btn-custom">Administrar mascotas</a>
            </div>

            <!-- Tarjeta Vacunas -->
            <div class="card-custom">
                <div>
                    <h2 class="card-title">Vacunas</h2>
                    <p class="card-text">Administrar el catálogo de vacunas disponibles en la clínica.</p>
                </div>
                <a href="vacunas.php" class="btn-custom">Administrar vacunas</a>
            </div>

            <!-- Tarjeta Especies -->
            <div class="card-custom">
                <div>
                    <h2 class="card-title">Especies</h2>
                    <p class="card-text">Administrar las clasificaciones de especies biológicas.</p>
                </div>
                <a href="especies.php" class="btn-custom">Administrar especies</a>
            </div>

            <!-- Tarjeta Razas -->
            <div class="card-custom">
                <div>
                    <h2 class="card-title">Razas</h2>
                    <p class="card-text">Administrar el listado de razas correspondientes a cada especie.</p>
                </div>
                <a href="razas.php" class="btn-custom">Administrar razas</a>
            </div>
        </div>
    </div>

    <!-- SCRIPT DE INTERACCIÓN DEL MENÚ -->
    <script>
        const mobileMenu = document.getElementById('mobile-menu');
        const navList = document.getElementById('nav-list');

        mobileMenu.addEventListener('click', () => {
            // Activa o desactiva la visualización de la lista de pestañas
            navList.classList.toggle('active');
            // Activa o desactiva la animación visual de las tres barritas (se vuelven una X)
            mobileMenu.classList.toggle('open');
        });
    </script>
</body>
</html>
