const nombre = document.querySelector("input#nombre");
const apellido = document.querySelector("input#apellido");
const correo = document.querySelector("input#correo");
const contraseña = document.querySelector("input#contraseña");

document.querySelector("form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const accion = await fetch("/api/perfil/register.php", {
        method: "POST",
        body: JSON.stringify({
            nombre: nombre.value,
            apellido: apellido.value,
            correo: correo.value,
            contraseña: contraseña.value
        })
    }).then((respuesta) => {
        return respuesta.json();
    });

    if (accion.status == "success") {
        alert("Cuenta registrada exitosamente");
    } else {
        alert("Hubo un error al registrar su cuenta");
    }
});