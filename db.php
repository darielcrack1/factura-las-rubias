<?php
$conn = new mysqli("localhost", "root", "", "sistema_ventas");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
