
<div class="hacer_padding">
    <h1 class="text-center">Formulario de Contacto</h1>
    <form id="formulario-contacto" action="controladores/email/form_email.controlador.php" method="POST">
        <!-- Campos del formulario -->
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre" class="form-label-list">Nombre y Apellido:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label-list">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="telefono" class="form-label-list">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="observaciones" class="form-label-list">Observaciones:</label>
                    <textarea id="observaciones" name="observaciones" class="form-control" rows="4" required></textarea>
                </div>
            </div>
        </div>

        <!-- Botón de envío -->
        <button type="submit" class="btn btn-action">Enviar</button>
    </form>
</div>

