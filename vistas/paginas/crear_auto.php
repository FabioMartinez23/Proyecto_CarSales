<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Auto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success alert-dismissible fade hide" role="alert">
            <strong>Éxito!</strong> El auto se ha creado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <h1 class="text-center">Formulario para Crear Auto</h1>
        <form id="autoForm" action="procesar.php" method="POST">
            <div class="mb-3">
                <label for="marca" class="form-label">Marca:</label>
                <input type="text" class="form-control" id="marca" name="marca" required>
            </div>
            <div class="mb-3">
                <label for="modelo" class="form-label">Modelo:</label>
                <input type="text" class="form-control" id="modelo" name="modelo" required>
            </div>
            <div class="mb-3">
                <label for="anio" class="form-label">Año:</label>
                <input type="number" class="form-control" id="anio" name="anio" min="1886" max="2024" required>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color:</label>
                <input type="text" class="form-control" id="color" name="color" required>
            </div>
            <button type="submit" class="btn btn-primary">Crear Auto</button>
            <a type="button"  href="listar_autos.php" class="btn btn-warning mt-2 mb-2"><i class="fa fas-row-left"></i> Volver</a>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#autoForm').on('submit', function(event) {
                console.log('se ejecuta');

                $(".alert-success").removeClass("hide").addClass("show");
                event.preventDefault(); // Prevenir el envío por defecto
                setTimeout(function(){
                    $(".alert-success").removeClass("show").addClass("hide");
                }, 3000);//se oculta la alerta despues de 3 segundos
                
                // Aquí puedes agregar cualquier validación adicional si lo deseas

                // Enviar el formulario a procesar.php
                // this.submit();
            });
        });
    </script>
</body>
</html>