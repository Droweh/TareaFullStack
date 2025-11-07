const addContainer = document.querySelector(".addContainer");
const tableroLista = document.querySelector("#lista");

function addTablero(lista) {
    // eliminar el botón + temporalmente
    const addBtn = lista.querySelector(".addContainer");
    if (addBtn) addBtn.remove();

    // crear el nuevo tablero
    const tablero = document.createElement("div");
    tablero.classList.add("container");
    tablero.id = "creando";
    tablero.innerHTML = `
        <div class="tablero-info">
            <div class="tablero-detalles">
                <input class="tablero-titulo" placeholder="Nuevo tablero..." />
            </div>
        </div>
        <div class="tablero-acciones">
            <button class="editar">✏️</button>
            <button class="eliminar">🗑️</button>
        </div>
    `;

    // agregar tablero al DOM
    lista.appendChild(tablero);

    // volver a agregar el botón +
    const nuevoAddBtn = document.createElement("div");
    nuevoAddBtn.classList.add("addContainer");
    nuevoAddBtn.textContent = "+";
    lista.appendChild(nuevoAddBtn);
    nuevoAddBtn.addEventListener("click", () => addTablero(lista));

    const input = tablero.querySelector(".tablero-titulo");
    input.focus();

    const finalizarCreacion = () => {
        if (input.value.trim()) {
            tablero.querySelector(".tablero-detalles").innerHTML = `
                <a href="workspace.html">
                    <span class="tablero-titulo">${input.value.trim()}</span>
                </a>`;
            tablero.id = "";
            tablero.querySelector(".editar").addEventListener("click", () => editarTablero(tablero));
            tablero.querySelector(".eliminar").addEventListener("click", () => tablero.remove());
        } else {
            tablero.remove();
        }
    };

    input.addEventListener("blur", finalizarCreacion);
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") finalizarCreacion();
    });
}

function editarTablero(tablero) {
    const detalles = tablero.querySelector(".tablero-detalles");
    const titulo = detalles.querySelector("span").textContent;
    detalles.innerHTML = `<input class="tablero-titulo" value="${titulo}" />`;
    tablero.id = "editando";

    const input = tablero.querySelector(".tablero-titulo");
    input.focus();

    const finalizarEdicion = () => {
        const nuevoTitulo = input.value.trim() || titulo;
        detalles.innerHTML = `
            <a href="workspace.html">
                <span class="tablero-titulo">${nuevoTitulo}</span>
            </a>`;
        tablero.id = "";
        tablero.querySelector(".editar").addEventListener("click", () => editarTablero(tablero));
        tablero.querySelector(".eliminar").addEventListener("click", () => tablero.remove());
    };

    input.addEventListener("blur", finalizarEdicion);
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") finalizarEdicion();
    });
}

// botón principal +
addContainer.addEventListener("click", () => addTablero(tableroLista));
