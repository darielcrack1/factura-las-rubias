<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

function generarNumeroRecibo() {
    return 'REC-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

function generarCodigoCliente() {
    return strtoupper('CL' . substr(md5(uniqid()), 0, 6)); // Ejemplo: CLa1b2c3
}

$fecha = date("d/m/Y");
$recibo = generarNumeroRecibo();
$codigoCliente = generarCodigoCliente();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color-light: #f8f9fa;
            --text-color-light: #212529;
            --card-bg-light: #ffffff;
            --input-bg-light: #fff;
            --input-border-light: #ced4da;
            --btn-outline-dark-light: #343a40;

            --bg-color-dark: #121212;
            --text-color-dark: #f8f9fa;
            --card-bg-dark: #1e1e1e;
            --input-bg-dark: #2c2c2c;
            --input-border-dark: #555;
            --btn-outline-dark-dark: #f8f9fa;
            --placeholder-dark: #adb5bd;
            --placeholder-light: #6c757d;
        }

        body {
            font-family: 'Roboto', sans-serif;
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

        .form-section {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 30px;
            transition: background-color 0.3s, color 0.3s;
        }
        .light-mode .form-section {
            background-color: var(--card-bg-light);
            color: var(--text-color-light);
        }
        .dark-mode .form-section {
            background-color: var(--card-bg-dark);
            color: var(--text-color-dark);
            box-shadow: none;
        }

        .product-group {
            border: 1px solid var(--input-border-light);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: var(--input-bg-light);
            transition: background-color 0.3s, border-color 0.3s;
        }
        .dark-mode .product-group {
            border-color: var(--input-border-dark);
            background-color: var(--input-bg-dark);
        }

        input.form-control, textarea.form-control {
            background-color: var(--input-bg-light);
            color: var(--text-color-light);
            border: 1px solid var(--input-border-light);
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }
        .dark-mode input.form-control,
        .dark-mode textarea.form-control {
            background-color: var(--input-bg-dark);
            color: var(--text-color-dark);
            border-color: var(--input-border-dark);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--placeholder-light);
            opacity: 1;
        }

        .dark-mode input::placeholder,
        .dark-mode textarea::placeholder {
            color: var(--placeholder-dark);
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
        }

        #modoToggle:hover {
            background-color: #ced4da;
            cursor: pointer;
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
    </style>
</head>
<body>

<div class="toggle-btn">
    <button id="modoToggle" title="Cambiar modo">
        <i class="bi bi-moon-stars-fill"></i>
    </button>
</div>

<div class="container mt-5">
    <div class="form-section">
        <h2 class="mb-4 text-center">📝 Registrar Nueva Venta</h2>
        <form method="POST" action="guardar_factura.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha:</label>
                    <input type="text" name="fecha" class="form-control" value="<?= $fecha ?>" required readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nº Recibo:</label>
                    <input type="text" name="recibo" class="form-control" value="<?= $recibo ?>" required readonly>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">Código Cliente:</label>
                    <input type="text" name="codigo_cliente" class="form-control" value="<?= $codigoCliente ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nombre del Cliente:</label>
                    <input type="text" name="cliente" class="form-control" placeholder="Ej. Juan Pérez" required>
                </div>
            </div>

            <h5 class="mt-4">🛒 Artículos</h5>
            <div id="productos">
                <div class="product-group row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="producto[]" class="form-control" placeholder="Producto" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="cantidad[]" class="form-control" placeholder="Cantidad" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="precio[]" class="form-control" placeholder="Precio" min="0" required>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">🗑️</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-secondary mt-3" onclick="agregarProducto()">+ Agregar otro producto</button>

            <div class="mt-4">
                <label class="form-label">Comentario:</label>
                <textarea name="comentario" class="form-control" rows="2" placeholder="Ej. Pagó en efectivo"></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">🖨️ Guardar e Imprimir</button>
                <a href="../dashboard.php" class="btn btn-danger ms-2">❌ Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function agregarProducto() {
    const div = document.createElement("div");
    div.className = "product-group row g-2 align-items-end";
    div.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="producto[]" class="form-control" placeholder="Producto" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="cantidad[]" class="form-control" placeholder="Cantidad" min="1" required>
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="precio[]" class="form-control" placeholder="Precio" min="0" required>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">🗑️</button>
        </div>
    `;
    document.getElementById("productos").appendChild(div);
}

function eliminarProducto(btn) {
    btn.closest(".product-group").remove();
}

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
