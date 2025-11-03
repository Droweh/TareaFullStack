const perfil = document.querySelector("a#perfil");

document.addEventListener("DOMContentLoaded", async () => {
    const accion = await fetch("/api/perfil/credentials.php").then((respuesta) => {
        return respuesta.json();
    });

    if (accion.status == "success") {
        perfil.textContent = "Perfil";
        perfil.href = "../perfil/perfil.html";
    }
});