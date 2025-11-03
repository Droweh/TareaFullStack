const addContainer = document.querySelector(".addContainer");
const tableroLista = document.querySelector("#lista");

function addTablero(lista) {
    lista.querySelector(".addContainer").remove();
    lista.innerHTML += `<div id="creando" class="container">
            <div class="tablero-info">
                <div class="tablero-detalles">
                <input class="tablero-titulo"></input>
                </div>
            </div>
            <div class="tablero-acciones">
                <button class="editar">✏️</button>
                <button class="eliminar">🗑️</button>
            </div>
        </div>
        <div class="addContainer">+</div>`;
    const tablero = lista.querySelector("#creando");
    const input = tablero.querySelector(".tablero-detalles>input");
    input.focus();
    
    input.addEventListener("blur", () => {
        if (input.value) {
            tablero.querySelector(".tablero-detalles").innerHTML = `<a href="workspace.html">
                        <span class="tablero-titulo">${input.value}</span>
                        </a>`;
            tablero.id = "";
            tablero.querySelector(".editar").addEventListener("click", () => {
                editarTablero(tablero);
            });
            tablero.querySelector(".eliminar").addEventListener("click", () => {tablero.remove()});
        } else {
            try {
                tablero.remove();
            } catch {
                
            }
        }
    });

    input.addEventListener("keydown", (e) => {
        if (e.key == "Enter") {
            if (input.value) {
                tablero.querySelector(".tablero-detalles").innerHTML = `<a href="workspace.html">
                            <span class="tablero-titulo">${input.value}</span>
                            </a>`;
                tablero.id = "";
                tablero.querySelector(".editar").addEventListener("click", () => {
                    editarTablero(tablero);
                });
                tablero.querySelector(".eliminar").addEventListener("click", () => {tablero.remove()});
            } else {
                try {
                    tablero.remove();
                } catch {
                    
                }
            }
        }
    });
    
    lista.querySelector(".addContainer").addEventListener("click", () => {addTablero(lista)});
}

function editarTablero(tablero) {
    const detalles = tablero.querySelector(".tablero-detalles");
    const titulo = detalles.querySelector("span").textContent;
    detalles.innerHTML = `<input class="tablero-titulo" value="${titulo}"></input>`;
    tablero.id = "editando";
    const input = tablero.querySelector(".tablero-detalles>input");
    input.focus();

    input.addEventListener("blur", () => {
        tablero.querySelector(".tablero-detalles").innerHTML = `<a href="workspace.html">
                    <span class="tablero-titulo">${input.value || titulo}</span>
                    </a>`;
        tablero.id = "";
        tablero.querySelector(".editar").addEventListener("click", () => {
            editarTablero(tablero);
        });
        tablero.querySelector(".eliminar").addEventListener("click", () => {tablero.remove()});
    });

    input.addEventListener("keydown", (e) => {
        if (e.key == "Enter") {
            tablero.querySelector(".tablero-detalles").innerHTML = `<a href="workspace.html">
                        <span class="tablero-titulo">${input.value || titulo}</span>
                        </a>`;
            tablero.id = "";
            tablero.querySelector(".editar").addEventListener("click", () => {
                editarTablero(tablero);
            });
            tablero.querySelector(".eliminar").addEventListener("click", () => {tablero.remove()});
        }
    });
}

addContainer.addEventListener("click", () => {addTablero(tableroLista)});