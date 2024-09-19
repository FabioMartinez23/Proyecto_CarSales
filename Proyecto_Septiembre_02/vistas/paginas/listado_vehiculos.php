<?php

ini_set('display_errors', 1);

$total_registros = 0;

$vehiculo = new Vehiculos();
$todos_vehiculos = $vehiculo->traer_todos_vehiculos();



$vehiculos = new Vehiculos();
$result_vehiculos_ = $vehiculos->traer_cantidad_vehiculo();
foreach($result_vehiculos_ as $vehiculo_1){
    $total_registros = $vehiculo_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $vehiculos->pagina_actual = $_GET['pagina_actual'];
}
$result_vehiculos = $vehiculos->traer_vehiculos();

?>

<div class="row">
    <div class="col">
        <h2>Registrar Vehiculo</h2>
        <form method="POST" action="controladores/vehiculos/vehiculos.controlador.php">
        <input type="hidden" name="action" value="guardar">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Patente</label>
                <input type="text" name="patente" onfocusout="validate_patente(event)" class="form-control" id="id_patente" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Chasis</label>
                <input type="text" name="chasis" onfocusout="validate_chasis(event)" class="form-control" id="id_chasis" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Motor</label>
                <input type="text" name="motor" onfocusout="validate_motor(event)" class="form-control" id="id_motor" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Año</label>
                <input type="text" name="año" onfocusout="validate_año(event)" class="form-control" id="id_año" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Color</label>
                <select name="idcolores" id="idcolores" class="form-select">
                    <option value="">Seleccione un Color</option>
                <?php
                    foreach($todos_vehiculos as $vehiculo){
                ?>
                    <option value="<?php echo $vehiculo['idcolores']?>"><?php echo $vehiculo['nombre_color']?></option>
                <?php
                    }
                ?>
                </select>
            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Marca</label>
                <select name="idmarcas" id="idmarcas" onchange="validar_marca(this.value)" class="form-select">
                    <option value="">Seleccione una Marca</option>
                <?php
                    foreach($todos_vehiculos as $vehiculo){
                ?>
                    <option value="<?php echo $vehiculo['idmarcas']?>"><?php echo $vehiculo['nombre_marca']?></option>
                <?php
                    }
                ?>
                </select>

            </div>
            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Modelo</label>
                <select name="idmodelos" id="idmodelos" class="form-select">
                    <option value="">Seleccione un Modelo</option>
                <?php
                    foreach($todos_vehiculos as $vehiculo){
                ?>
                    <option value="<?php echo $vehiculo['idmodelos']?>"><?php echo $vehiculo['nombre_modelo']?></option>
                <?php
                    }
                ?>
                </select>

            </div>
            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Tipo</label>
                <select name="idtipo_vehiculos" id="idtipo_vehiculos" class="form-select">
                    <option value="">Seleccione un Tipo</option>
                <?php
                    foreach($todos_vehiculos as $vehiculo){
                ?>
                    <option value="<?php echo $vehiculo['idtipo_vehiculos']?>"><?php echo $vehiculo['nombre_tipo_vehiculo']?></option>
                <?php
                    }
                ?>
                </select>

            </div>
            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Usuarios</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Patente</th>
                <th>Chasis</th>
                <th>Motor</th>
                <th>Año</th>
                <th>Color</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Tipo</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_vehiculos as $vehiculo_){
                ?>
                <tr>
                <td><?= $vehiculo_['patente']; ?></td>
                <td><?= $vehiculo_['chasis']; ?></td>
                <td><?= $vehiculo_['motor']; ?></td>
                <td><?= $vehiculo_['año']; ?></td>
                <td><?= $vehiculo_['nombre_color']; ?></td>
                <td><?= $vehiculo_['nombre_marca']; ?></td>
                <td><?= $vehiculo_['nombre_modelo']; ?></td>
                <td><?= $vehiculo_['nombre_tipo']; ?></td>
                <td>
                    <a href="index.php?page=listado_usuarios&idusuarios=<?=$usuario_['idusuarios']; ?>&nombre=<?=$usuario_['username'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>
                <td>
                    <form method="POST" action="controladores/usuarios/usuarios.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idusuarios" value="<?= $usuario_['idusuarios'] ?>">
                        <button onclick="return eliminar()" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>

        <nav aria-label="...">
            <ul class="pagination">
            <li class="page-item <?php if($vehiculos->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual - 1 ?>"><?= $vehiculos->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual ?>"><?= $vehiculos->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual + 1 ?>"><?= $vehiculos->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($vehiculos->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>

<script>

function validar_marca(idmarcas) {
    if(idmarcas === "") {
        document.getElementById("idmodelos").innerHTML = "<option value=''>Seleccione un Modelo</option>";
        return;
    }

    // Crear una solicitud AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "controladores/vehiculos/obtener_modelos_por_marca.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Definir qué hacer cuando el servidor responde
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Parsear la respuesta JSON
            var modelos = JSON.parse(xhr.responseText);

            // Vaciar el select de modelos
            var selectModelos = document.getElementById("idmodelos");
            selectModelos.innerHTML = "<option value=''>Seleccione un Modelo</option>"; 

            // Rellenar el select con los nuevos modelos
            modelos.forEach(function(modelo) {
                var option = document.createElement("option");
                option.value = modelo.idmodelos;
                option.text = modelo.nombre_modelo;
                selectModelos.appendChild(option);
            });
        }
    };

    // Enviar la solicitud con el idmarcas
    xhr.send("idmarcas=" + idmarcas);
}


</script>

