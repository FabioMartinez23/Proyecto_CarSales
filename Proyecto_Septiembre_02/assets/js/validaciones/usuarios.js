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
                alert('el usuario ya existe');
                document.getElementById('idusuarios').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })

    
}

