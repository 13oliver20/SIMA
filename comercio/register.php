<?php require_once "includes/conexion.php"; ?>
<?php
ob_start();
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dni = $_POST['dni'] ?? '';
  $nombres = $_POST['nombres'] ?? '';
  $apellidos = $_POST['apellidos'] ?? '';
  $cargo = $_POST['cargo'] ?? '';

  $nombre_usuario = $_POST['nombre'] ?? '';
  $apellido_usuario = $_POST['apellido'] ?? '';
  $correo = $_POST['correo'] ?? '';
  $contrasena = $_POST['contrasena'] ?? '';
  $repetirContrasena = $_POST['repetir_contrasena'] ?? '';

  if ($contrasena !== $repetirContrasena) {
    $_SESSION['error'] = "Las contraseñas no coinciden.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }

  // Iniciar transacción
  $conn->begin_transaction();

  try {
    // Insertar en tabla personal
    $stmt = $conn->prepare("INSERT INTO personal (dni, nombres, apellidos, cargo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $dni, $nombres, $apellidos, $cargo);
    $stmt->execute();
    $personal_id = $conn->insert_id;
    $stmt->close();

    // Insertar en tabla usuarios
    $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, correo, contraseña, personal_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre_usuario, $apellido_usuario, $correo, $hashContrasena, $personal_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $_SESSION['exito'] = "Registro completado correctamente.";
  } catch (mysqli_sql_exception $e) {
    $conn->rollback();
    if ($e->getCode() == 1062) { // Error de clave duplicada (correo)
      $_SESSION['error'] = "El correo ya está registrado.";
    } else {
      $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }
}
?>

<?php require_once "includes/header.php"; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card o-hidden border-0 shadow-lg w-100" style="max-width: 900px;">
    <div class="card-body p-0">
      <div class="row">
        <!-- Imagen opcional 
        <div class="col-lg-5 d-none d-lg-block bg-register-image">
          <img
            src="assets/img/sima.png"
            alt="Imagen de Login"
            class="img-fluid"
            style="max-height: 90%; max-width: 90%" />
        </div> 
        -->
        <div class="col-lg-12">
          <div class="p-5">
            <div class="text-center">
              <h1 class="h4 text-gray-900 mb-4">¡Crea una cuenta!</h1>
              <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                  <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
              <?php endif; ?>
              <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success">
                  <?= $_SESSION['exito']; unset($_SESSION['exito']); ?>
                </div>
              <?php endif; ?>
            </div>

            <form class="user" method="POST" action="">
              <h5>Datos del Personal Administrativo</h5>
              <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <input type="text" class="form-control form-control-user" name="dni" placeholder="DNI" maxlength="8" required />
                </div>
                <div class="col-sm-6">
                  <input type="text" class="form-control form-control-user" name="cargo" placeholder="Cargo" />
                </div>
              </div>

              <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <input type="text" class="form-control form-control-user" name="nombres" placeholder="Nombres del personal" required />
                </div>
                <div class="col-sm-6">
                  <input type="text" class="form-control form-control-user" name="apellidos" placeholder="Apellidos del personal" required />
                </div>
              </div>

              <hr />
              <h5>Datos de Usuario</h5>
              <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <input type="text" class="form-control form-control-user" name="nombre" placeholder="Nombre de usuario" required />
                </div>
                <div class="col-sm-6">
                  <input type="text" class="form-control form-control-user" name="apellido" placeholder="Apellido de usuario" required />
                </div>
              </div>

              <div class="form-group">
                <input type="email" class="form-control form-control-user" name="correo" placeholder="Correo electrónico" required />
              </div>

              <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                  <input type="password" class="form-control form-control-user" name="contrasena" placeholder="Contraseña" required />
                </div>
                <div class="col-sm-6">
                  <input type="password" class="form-control form-control-user" name="repetir_contrasena" placeholder="Repetir contraseña" required />
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-user btn-block">
                Registrar cuenta
              </button>
              <hr />
            </form>
            <!-- <div class="text-center">
              <a class="small" href="login.php">¿Ya tienes cuenta? ¡Inicia sesión!</a>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once "includes/footer.php"; ?>
