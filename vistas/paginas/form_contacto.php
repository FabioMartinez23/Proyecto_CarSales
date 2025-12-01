<section class="contacto-form-wrapper container hacer_padding">

    <h1 class="form-title">Formulario de Contacto</h1>

    <div class="contacto-card">

        <form id="formulario-contacto" action="controladores/email/form_email.controlador.php" method="POST">

            <div class="row">

                <!-- Nombre -->
                <div class="col-md-6">
                    <div class="input-box">
                        <label for="nombre">Nombre y Apellido</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Escribe tu Nombre y Apellido">
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <div class="input-box">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required placeholder="ejemplo@gmail.com">
                    </div>
                </div>

            </div>

            <div class="row">

                <!-- Teléfono -->
                <div class="col-md-6">
                    <div class="input-box">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" required placeholder="+54 ...">
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="col-md-6">
                    <div class="input-box">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="4" required placeholder="Escriba su consulta..."></textarea>
                    </div>
                </div>

            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn-enviar">
                    Enviar Consulta
                </button>
            </div>

        </form>

    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const element = document.getElementById('telefono');

    const maskOptions = {
        mask: [
            {
                mask: '+{54} 9 0000 0000',     // Formato sin guiones → +54 9 3704 1234
            },
            {
                mask: '+{54} 0000 000000',     // Variante tradicional → +54 370 4123456
            }
        ]
    };

    const mask = IMask(element, maskOptions);
});
</script>


