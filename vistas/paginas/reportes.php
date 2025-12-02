<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet" href="assets/css/reportes.css">

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reportes</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reportes-container">

    <!-- Título principal -->
    <div class="reportes-header mb-4">
        <div>
            <h1 class="mb-1">
                <i class="fa-solid fa-chart-line me-2"></i> Reportes del Sistema
            </h1>
            <p class="text-muted mb-0">
                Analiza el rendimiento de la concesionaria desde diferentes perspectivas: ventas, gastos, stock, clientes y más.
            </p>
        </div>
        <div class="reportes-badge">
            <span class="badge rounded-pill bg-report-main">
                <i class="fa-solid fa-circle-info me-1"></i> Selecciona un módulo para ver sus reportes
            </span>
        </div>
    </div>

    <!-- ============================= -->
    <!-- BLOQUE: REPORTES DE VENTAS    -->
    <!-- ============================= -->
    <section class="report-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="report-section-title">
                <i class="fa-solid fa-handshake-angle me-2"></i> Reportes de Ventas
            </h3>
            <span class="badge bg-soft-success text-success">
                Núcleo del negocio
            </span>
        </div>

        <div class="row g-4">
            <!-- Ventas por mes/año -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-primary text-primary mb-3">
                        <i class="fa-solid fa-calendar-alt"></i>
                    </div>
                    <div class="report-body">
                        <h5>Ventas por mes/año</h5>
                        <p>
                            Analiza la evolución de las ventas en un período determinado, comparando meses y años.
                        </p>
                        <a href="index.php?page=reporte_ventas_periodo" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ventas por vendedor -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-success text-success mb-3">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="report-body">
                        <h5>Ventas por vendedor</h5>
                        <p>
                            Evalúa el desempeño individual de cada vendedor y detecta a los de mejor rendimiento.
                        </p>
                        <a href="index.php?page=reporte_ventas_vendedor" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ventas anuladas -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-danger text-danger mb-3">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div class="report-body">
                        <h5>Ventas anuladas</h5>
                        <p>
                            Identifica las anulaciones de ventas, sus motivos más frecuentes y el impacto económico.
                        </p>
                        <a href="index.php?page=reporte_ventas_anuladas" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ventas por método de pago -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-warning text-warning mb-3">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <div class="report-body">
                        <h5>Ventas por método de pago</h5>
                        <p>
                            Conoce la preferencia de los clientes según el medio de pago utilizado (efectivo, transferencia, etc.).
                        </p>
                        <a href="index.php?page=reporte_ventas_metodo_pago" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- BLOQUE: REPORTES DE COMPRAS   -->
    <!-- ============================= -->
    <section class="report-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="report-section-title">
                <i class="fa-solid fa-car-side me-2"></i> Reportes de Ingresos (Consignaciones)
            </h3>
            <span class="badge bg-soft-primary text-primary">
                Entrada de vehículos
            </span>
        </div>

        <div class="row g-4">
            <!-- Vehículos ingresados por mes/año -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-primary text-primary mb-3">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="report-body">
                        <h5>Ingresos por mes/año</h5>
                        <p>
                            Controla cuántos vehículos ingresan en consignación en cada período.
                        </p>
                        <a href="index.php?page=reporte_compras_periodo" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vehículos por estado -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-info text-info mb-3">
                        <i class="fa-solid fa-traffic-light"></i>
                    </div>
                    <div class="report-body">
                        <h5>Vehículos por estado</h5>
                        <p>
                            Analiza cuántos vehículos están disponibles, con falta de documentación o dados de baja.
                        </p>
                        <a href="index.php?page=reporte_vehiculos_estado" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- BLOQUE: STOCK / INVENTARIO    -->
    <!-- ============================= -->
    <section class="report-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="report-section-title">
                <i class="fa-solid fa-warehouse me-2"></i> Reportes de Stock / Inventario
            </h3>
            <span class="badge bg-soft-secondary text-secondary">
                Control de inventario
            </span>
        </div>

        <div class="row g-4">
            <!-- Stock envejecido -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-warning text-warning mb-3">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div class="report-body">
                        <h5>Stock envejecido</h5>
                        <p>
                            Detecta vehículos con muchos días en stock y baja rotación.
                        </p>
                        <a href="index.php?page=reporte_stock_envejecido" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stock por estado -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-info text-info mb-3">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div class="report-body">
                        <h5>Stock por estado</h5>
                        <p>
                            Visión general de cuántos vehículos hay por cada estado operativo.
                        </p>
                        <a href="index.php?page=reporte_stock_estado" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- BLOQUE: GASTOS Y FINANZAS     -->
    <!-- ============================= -->
    <section class="report-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="report-section-title">
                <i class="fa-solid fa-wallet me-2"></i> Reportes de Gastos y Caja
            </h3>
            <span class="badge bg-soft-danger text-danger">
                Control financiero
            </span>
        </div>

        <div class="row g-4">
            <!-- Reporte de gastos -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-danger text-danger mb-3">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="report-body">
                        <h5>Gastos por período</h5>
                        <p>
                            Detalle de gastos por tipo, origen y período, con totales y resúmenes.
                        </p>
                        <a href="index.php?page=reporte_gastos" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de caja mensual -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-success text-success mb-3">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div class="report-body">
                        <h5>Movimientos de caja</h5>
                        <p>
                            Visualiza ingresos, egresos y balance de caja en un mes determinado.
                        </p>
                        <a href="index.php?page=reporte_caja_mensual" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================= -->
    <!-- BLOQUE: CLIENTES              -->
    <!-- ============================= -->
    <section class="report-section mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="report-section-title">
                <i class="fa-solid fa-users me-2"></i> Reportes de Clientes
            </h3>
            <span class="badge bg-soft-info text-info">
                Fidelización
            </span>
        </div>

        <div class="row g-4">
            <!-- Clientes más frecuentes -->
            <div class="col-md-6 col-lg-4">
                <div class="report-block h-100">
                    <div class="report-icon bg-soft-primary text-primary mb-3">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div class="report-body">
                        <h5>Clientes más frecuentes</h5>
                        <p>
                            Identifica a los clientes con mayor cantidad de operaciones para campañas de fidelización.
                        </p>
                        <a href="index.php?page=reporte_clientes_frecuentes" class="report-btn">
                            Ver reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
