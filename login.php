<?php
session_start();
require_once "db.php";

// Recordar usuario si hay cookie
$usuarioRecordado = $_COOKIE['usuarioRecordado'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];
    $recordar = isset($_POST["recordar"]);

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $row = $resultado->fetch_assoc();
        if (password_verify($clave, $row['clave'])) {
            $_SESSION['usuario'] = $usuario;
            if ($recordar) {
                setcookie('usuarioRecordado', $usuario, time() + 2592000, "/");
            } else {
                setcookie('usuarioRecordado', '', time() - 3600, "/");
            }
            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Usuario o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        :root {
            --bg-light: #f8f9fa;
            --text-light: #212529;
            --card-bg-light: #ffffff;
            --input-bg-light: #fff;
            --input-border-light: #ced4da;

            --bg-dark: #121212;
            --text-dark: #f8f9fa;
            --card-bg-dark: #1e1e1e;
            --input-bg-dark: #2c2c2c;
            --input-border-dark: #555;
            --btn-primary-dark-bg: #0d6efd;
            --btn-primary-dark-hover: #0b5ed7;
        }

        body {
            font-family: 'Roboto', sans-serif;
            transition: background-color 0.3s, color 0.3s;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            margin: 0;
        }

        .light-mode {
            background-color: var(--bg-light);
            color: var(--text-light);
        }

        .dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            padding: 30px 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, color 0.3s;
        }

        .light-mode .card {
            background-color: var(--card-bg-light);
            color: var(--text-light);
        }

        .dark-mode .card {
            background-color: var(--card-bg-dark);
            color: var(--text-dark);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
        }

        label.form-label {
            font-weight: 600;
            color: var(--text-light);
        }

        .dark-mode label.form-label {
            color: var(--text-dark);
        }

        input.form-control {
            background-color: var(--input-bg-light);
            color: var(--text-light);
            border: 1px solid var(--input-border-light);
        }

        .dark-mode input.form-control {
            background-color: var(--input-bg-dark);
            color: var(--text-dark);
            border-color: var(--input-border-dark);
        }

        input::placeholder {
            color: #6c757d;
        }

        .dark-mode input::placeholder {
            color: #adb5bd;
        }

        .input-group-text {
            cursor: pointer;
            background-color: #e9ecef;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            justify-content: center;
            align-items: center;
            display: flex;
            font-size: 1.3rem;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
            transition: background-color 0.3s, color 0.3s;
        }

        .input-group-text:hover {
            background-color: #d6d8db;
        }

        .dark-mode .input-group-text {
            background-color: #333;
            color: #f8f9fa;
            box-shadow: 0 3px 6px rgba(255, 255, 255, 0.1);
        }

        .dark-mode .input-group-text:hover {
            background-color: #444;
        }

        button.btn-primary {
            width: 100%;
            font-weight: 700;
            padding: 10px;
            border-radius: 8px;
            border: none;
            background-color: var(--btn-primary-dark-bg);
            color: #fff;
        }

        button.btn-primary:hover {
            background-color: var(--btn-primary-dark-hover);
        }

        .alert {
            font-weight: 600;
        }

        p.text-muted {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .dark-mode p.text-muted {
            color: #ced4da;
        }

        .dark-mode p.text-muted strong {
            color: #f8f9fa;
        }

        .toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
        }

        #modoToggle {
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            color: #212529;
            background-color: #e2e6ea;
            border: none;
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
    </style>
</head>
<body>

<!-- Botón modo oscuro -->
<div class="toggle-btn">
    <button id="modoToggle" title="Cambiar modo">
        <i class="bi bi-moon-stars-fill"></i>
    </button>
</div>

<div class="card">
    <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario:</label>
            <input type="text" id="usuario" name="usuario" class="form-control" required placeholder="Ingrese su usuario" value="<?= htmlspecialchars($usuarioRecordado) ?>" />
        </div>

        <div class="mb-3">
            <label for="clave" class="form-label">Contraseña:</label>
            <div class="input-group">
                <input type="password" id="clave" name="clave" class="form-control" required placeholder="Ingrese su contraseña" />
                <span class="input-group-text" id="togglePassword" role="button" tabindex="0" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye-fill"></i>
                </span>
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="recordar" name="recordar" <?= $usuarioRecordado ? 'checked' : '' ?>>
            <label class="form-check-label" for="recordar">Recordar usuario</label>
        </div>

        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>

    <p class="text-muted mt-3">Usuario: <strong>demo</strong> | Clave: <strong>tareafacil25</strong></p>
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
        } else {
            body.classList.add('light-mode');
            body.classList.remove('dark-mode');
            toggleBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        }
    }

    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('clave');

    togglePassword.addEventListener('click', () => {
        const tipo = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = tipo;
        togglePassword.innerHTML = tipo === 'password' ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-eye-slash-fill"></i>';
    });

    togglePassword.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            togglePassword.click();
        }
    });
</script>

</body>
</html>
