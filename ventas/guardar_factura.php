<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../db.php";

// Verificar si se enviaron los datos necesarios
if (
    !isset($_POST['cliente'], $_POST['producto'], $_POST['cantidad'], $_POST['precio']) ||
    !is_array($_POST['producto']) || !is_array($_POST['cantidad']) || !is_array($_POST['precio'])
) {
    die("Datos incompletos o mal formateados.");
}

$cliente = trim($_POST['cliente']);
$productos = $_POST['producto'];
$cantidades = $_POST['cantidad'];
$precios = $_POST['precio'];

// Calcular total de la venta
$total = 0;
for ($i = 0; $i < count($productos); $i++) {
    $cantidad = (float)$cantidades[$i];
    $precio = (float)$precios[$i];
    $total += $cantidad * $precio;
}

// Insertar venta principal
$stmtVenta = $conn->prepare("INSERT INTO ventas (cliente, total) VALUES (?, ?)");
if (!$stmtVenta) {
    die("Error al preparar venta: " . $conn->error);
}
$stmtVenta->bind_param("sd", $cliente, $total);
$stmtVenta->execute();
$venta_id = $stmtVenta->insert_id;
$stmtVenta->close();

// Preparar inserción de detalles
$stmtDetalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto, cantidad, precio) VALUES (?, ?, ?, ?)");
if (!$stmtDetalle) {
    die("Error al preparar detalle: " . $conn->error);
}

for ($i = 0; $i < count($productos); $i++) {
    $producto = trim($productos[$i]);
    $cantidad = (int)$cantidades[$i];
    $precio = (float)$precios[$i];

    $stmtDetalle->bind_param("isid", $venta_id, $producto, $cantidad, $precio);
    $stmtDetalle->execute();
}

$stmtDetalle->close();
$conn->close();

// Redirigir al listado
header("Location: listado.php");
exit();
