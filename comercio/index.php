<?php
ob_start();
require_once "includes/header.php";
require_once "includes/conexion.php";
require_once 'auth.php';

// Consulta para obtener el total de socios
$sql_socios = "SELECT COUNT(*) AS total FROM socio";
$result_socios = $conn->query($sql_socios);
$total_socios = $result_socios ? $result_socios->fetch_assoc()['total'] : 0;

// Consulta para obtener el total de asociaciones (grupos)
$sql_asociaciones = "SELECT COUNT(*) AS total FROM grupo";
$result_asociaciones = $conn->query($sql_asociaciones);
$total_asociaciones = $result_asociaciones ? $result_asociaciones->fetch_assoc()['total'] : 0;

// Consulta para contar socios que pertenecen a más de una asociación
$sql_socios_multiples = "SELECT COUNT(*) AS total FROM (
        SELECT socio_idsocio FROM socio_asociacion 
        GROUP BY socio_idsocio HAVING COUNT(grupo_idgrupo) > 1
    ) AS subquery";
$result_socios_multiples = $conn->query($sql_socios_multiples);
$socios_multiples = $result_socios_multiples ? (int) $result_socios_multiples->fetch_assoc()['total'] : 0;

// Consulta para contar el total de asociados (incluye repeticiones si un socio está en varias asociaciones)
$sql_total_asociados = "SELECT COUNT(*) AS total FROM socio_asociacion";
$result_total_asociados = $conn->query($sql_total_asociados);
$total_asociados = $result_total_asociados ? (int) $result_total_asociados->fetch_assoc()['total'] : 0;

// Consulta para contar todos los registros en verificacion_asociados
$sql_total_verificados = "SELECT COUNT(*) AS total_registrados FROM verificacion_asociados";
$result_total_verificados = $conn->query($sql_total_verificados);
$total_verificados = $result_total_verificados ? (int) $result_total_verificados->fetch_assoc()['total_registrados'] : 0;

// Calcular el total de socios no verificados
$total_no_verificados = $total_asociados - $total_verificados;

// Consulta para obtener el total de grupos activos
$query_activos = "SELECT COUNT(*) AS total_activos FROM grupo WHERE estado = 'Activo'";
$resultado_activos = $conn->query($query_activos);
$total_activos = $resultado_activos->fetch_assoc()['total_activos'];

// Consulta para obtener el total de grupos inactivos
$query_inactivos = "SELECT COUNT(*) AS total_inactivos FROM grupo WHERE estado = 'Inactivo'";
$resultado_inactivos = $conn->query($query_inactivos);
$total_inactivos = $resultado_inactivos->fetch_assoc()['total_inactivos'];

// Masculinos
$sql_masculinos = "SELECT COUNT(*) AS total_masculinos
                   FROM socio_asociacion sa
                   JOIN socio s ON sa.socio_idsocio = s.idsocio
                   WHERE s.genero = 'M'";
$result_masculinos = $conn->query($sql_masculinos);
$total_masculinos = $result_masculinos ? $result_masculinos->fetch_assoc()['total_masculinos'] : 0;

// Femeninos
$sql_femeninos = "SELECT COUNT(*) AS total_femeninos
                  FROM socio_asociacion sa
                  JOIN socio s ON sa.socio_idsocio = s.idsocio
                  WHERE s.genero = 'F'";
$result_femeninos = $conn->query($sql_femeninos);
$total_femeninos = $result_femeninos ? $result_femeninos->fetch_assoc()['total_femeninos'] : 0;

?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- Primera fila de tarjetas -->
    <div class="row">

        <!-- Total de Socios -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Socios</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_asociados; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Asociaciones -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Asociaciones</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_asociaciones; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Socios en múltiples asociaciones -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Socios Duplicados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $socios_multiples; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asociaciones Activas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Asociaciones Activas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_activos; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila de tarjetas -->
    <div class="row">

        <!-- Socios Inactivos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Asociaciones Inactivas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $total_inactivos; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mercados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Socios Masculinos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $total_masculinos; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-market fa-2x text-gray-300"></i> <!-- puedes usar fa-store o fa-store-alt -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rubros -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Socios femeninos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $total_femeninos; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actas Registradas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-light shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-light text-uppercase mb-1">Actas Registradas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $total_socios; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Direct
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Social
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Referral
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->

</div>
<!-- /.container-fluid -->


<?php require_once "includes/footer.php" ?>