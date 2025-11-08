let nombre;
let apellido;
let correo;
let nuevaContraseña;
let antiguaContraseña;

let nombreSpan = document.querySelector("#nombre>span");
let apellidoSpan = document.querySelector("#apellido>span");

document.addEventListener("DOMContentLoaded", async () => {
    const credentials = await fetch("/api/perfil/credentials.php").then((response) => {
        return response.json(); 
    });

    if (credentials.status == "success") {
        const usuario = credentials.result;
        nombre = usuario.nombre;
        apellido = usuario.apellido;
        correo = usuario.correo;
    } else {
        alert("No se ha podido cargar los datos del usuario");
        window.location = "/";
    }

    nombreSpan.textContent = nombre;
    apellidoSpan.textContent = apellido;
});

document.addEventListener('blur', function(event) {
    const elementoQuePerdioFoco = event.target;

    if (elementoQuePerdioFoco.tagName.toLowerCase() === 'input') {
        setTimeout(() => {
            nombreSpan = document.querySelector("#nombre>span");
            apellidoSpan = document.querySelector("#apellido>span");

            if (document.querySelector("#antiguaContraseña")?.value && document.querySelector("#nuevaContraseña")?.value) {
                antiguaContraseña = document.querySelector("#antiguaContraseña").value;
                nuevaContraseña = document.querySelector("#nuevaContraseña").value;
            }
        }, 0);
    }
}, true);

document.querySelector("#pass").addEventListener("click", () => {
    const parent = document.querySelector(".profile-body");

    parent.innerHTML += `<div class="row">
                        <h3>Antigua Contraseña: </h3>
                        <input id="antiguaContraseña" type="password" placeholder="********" required>
                    </div>
                    <div class="row">
                        <h3>Nueva Contraseña: </h3>
                        <input id="nuevaContraseña" type="password" placeholder="********" required>
                    </div>`;
    document.querySelector("#pass").remove();
});

document.querySelector("#closesessBtn").addEventListener("click", async () => {
    await fetch("/api/perfil/logout.php");
    window.location = "/";
});

document.querySelector("#delaccBtn").addEventListener("click", async () => {
    await fetch("/api/perfil/delete.php", {method: "POST"});
    window.location = "/";
});

document.querySelector("#save").addEventListener("click", async () => {
    let cambios;
    if (document.querySelector("#antiguaContraseña")?.value && document.querySelector("#nuevaContraseña")?.value) {
        cambios = {
            nombre: nombreSpan.textContent,
            apellido: apellidoSpan.textContent,
            contraseña: antiguaContraseña,
            newcontraseña: nuevaContraseña
        };
    } else {
        cambios = {
            nombre: nombreSpan.textContent,
            apellido: apellidoSpan.textContent
        };
    }

    const response = await fetch("/api/perfil/modify.php", {
        method: "POST",
        body: JSON.stringify(cambios)
    }).then((response) => {
        return response.json();
    });

    if (response.status == "success") {
        window.location = "/";
    } else {
        let errMessage = "";
        response.ErrDetails.forEach(err => {
            errMessage += err + " ";
        });

        alert(errMessage);
    }
});