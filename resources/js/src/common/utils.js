export function showAlertError(swal, message){
    swal({
        icon: 'error',
        title: 'Oops...',
        text: message
    });
}

export function showAlertSuccess(swal, message){
    swal({
        icon: 'success',
        title: 'Success!',
        text: message
    });
}