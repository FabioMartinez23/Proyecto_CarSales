
function validate(){

    nombre_usuario = document.getElementById('username');
    password = document.getElementById('password');
    form = document.getElementById('id_form');

    id_usuario_parrafo = document.getElementById('id_usuario_parrafo');
    id_password_parrafo = document.getElementById('id_password_parrafo');

    if(nombre_usuario.value.length == 0){
        nombre_usuario.classList.add('validation-error')
        id_usuario_parrafo.style.display = 'block';
        return;
    }
    if(password.value.length == 0){
        password.classList.add('validation-error')
        id_password_parrafo.style.display = 'block';
        return;
    }
    form.submit();
}

