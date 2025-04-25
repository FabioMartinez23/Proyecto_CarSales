function validate_email(event){
    console.log(event.target.value);
    $.ajax({
        url: "controladores/usuarios/email.ajax.controlador.php",
        type: 'post',
        data: {
            'email': event.target.value,
            'action': 'ajax'
        },
        success: function(response){
            let data = JSON.parse(response);
            if(data.data == 'error'){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El Email ya existe.',
                    showConfirmButton: true
                });
                document.getElementById('idemail').value = '';
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(textStatus, errorThrown);
        }
    })

    
}