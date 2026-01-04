<?php
session_start();
require_once 'includes/conexion.php';

// Verifica si hay un mensaje de error en sesión
$mensaje_error = "";
if (!empty($_SESSION['mensaje_error'])) {
    $mensaje_error = $_SESSION['mensaje_error'];
    unset($_SESSION['mensaje_error']); // Elimina para que no se muestre al recargar
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Escapar el correo para evitar SQL injection
    $correo_escaped = $conn->real_escape_string($correo);

    // Usar query directo en lugar de prepared statement (evita bug en PostgresAdapter)
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo_escaped' LIMIT 1";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($contrasena, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];
            $_SESSION['personal_id'] = $usuario['personal_id'];

            // Redirige después del login
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $_SESSION['mensaje_error'] = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['mensaje_error'] = "Usuario no encontrado.";
    }

    // Redirige para evitar que el error persista en el reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$conn->close();
?>


<!-- HTML del formulario de login aquí -->


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SB Admin 2 - Iniciar sesión</title>
    <link rel="icon" type="image/png" href="assets/img/icono-sima.PNG">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet" />
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />

</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
                                <img src="assets/img/sima.png" alt="Imagen de Login" class="img-fluid"
                                    style="max-height: 90%; max-width: 90%" />
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">¡Bienvenido de nuevo!</h1>
                                    </div>

                                    <?php if (!empty($mensaje_error)): ?>
                                        <div id="alerta-error"
                                            style="background-color: #f44336; color: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); font-family: Arial, sans-serif; font-size: 16px; max-width: 600px; margin: 20px auto; text-align: left; display: flex; align-items: center; transition: opacity 1s ease;">
                                            <span style="font-size: 20px; margin-right: 12px;">&#9888;</span>
                                            <?= htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8') ?>
                                        </div>

                                    <?php endif; ?>
                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="correo"
                                                name="correo" placeholder="Correo electrónico" required />
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="contrasena" name="contrasena" placeholder="Contraseña" required />
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="recordarme"
                                                    name="recordarme" />
                                                <label class="custom-control-label" for="recordarme">Recordarme</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Iniciar sesión
                                        </button>
                                    </form>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

<script>
    setTimeout(function () {
        var alerta = document.getElementById('alerta-error');
        alerta.style.opacity = '0';
        setTimeout(function () {
            alerta.style.display = 'none';
        }, 1000); // Espera a que termine la transición
    }, 4000); // Desaparece después de 4 segundos
</script>

</html>