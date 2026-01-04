<?php
ob_start(); 
require_once "includes/header.php"; 
require_once 'auth.php';
?>

<div class="container mt-4">
  <h2 class="mb-3">Registrar Asistencia</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <caption>Registrar Asistencia</caption>
      <thead class="thead-dark">
        <tr>
          <th>Grupo</th>
          <th>Socio</th>
          <th>DNI</th>
          <th>Primera salida</th>
          <th>Fecha 1</th>
          <th>Segunda salida</th>
          <th>Fecha 2</th>
          <th>Tercera salida</th>
          <th>Fecha 3</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Grupo 10</td>
          <td>Juan Pérez</td>
          <td>12345678</td>
          <td>
            <select name="primera_salida_juan" class="form-control form-control-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Justificado">Justificado</option>
            </select>
          </td>
          <td>
            <input type="date" name="fecha_primera_salida_juan" class="form-control form-control-sm" />
          </td>
          <td>
            <select name="segunda_salida_juan" class="form-control form-control-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Justificado">Justificado</option>
            </select>
          </td>
          <td>
            <input type="date" name="fecha_segunda_salida_juan" class="form-control form-control-sm" />
          </td>
          <td>
            <select name="tercera_salida_juan" class="form-control form-control-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Justificado">Justificado</option>
            </select>
          </td>
          <td>
            <input type="date" name="fecha_tercera_salida_juan" class="form-control form-control-sm" />
          </td>
        </tr>

        <tr>
          <td>Grupo 10</td>
          <td>María López</td>
          <td>87654321</td>
          <td>
            <select name="primera_salida_maria" class="form-control form-control-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Justificado">Justificado</option>
            </select>
          </td>
          <td>
            <input type="date" name="fecha_primera_salida_maria" class="form-control form-control-sm" />
          </td>
          <td>
            <select name="segunda_salida_maria" class="form-control form-control-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Justificado">Justificado</option>
            </select>
          </td>
          <td>
            <input type="date" name="fecha_segunda_salida_maria" class="form-control form-control-sm" />
          </td>
          <td>
            <select name="tercera_salida_maria" class="form-control form-control-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Justificado">Justificado</option>
            </select>
          </td>
          <td>
            <input type="date" name="fecha_tercera_salida_maria" class="form-control form-control-sm" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <button type="submit" class="btn btn-primary" onclick="alert('Asistencia registrada (simulado)')">Registrar Asistencia</button>
</div>

<?php require_once "includes/footer.php"; ?>
