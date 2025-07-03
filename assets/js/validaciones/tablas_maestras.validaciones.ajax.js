// VALIDAR EXISTENCIA

// PERFIL
function validate_perfil(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'perfil.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Perfil ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idperfiles').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// MODULO
function validate_modulo(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'modulo.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Modulo ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idmodulos').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}


// TIPO SEXO
function validate_tipo_sexo(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_sexo.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Sexo ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_sexo').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}


// MARCA
function validate_marca(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'nombre': event.target.value,
            'action': 'marca.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La marca ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idmarcas').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })

    
}


// TIPO VEHICULO
function validate_tipo_vehiculo(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'nombre': event.target.value,
            'action': 'tipo_vehiculo.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Vehiculo ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_vehiculos').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}


// TIPO DOCUMENTO
function validate_tipo_documento(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_documento.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Documento ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idTipo_documento').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}


// TIPO CONTACTO
function validate_tipo_contacto(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_contacto.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Contacto ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_contacto').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// MODELO VEHICULO
function validate_modelo_vehiculo(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'nombre': event.target.value,
            'action': 'modelo_vehiculo.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Modelo ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idmodelos').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// TIPO DOMICILIO
function validate_tipo_domicilio(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_domicilio.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Domicilio ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_domicilio').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}


//PAIS
function validate_pais(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'pais.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Pais ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idpaises').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// PROVINCIAS
function validate_provincia(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'provincia.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La Provincia ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idprovincias').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// LOCALIDADES
function validate_localidad(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'localidad.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La Localidad ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idlocalidades').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// BARRIOS
function validate_barrio(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'barrio.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Barrio ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idbarrios').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}



// TIPO DE PUESTO
function validate_tipo_puesto(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_puesto.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Puesto ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_de_puestos').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// COLOR
function validate_color(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'color.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Color ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idcolores').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// TIPO DE PAGO
function validate_tipo_pago(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_pago.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Pago ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_pago').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}

// TIPO DE ANULACION
function validate_tipo_anulacion(event){
    console.log(event.target.value);
    $.ajax({
        url: "../controladores/tablas_maestras/tablas_maestras.validaciones.ajax.php",
        type: 'post',
        data: {
            'descripcion': event.target.value,
            'action': 'tipo_anulacion.ajax'
        },
        success: function(response){
            console.log(response);
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Tipo de Anulación ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idtipo_anulacion').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })
    
}



// CAMPOS VACIOS


// PERFIL
function validar_vacio_perfil(){
    perfil = document.getElementById('idperfiles');
    id_perfil_parrafo = document.getElementById('id_perfil_parrafo');
    form = document.getElementById('id_form_perfil');

    if(perfil.value.length == 0){
        perfil.classList.add('validation-error')
        id_perfil_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// MODULO
function validar_vacio_modulo(){
    modulo = document.getElementById('idmodulos');
    id_modulo_parrafo = document.getElementById('id_modulo_parrafo');
    form = document.getElementById('id_form_modulo');

    if(modulo.value.length == 0){
        modulo.classList.add('validation-error')
        id_modulo_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// MARCA
function validar_vacio_marca(){
    marca = document.getElementById('idmarcas');
    id_marca_parrafo = document.getElementById('id_marca_parrafo');
    form = document.getElementById('id_form_marca');

    if(marca.value.length == 0){
        marca.classList.add('validation-error')
        id_marca_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}


// TIPO VEHICULO
function validar_vacio_tipo_vehiculo(){
    tipo_vehiculo = document.getElementById('idtipo_vehiculos');
    id_tipo_vehiculo_parrafo = document.getElementById('id_tipo_vehiculo_parrafo');
    form = document.getElementById('id_form_tipo_vehiculo');

    if(tipo_vehiculo.value.length == 0){
        tipo_vehiculo.classList.add('validation-error')
        id_tipo_vehiculo_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// TIPO SEXO

function validar_vacio_tipo_sexo(){
    tipo_sexo = document.getElementById('idtipo_sexo');
    id_tipo_sexo_parrafo = document.getElementById('id_tipo_sexo_parrafo');
    form = document.getElementById('id_form_tipo_sexo');

    if(tipo_sexo.value.length == 0){
        tipo_sexo.classList.add('validation-error')
        id_tipo_sexo_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}


// TIPO DOCUMENTO

function validar_vacio_tipo_documento(){
    tipo_documento = document.getElementById('idTipo_documento');
    id_tipo_documento_parrafo = document.getElementById('id_tipo_documento_parrafo');
    form = document.getElementById('id_form_tipo_documento');

    if(tipo_documento.value.length == 0){
        tipo_documento.classList.add('validation-error')
        id_tipo_documento_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}


// TIPO CONTACTO
function validar_vacio_tipo_contacto(){
    contacto = document.getElementById('idtipo_contacto');
    id_contacto_parrafo = document.getElementById('id_tipo_contacto_parrafo');
    form = document.getElementById('id_form_tipo_contacto');

    if(contacto.value.length == 0){
        contacto.classList.add('validation-error')
        id_contacto_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// MODELO DE VEHICULO
function validar_vacio_modelo(){
    modelo = document.getElementById('idmodelos');
    id_modelo_parrafo = document.getElementById('id_modelo_parrafo');
    form = document.getElementById('id_form_modelo');

    if(modelo.value.length == 0){
        modelo.classList.add('validation-error')
        id_modelo_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// TIPO DOMICILIO
function validar_vacio_tipo_domicilio(){
    domicilio = document.getElementById('idtipo_domicilio');
    id_domicilio_parrafo = document.getElementById('id_tipo_domicilio_parrafo');
    form = document.getElementById('id_form_tipo_domicilio');

    if(domicilio.value.length == 0){
        domicilio.classList.add('validation-error')
        id_domicilio_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// PAIS
function validar_vacio_pais(){
    pais = document.getElementById('idpaises');
    id_pais_parrafo = document.getElementById('id_pais_parrafo');
    form = document.getElementById('id_form_pais');

    if(pais.value.length == 0){
        pais.classList.add('validation-error')
        id_pais_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// PROVINCIAS
function validar_vacio_provincia(){
    provincia = document.getElementById('idprovincias');
    id_provincia_parrafo = document.getElementById('id_provincia_parrafo');
    form = document.getElementById('id_form_provincia');

    if(provincia.value.length == 0){
        provincia.classList.add('validation-error')
        id_provincia_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// LOCALIDADES
function validar_vacio_localidad(){
    localidad = document.getElementById('idlocalidades');
    id_localidad_parrafo = document.getElementById('id_localidad_parrafo');
    form = document.getElementById('id_form_localidad');

    if(localidad.value.length == 0){
        localidad.classList.add('validation-error')
        id_localidad_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// BARRIOS
function validar_vacio_barrio(){
    barrio = document.getElementById('idbarrios');
    id_barrio_parrafo = document.getElementById('id_barrio_parrafo');
    form = document.getElementById('id_form_barrio');

    if(barrio.value.length == 0){
        barrio.classList.add('validation-error')
        id_barrio_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

//TIPO PUESTO
function validar_vacio_tipo_puesto(){
    tipo_puesto = document.getElementById('idtipo_de_puestos');
    id_tipo_puesto_parrafo = document.getElementById('id_tipo_puesto_parrafo');
    form = document.getElementById('id_form_tipo_puesto');
    if(tipo_puesto.value.length == 0){
        tipo_puesto.classList.add('validation-error')
        id_tipo_puesto_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// COLOR
function validar_vacio_color(){
    color = document.getElementById('idcolores');
    id_color_parrafo = document.getElementById('id_color_parrafo');
    form = document.getElementById('id_form_color');
    if(color.value.length == 0){
        color.classList.add('validation-error')
        id_color_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// TIPO DE PAGO
function validar_vacio_tipo_pago(){
    tipo_pago = document.getElementById('idtipo_pago');
    id_tipo_pago_parrafo = document.getElementById('id_tipo_pago_parrafo');
    form = document.getElementById('id_form_tipo_pago');

    if(tipo_pago.value.length == 0){
        tipo_pago.classList.add('validation-error')
        id_tipo_pago_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

// TIPO DE ANULACION
function validar_vacio_tipo_anulacion(){
    tipo_anulacion = document.getElementById('idtipo_anulacion');
    id_tipo_anulacion_parrafo = document.getElementById('id_tipo_anulacion_parrafo');
    form = document.getElementById('id_form_tipo_anulacion');

    if(tipo_anulacion.value.length == 0){
        tipo_anulacion.classList.add('validation-error')
        id_tipo_anulacion_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}