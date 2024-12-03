function showSweetAlert(icon, title, text) {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonText: 'Volver al inicio'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../public/dashboard.php';
        }
    });
}
