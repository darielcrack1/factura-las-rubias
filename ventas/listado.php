<?php 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../db.php";

$ventas = $conn->query("SELECT * FROM ventas ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Ventas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color-light: #f5f7fa;
            --text-color-light: #343a40;
            --card-bg-light: #fff;
            --btn-outline-dark-light: #343a40;

            --bg-color-dark: #121212;
            --text-color-dark: #f8f9fa;
            --card-bg-dark: #1e1e1e;
            --btn-outline-dark-dark: #f8f9fa;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color-light);
            color: var(--text-color-light);
            transition: background-color 0.3s, color 0.3s;
            margin-bottom: 50px;
        }

        .light-mode {
            background-color: var(--bg-color-light);
            color: var(--text-color-light);
        }

        .dark-mode {
            background-color: var(--bg-color-dark);
            color: var(--text-color-dark);
        }

        h2 {
            font-weight: 700;
            transition: color 0.3s;
            color: var(--text-color-light);
        }
        .dark-mode h2 {
            color: var(--text-color-dark);
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            background-color: var(--card-bg-light);
            transition: background-color 0.3s, color 0.3s;
            color: var(--text-color-light);
        }
        .dark-mode .card {
            background-color: var(--card-bg-dark);
            color: var(--text-color-dark);
            border-color: #444;
            box-shadow: none;
        }

        .btn-outline-primary {
            font-weight: 500;
            color: #0d6efd;
            border-color: #0d6efd;
            transition: color 0.3s, border-color 0.3s;
        }
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        summary {
            cursor: pointer;
            font-weight: 600;
            color: #0d6efd;
            transition: color 0.3s;
        }
        summary:hover {
            text-decoration: underline;
        }
        .dark-mode summary {
            color: #66aaff;
        }

        .btn-outline-dark {
            font-weight: 500;
            color: var(--btn-outline-dark-light);
            border-color: var(--btn-outline-dark-light);
            transition: color 0.3s, border-color 0.3s;
        }
        .btn-outline-dark:hover {
            background-color: var(--btn-outline-dark-light);
            color: var(--bg-color-light);
        }
        .dark-mode .btn-outline-dark {
            color: var(--btn-outline-dark-dark);
            border-color: var(--btn-outline-dark-dark);
        }
        .dark-mode .btn-outline-dark:hover {
            background-color: var(--btn-outline-dark-dark);
            color: var(--bg-color-dark);
        }

        .toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        #modoToggle {
            border-radius: 50%;
            width: 45px;
            height: 45px;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            color: #212529;
            background-color: #e2e6ea;
            border: none;
            transition: background-color 0.3s, color 0.3s;
            cursor: pointer;
        }

        #modoToggle:hover {
            background-color: #ced4da;
        }

        .dark-mode #modoToggle {
            color: #f8f9fa;
            background-color: #343a40;
        }

        .dark-mode #modoToggle:hover {
            background-color: #495057;
        }

        #modoToggle i {
            pointer-events: none;
        }

        .btn-imprimir {
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 2px 6px rgba(0,123,255,0.4);
        }
        .btn-imprimir:hover {
            filter: brightness(1.1);
            box-shadow: 0 6px 12px rgba(0,123,255,0.6);
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="toggle-btn">
    <button id="modoToggle" title="Cambiar modo">
        <i class="bi bi-moon-stars-fill"></i>
    </button>
</div>

<div class="container mt-5">
    <h2 class="mb-4 text-center">💾 Listado de Ventas</h2>
    <div class="d-flex justify-content-start mb-4">
        <a href="../dashboard.php" class="btn btn-outline-primary">← Volver al tablero</a>
    </div>

    <?php while($venta = $ventas->fetch_assoc()): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente']) ?></h5>
                <p class="card-text mb-1">
                    <strong>Fecha:</strong> <?= htmlspecialchars($venta['fecha']) ?>
                </p>
                <p class="card-text mb-3">
                    <strong>Total:</strong> $<?= number_format($venta['total'], 2) ?>
                </p>

                <details>
                    <summary>Ver Detalles</summary>
                    <ul class="list-group list-group-flush mt-2 mb-3">
                        <?php
                        $vid = $venta['id'];
                        $stmt = $conn->prepare("SELECT producto, cantidad, precio FROM detalle_ventas WHERE venta_id = ?");
                        $stmt->bind_param("i", $vid);
                        $stmt->execute();
                        $detalles = $stmt->get_result();

                        while ($detalle = $detalles->fetch_assoc()):
                        ?>
                            <li class="list-group-item">
                                <?= htmlspecialchars($detalle['producto']) ?> — <?= $detalle['cantidad'] ?> x $<?= number_format($detalle['precio'], 2) ?>
                            </li>
                        <?php endwhile;
                        $stmt->close();
                        ?>
                    </ul>
                </details>

                <a href="imprimir.php?id=<?= $venta['id'] ?>" 
                   class="btn btn-primary btn-sm btn-imprimir">
                   <i class="bi bi-printer-fill"></i> Imprimir
                </a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
const body = document.body;
const toggleBtn = document.getElementById('modoToggle');

const temaGuardado = localStorage.getItem('modo') || 'light';
aplicarModo(temaGuardado);

toggleBtn.addEventListener('click', () => {
    const nuevoModo = body.classList.contains('dark-mode') ? 'light' : 'dark';
    aplicarModo(nuevoModo);
    localStorage.setItem('modo', nuevoModo);
});

function aplicarModo(modo) {
    if (modo === 'dark') {
        body.classList.add('dark-mode');
        body.classList.remove('light-mode');
        toggleBtn.innerHTML = '<i class="bi bi-brightness-high-fill"></i>';
        toggleBtn.title = "Modo Claro";
    } else {
        body.classList.add('light-mode');
        body.classList.remove('dark-mode');
        toggleBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        toggleBtn.title = "Modo Oscuro";
    }
}
</script>

</body>
</html>