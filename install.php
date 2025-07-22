<?php
// install.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_ventas";

// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos creada correctamente o ya existe.<br>";
} else {
    die("Error creando la base de datos: " . $conn->error);
}

$conn->select_db($dbname);

// Crear tabla usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Tabla 'usuarios' creada correctamente.<br>";
} else {
    die("Error creando tabla usuarios: " . $conn->error);
}

// Crear usuario por defecto
$usuario = 'demo';
$clave = password_hash('tareafacil25', PASSWORD_DEFAULT);

$check = $conn->query("SELECT * FROM usuarios WHERE usuario = '$usuario'");
if ($check->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, clave) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $clave);
    $stmt->execute();
    echo "Usuario demo creado correctamente.<br>";
} else {
    echo "Usuario demo ya existe.<br>";
}

// Crear tabla ventas
$sql = "CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(100),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2)
)";
$conn->query($sql);

// Crear tabla detalle_ventas
$sql = "CREATE TABLE IF NOT EXISTS detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    producto VARCHAR(100),
    cantidad INT,
    precio DECIMAL(10,2),
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE
)";
$conn->query($sql);

echo "Estructura completada.";
$conn->close();
?>
