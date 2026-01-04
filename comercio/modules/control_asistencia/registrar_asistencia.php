<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Registro de Asistencia</title>
<style>
  table {
    border-collapse: collapse;
    width: 100%;
    max-width: 900px;
    margin: 20px auto;
    font-family: Arial, sans-serif;
  }
  th, td {
    border: 1px solid #ccc;
    padding: 6px 8px;
    text-align: center;
  }
  th {
    background-color: #f2f2f2;
  }
  select, input[type="date"] {
    width: 100%;
    box-sizing: border-box;
  }
  caption {
    font-size: 1.5em;
    margin-bottom: 10px;
  }
  button {
    display: block;
    margin: 20px auto;
    padding: 8px 16px;
    font-size: 1em;
  }
</style>
</head>
<body>

<table>
  <caption>Registrar Asistencia</caption>
  <thead>
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
    <!-- Ejemplo con 2 socios, agrega más según necesidad -->
    <tr>
      <td>Grupo 10</td>
      <td>Juan Pérez</td>
      <td>12345678</td>
      <td>
        <select name="primera_salida_juan">
          <option value="Presente">Presente</option>
          <option value="Ausente">Ausente</option>
          <option value="Justificado">Justificado</option>
        </select>
      </td>
      <td>
        <input type="date" name="fecha_primera_salida_juan" value="" />
      </td>
      <td>
        <select name="segunda_salida_juan">
          <option value="Presente">Presente</option>
          <option value="Ausente">Ausente</option>
          <option value="Justificado">Justificado</option>
        </select>
      </td>
      <td>
        <input type="date" name="fecha_segunda_salida_juan" value="" />
      </td>
      <td>
        <select name="tercera_salida_juan">
          <option value="Presente">Presente</option>
          <option value="Ausente">Ausente</option>
          <option value="Justificado">Justificado</option>
        </select>
      </td>
      <td>
        <input type="date" name="fecha_tercera_salida_juan" value="" />
      </td>
    </tr>

    <tr>
      <td>Grupo 10</td>
      <td>María López</td>
      <td>87654321</td>
      <td>
        <select name="primera_salida_maria">
          <option value="Presente">Presente</option>
          <option value="Ausente">Ausente</option>
          <option value="Justificado">Justificado</option>
        </select>
      </td>
      <td>
        <input type="date" name="fecha_primera_salida_maria" value="" />
      </td>
      <td>
        <select name="segunda_salida_maria">
          <option value="Presente">Presente</option>
          <option value="Ausente">Ausente</option>
          <option value="Justificado">Justificado</option>
        </select>
      </td>
      <td>
        <input type="date" name="fecha_segunda_salida_maria" value="" />
      </td>
      <td>
        <select name="tercera_salida_maria">
          <option value="Presente">Presente</option>
          <option value="Ausente">Ausente</option>
          <option value="Justificado">Justificado</option>
        </select>
      </td>
      <td>
        <input type="date" name="fecha_tercera_salida_maria" value="" />
      </td>
    </tr>

  </tbody>
</table>

<button type="submit" onclick="alert('Asistencia registrada (simulado)')">Registrar Asistencia</button>

</body>
</html>
