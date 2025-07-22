<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Las Rubias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color-light: #f8f9fa;
            --text-color-light: #212529;
            --card-bg-light: #ffffff;
            --text-muted-light: #6c757d;

            --bg-color-dark: #121212;
            --text-color-dark: #f8f9fa;
            --card-bg-dark: #1e1e1e;
            --text-muted-dark: #adb5bd;
        }

        body {
            font-family: 'Roboto', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .light-mode {
            background-color: var(--bg-color-light);
            color: var(--text-color-light);
        }

        .dark-mode {
            background-color: var(--bg-color-dark);
            color: var(--text-color-dark);
        }

        .card {
            border-radius: 12px;
            border: none;
            transition: background-color 0.3s;
        }

        .light-mode .card {
            background-color: var(--card-bg-light);
        }

        .dark-mode .card {
            background-color: var(--card-bg-dark);
        }

        footer {
            margin-top: 60px;
            padding: 20px 0;
        }

        /* Ajustes para textos específicos segun modo */
        /* Bienvenido a */
        .bienvenido-text {
            font-weight: 700;
            color: var(--text-color-light);
            transition: color 0.3s;
        }
        .dark-mode .bienvenido-text {
            color: var(--text-color-dark);
        }

        /* ¿Qué deseas hacer hoy? */
        .pregunta-text {
            color: var(--text-muted-light);
            transition: color 0.3s;
        }
        .dark-mode .pregunta-text {
            color: var(--text-muted-dark);
        }

        /* Footer texto */
        footer p {
            color: var(--text-muted-light);
            transition: color 0.3s;
        }
        .dark-mode footer p {
            color: var(--text-muted-dark);
        }

        .btn i {
            margin-right: 6px;
        }

        .toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* Botón redondo */
        #modoToggle {
            border-radius: 50%;
            width: 45px;
            height: 45px;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
        }

        #modoToggle i {
            pointer-events: none;
        }
    </style>
</head>
<body>

<!-- Botón para cambiar tema -->
<div class="toggle-btn">
    <button id="modoToggle" class="btn btn-secondary" title="Cambiar modo">
        <i class="bi bi-moon-stars-fill"></i>
    </button>
</div>

<div class="container mt-5">
    <div class="card p-4 shadow-sm text-center">
        <h2 class="mb-3 bienvenido-text">Bienvenido a <strong class="text-danger">Factura Las Rubias</strong></h2>
        <p class="pregunta-text">¿Qué deseas hacer hoy?</p>

        <div class="d-grid gap-3 col-md-6 mx-auto mt-4">
            <a href="ventas/registrar.php" class="btn btn-success btn-lg">
                <i class="bi bi-plus-circle"></i> Registrar Venta
            </a>
            <a href="ventas/listado.php" class="btn btn-primary btn-lg">
                <i class="bi bi-list-ul"></i> Ver Ventas
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-lg">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</div>

<footer class="text-center">
    <p class="mt-4">&copy; <?= date("Y") ?> Factura Las Rubias. Todos los derechos reservados.</p>
</footer>

<script>
    const body = document.body;
    const toggleBtn = document.getElementById('modoToggle');

    // Cargar tema guardado
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
