<main class="my-4">

<h3 class="text-center mb-4">¡Bienvenido a CarSales!</h3>

<section class="stats d-flex justify-content-between flex-wrap">
    <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_vehiculos'">
        <h2><?= $total_vehiculos_count ?></h2>
        <p>Vehículos Disponibles</p>
    </div>

    <div class="card text-center p-3">
        <h2><?= $mis_compras_count ?></h2>
        <p>Mis Compras</p>
    </div>

    <div class="card text-center p-3">
        <h2><?= $vehiculos_sugeridos ?></h2>
        <p>Recomendados Para Ti</p>
    </div>
</section>

</main>
