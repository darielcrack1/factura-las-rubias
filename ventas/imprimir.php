<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../db.php";

// Validar ID de venta
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de venta no especificado o inválido.");
}

$venta_id = intval($_GET['id']);

// Obtener datos de la venta
$stmtVenta = $conn->prepare("SELECT * FROM ventas WHERE id = ?");
if (!$stmtVenta) {
    die("Error en la consulta: " . $conn->error);
}
$stmtVenta->bind_param("i", $venta_id);
$stmtVenta->execute();
$resultado = $stmtVenta->get_result();
$venta = $resultado->fetch_assoc();
$stmtVenta->close();

if (!$venta) {
    die("Venta no encontrada.");
}

// Obtener detalles de la venta
$stmtDetalle = $conn->prepare("SELECT producto, cantidad, precio FROM detalle_ventas WHERE venta_id = ?");
if (!$stmtDetalle) {
    die("Error en la consulta de detalles: " . $conn->error);
}
$stmtDetalle->bind_param("i", $venta_id);
$stmtDetalle->execute();
$detalles = $stmtDetalle->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Venta #<?= $venta_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-white">
<div class="container mt-5">
    <div class="text-center mb-4">
        <h2>Factura de Venta</h2>
        <p><strong>Número:</strong> <?= $venta_id ?> | <strong>Fecha:</strong> <?= htmlspecialchars($venta['fecha']) ?></p>
    </div>

    <div class="mb-4">
        <h5>Cliente:</h5>
        <p><?= htmlspecialchars($venta['cliente']) ?></p>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            while ($detalle = $detalles->fetch_assoc()):
                $cantidad = (int)$detalle['cantidad'];
                $precio = (float)$detalle['precio'];
                $subtotal = $cantidad * $precio;
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($detalle['producto']) ?></td>
                    <td><?= $cantidad ?></td>
                    <td>$<?= number_format($precio, 2) ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>$<?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir</button>
        <a href="listado.php" class="btn btn-secondary">← Volver</a>
    </div>
</div>
</body>
</html>

<?php
$stmtDetalle->close();
$conn->close();
?>
