function verificarEdad() {
    let fechaNacimiento = document.getElementById('id_fecha_nacimiento').value;
    let fechaNacimientoDate = new Date(fechaNacimiento);
    let hoy = new Date();
    let edad = hoy.getFullYear() - fechaNacimientoDate.getFullYear();
    
    // Ajustar por si la fecha de cumpleaños no ha ocurrido aún este año
    let mes = hoy.getMonth() - fechaNacimientoDate.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimientoDate.getDate())) {
        edad--;
    }
    
    if (edad < 18) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debes ser mayor de 18 años para registrarte.',
            showConfirmButton: true
        });
        document.getElementById('id_fecha_nacimiento').value = ''; // Limpia el campo si es inválido
    }
}

function validarFormulario() {
    let fechaNacimiento = document.getElementById('id_fecha_nacimiento').value;
    if (!fechaNacimiento) return true; // Si no hay fecha, no validar
    let fechaNacimientoDate = new Date(fechaNacimiento);
    let hoy = new Date();
    let edad = hoy.getFullYear() - fechaNacimientoDate.getFullYear();
    
    // Ajustar por si la fecha de cumpleaños no ha ocurrido aún este año
    let mes = hoy.getMonth() - fechaNacimientoDate.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimientoDate.getDate())) {
        edad--;
    }
    
    if (edad < 18) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debes ser mayor de 18 años para registrarte.',
            showConfirmButton: true
        });
        return false; // Detiene el envío del formulario
    }
    
    return true; // Permite el envío del formulario si la edad es válida
}
