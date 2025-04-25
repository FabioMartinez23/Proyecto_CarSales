function validate_username(event){
    console.log(event.target.value);
    $.ajax({
        url: "controladores/usuarios/usuarios.ajax.controlador.php",
        type: 'post',
        data: {
            'username': event.target.value,
            'action': 'ajax'
        },
        success: function(response){
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Usuario ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('username').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })

    
}

