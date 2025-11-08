const correo = document.querySelector("input#correo");
const contraseña = document.querySelector("input#contraseña");

document.querySelector("form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const accion = await fetch("/api/perfil/login.php", {
        method: "POST",
        body: JSON.stringify({
            correo: correo.value,
            contraseña: contraseña.value
        })
    }).then((respuesta) => {
        return respuesta.json();
    });

    if (accion.status == "success") {
        alert("Sesion iniciada exitosamente");
        window.location = "/Front/index/index.html";
    } else {
        alert(accion.ErrDetails[0]);
    }
});