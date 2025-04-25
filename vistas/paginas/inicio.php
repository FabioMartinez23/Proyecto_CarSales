<?php
ini_set('display_errors', 0);

?>

<section id="encabezado" class="text-center py-5">
    <div>
        <h1 class="display-4">¡Tu próximo auto te espera aquí!</h1>
        <p class="lead">Encuentra vehículos usados con la mejor calidad y precios accesibles.</p>
        <a href="index.php?page=comprar_vehiculo" class="btn btn-action btn-lg">Explorar Vehículos</a>
    </div>
</section>

<section>
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" onclick="window.location.href='index.php?page=form_contacto'">
                <img  src="assets/img/carrusel_principal/1.jpg" class="d-block" alt="Banner 1">
            </div>
            <div class="carousel-item" onclick="window.location.href='index.php?page=comprar_vehiculo'">
                <img src="assets/img/carrusel_principal/2.jpg" class="d-block" alt="Banner 2">
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</section>

<section id="catalogo" class="py-5">
    <div>
        <h1 class="text-center mb-4">Vehículos Destacados</h1>
        <div class="row">
            <div class="col-md-4 catalog-item">
                <div class="card">
                    <img src="assets/img/carrusel_principal/NUEVO-COROLLA-XEI.jpg" class="card-img-top" alt="Auto 1">
                    <div class="card-body text-center">
                        <h5 class="card-title">Toyota Corolla 2020</h5>
                        <p class="card-text">$25.000.000</p>
                        <a href="#" class="btn btn-action">Ver Detalles</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 catalog-item">
                <div class="card">
                    <img src="assets/img/carrusel_principal/honda_civic.jpg" class="card-img-top" alt="Auto 2">
                    <div class="card-body text-center">
                        <h5 class="card-title">Honda Civic 2015</h5>
                        <p class="card-text">$18.000.000</p>
                        <a href="#" class="btn btn-action">Ver Detalles</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 catalog-item">
                <div class="card">
                    <img src="assets/img/carrusel_principal/clio_mio.jpg" class="card-img-top" alt="Auto 3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Renault Clio Mio 2013</h5>
                        <p class="card-text">$13.000.000</p>
                        <a href="#" class="btn btn-action">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<section id="testimonios" class="bg-light py-5">
    <div>
        <h2 class="text-center mb-4">Lo que dicen nuestros clientes</h2>
        <div class="row">
            <div class="col-md-4">
                <blockquote class="blockquote">
                    <p>"Excelente atención y los autos son de gran calidad."</p>
                    <footer class="blockquote-footer">Juan Pérez</footer>
                </blockquote>
            </div>
            <div class="col-md-4">
                <blockquote class="blockquote">
                    <p>"Gracias a ellos encontré el auto perfecto para mi familia."</p>
                    <footer class="blockquote-footer">María López</footer>
                </blockquote>
            </div>
            <div class="col-md-4">
                <blockquote class="blockquote">
                    <p>"¡Recomendados 100%! Los mejores precios y servicio."</p>
                    <footer class="blockquote-footer">Carlos Díaz</footer>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<style>
    /* Ajusta el alto del carrusel y de las imágenes */
    #bannerCarousel .carousel-item img {
        width: 100%;
        height: 500px; /* Altura fija para el carrusel */
        cursor: pointer;
    }

    /* Asegura que el carrusel ocupe todo el ancho del body */
    #bannerCarousel {
        width: 100vw; /* Ocupa todo el ancho de la ventana */
        max-width: 100%;
    }
</style>

<script>
    // Seleccionar todas las tarjetas
    const items = document.querySelectorAll('.catalog-item');

    // Configurar IntersectionObserver
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            const item = entry.target;

            if (entry.isIntersecting) {
                // Asignar retraso dinámico
                item.style.animationDelay = `${index * 0.2}s`;

                // Activar animación
                item.classList.add('is-visible');
            } else {
                // Resetear animación si sale del viewport
                item.classList.remove('is-visible');
            }
        });
    }, {
        threshold: 0.1 // Activar si el 10% del elemento es visible
    });

    // Observar cada tarjeta
    items.forEach(item => observer.observe(item));
</script>



