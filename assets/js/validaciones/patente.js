function validate_patente(event){
    console.log(event.target.value);
    $.ajax({
        url: "controladores/vehiculos/patente.ajax.controlador.php",
        type: 'post',
        data: {
            'patente': event.target.value,
            'action': 'ajax'
        },
        success: function(response){
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La Patente ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('id_patente').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })

    
}