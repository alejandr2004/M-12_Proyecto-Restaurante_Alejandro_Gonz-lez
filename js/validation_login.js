    document.getElementById("codigo_empleado").onblur = validaCodigoCamarero;
    document.getElementById("pwd").onblur = validaPassword;
    document.getElementById("loginForm").onsubmit = validaForm;
    
    function validaCodigoCamarero() {
    let codigo_empleado = document.getElementById("codigo_empleado").value;
    let input_empleado = document.getElementById("codigo_empleado");
    let codigoError = document.getElementById("codigo_empleado_error");

    if(codigo_empleado === "" || codigo_empleado === null){
        codigoError.textContent = "El código de empleado es obligatorio.";
        input_empleado.classList.add("error-border");
        return false;
    } else if(codigo_empleado.length < 4){
        codigoError.textContent = "El código de empleado debe tener 4 caracteres mínimo.";
        input_empleado.classList.add("error-border");
        return false;
    } else {
        codigoError.textContent = "";
        input_empleado.classList.remove("error-border");
        return true;
    }
    }

    function validaPassword() {
    let pwd = document.getElementById("pwd").value;
    let input_pwd = document.getElementById("pwd");
    let pwdError = document.getElementById("pwd_error");

    if(pwd === "" || pwd === null){
        pwdError.textContent = "La contraseña es obligatoria.";
        input_pwd.classList.add("error-border");
        return false;
    } else if(pwd.length < 8){
        pwdError.textContent = "La contraseña debe tener 8 caracteres mínimo.";
        input_pwd.classList.add("error-border");
        return false;
    } else if(!pwd.match(/[A-Z]/) || !pwd.match(/[a-z]/) || !pwd.match(/[0-9]/)){
        pwdError.textContent = "La contraseña debe contener al menos una letra mayúscula o minúscula y un número.";
        input_pwd.classList.add("error-border");
        return false;
    } else {
        pwdError.textContent = "";
        input_pwd.classList.remove("error-border");
        return true;
    }
    }

    function validaForm(event) {
    event.preventDefault();
    if (validaCodigoCamarero() && validaPassword()) {
        document.getElementById("loginForm").submit(); 
    }
    }