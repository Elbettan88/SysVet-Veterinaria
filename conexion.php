<?php
// Configuración de credenciales para XAMPP
$host = 'localhost';
$db   = 'veterinaria'; // Nombre de tu base de datos en Workbench
$user = 'php_veterinaria';
$pass = 'clave123';
$charset = 'utf8mb4';            // Permite eñes, acentos y caracteres especiales

// Construcción del DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de seguridad y rendimiento para PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve los datos en arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación para mayor seguridad contra SQL Injection
];

try {
    // Intentar establecer la conexión
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // NOTA: Una vez que confirmes que funciona, puedes comentar o borrar la línea de abajo 
    // para que no interfiera con el diseño visual de tus tablas HTML.
    echo "Conexión exitosa a la base de datos de la Veterinaria.";

} catch (\PDOException $e) {
    // Si hay un error, detiene la ejecución y te dice qué pasó
    die("Error crítico de conexión: " . $e->getMessage());
}
?>
