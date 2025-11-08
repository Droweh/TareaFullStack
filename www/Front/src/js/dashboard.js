const addContainer = document.querySelector(".addContainer");
const tableroLista = document.querySelector("#lista");

let tableros = [];

document.addEventListener("DOMContentLoaded", async () => {
    const response = await fetch("/api/tablero/getTableros.php").then((response) => {
        return response.json();
    });

    if (response.status == "success") {
        tableros = response.result.result;
        renderizarTableros()
    } else {
        alert("Hubo un error al solicitar los tableros");
        window.location = "/";
    }
});

function renderizarTableros() {
    tableroLista.innerHTML = '';
    
    tableros.forEach(tablero => {
        const tableroElement = document.createElement("div");
        tableroElement.classList.add("container");
        tableroElement.dataset.id = tablero.id;
        
        tableroElement.innerHTML = `
            <div class="tablero-info">
                <div class="tablero-detalles">
                    <a href="workspace.html?tablero=${tablero.id}">
                        <span class="tablero-titulo">${tablero.nombre}</span>
                    </a>
                    <span class="tablero-desc">${tablero.descripcion}</span>
                </div>
            </div>
            <div class="tablero-acciones">
                <button class="editar">✏️</button>
                <button class="eliminar">🗑️</button>
            </div>
        `;

        tableroElement.querySelector(".editar").addEventListener("click", () => editarTablero(tableroElement));
        tableroElement.querySelector(".eliminar").addEventListener("click", () => eliminarTablero(tableroElement));

        tableroLista.appendChild(tableroElement);
    });

    const nuevoAddBtn = document.createElement("div");
    nuevoAddBtn.classList.add("addContainer");
    nuevoAddBtn.textContent = "+";
    tableroLista.appendChild(nuevoAddBtn);
    nuevoAddBtn.addEventListener("click", () => addTablero(tableroLista));
}

async function eliminarTablero(tableroElement) {
    const tableroId = tableroElement.dataset.id;
    
    if (confirm("¿Estás seguro de que quieres eliminar este tablero?")) {
        const response = await fetch("/api/tablero/eliminarTablero.php", {
            method: "POST",
            body: JSON.stringify({
                id: tableroId
            })
        }).then((response) => {
            return response.json();
        });

        if (response.status === "success") {
            tableros = tableros.filter(t => t.id != tableroId);
            tableroElement.remove();
        } else {
            alert("Hubo un error al eliminar el tablero");
        }
    }
}

function addTablero(lista) {
    const addBtn = lista.querySelector(".addContainer");
    if (addBtn) addBtn.remove();

    const tablero = document.createElement("div");
    tablero.classList.add("container");
    tablero.id = "creando";
    tablero.innerHTML = `
        <div class="tablero-info">
            <div class="tablero-detalles">
                <input class="tablero-titulo-input" placeholder="Nuevo tablero..." />
                <input class="tablero-desc-input" placeholder="Descripción del tablero..." />
            </div>
        </div>
        <div class="tablero-acciones">
            <button class="editar">✏️</button>
            <button class="eliminar">🗑️</button>
        </div>
    `;

    lista.appendChild(tablero);

    const nuevoAddBtn = document.createElement("div");
    nuevoAddBtn.classList.add("addContainer");
    nuevoAddBtn.textContent = "+";
    lista.appendChild(nuevoAddBtn);
    nuevoAddBtn.addEventListener("click", () => addTablero(lista));

    const tituloInput = tablero.querySelector(".tablero-titulo-input");
    const descInput = tablero.querySelector(".tablero-desc-input");
    tituloInput.focus();

    let isCreating = true;

    const finalizarCreacion = async () => {
        if (!isCreating) return;
        isCreating = false;

        if (tituloInput.value.trim()) {
            const nuevoTablero = {
                id: Date.now(),
                nombre: tituloInput.value.trim(),
                descripcion: descInput.value.trim()
            };

            tablero.querySelector(".tablero-detalles").innerHTML = `
                <a href="workspace.html?tablero=${nuevoTablero.id}">
                    <span class="tablero-titulo">${nuevoTablero.nombre}</span>
                </a>
                <span class="tablero-desc">${nuevoTablero.descripcion}</span>`;
            tablero.id = "";
            tablero.dataset.id = nuevoTablero.id;
            tablero.querySelector(".editar").addEventListener("click", () => editarTablero(tablero));
            tablero.querySelector(".eliminar").addEventListener("click", () => eliminarTablero(tablero));

            const response = await fetch("/api/tablero/agregarTablero.php", {
                method: "POST",
                body: JSON.stringify({
                    nombre: tituloInput.value.trim(),
                    descripcion: descInput.value.trim()
                })
            }).then((response) => {
                return response.json();
            });

            if (response.status === "success") {
                if (response.result && response.result.id) {
                    tablero.dataset.id = response.result.id;
                    nuevoTablero.id = response.result.id;
                }
                tableros.push(nuevoTablero);
            } else {
                alert("Hubo un error al agregar el tablero");
                tablero.remove();
            }
        } else {
            tablero.remove();
        }
    };

    tituloInput.addEventListener("blur", (e) => {
        setTimeout(() => {
            if (document.activeElement !== descInput) {
                finalizarCreacion();
            }
        }, 100);
    });

    descInput.addEventListener("blur", (e) => {
        setTimeout(() => {
            if (document.activeElement !== tituloInput) {
                finalizarCreacion();
            }
        }, 100);
    });

    tituloInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            descInput.focus();
        }
    });

    descInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            finalizarCreacion();
        }
    });
}

function editarTablero(tablero) {
    const detalles = tablero.querySelector(".tablero-detalles");
    const titulo = detalles.querySelector(".tablero-titulo").textContent;
    const descripcion = detalles.querySelector(".tablero-desc").textContent;
    const tableroId = tablero.dataset.id;
    
    detalles.innerHTML = `
        <input class="tablero-titulo-input" value="${titulo}" />
        <input class="tablero-desc-input" value="${descripcion}" />`;
    tablero.id = "editando";

    const tituloInput = detalles.querySelector(".tablero-titulo-input");
    const descInput = detalles.querySelector(".tablero-desc-input");
    tituloInput.focus();
    tituloInput.select();

    let isEditing = true;

    const finalizarEdicion = async () => {
        if (!isEditing) return;
        isEditing = false;

        const nuevoTitulo = tituloInput.value.trim() || titulo;
        const nuevaDescripcion = descInput.value.trim();
        
        detalles.innerHTML = `
            <a href="workspace.html?tablero=${tableroId}">
                <span class="tablero-titulo">${nuevoTitulo}</span>
            </a>
            <span class="tablero-desc">${nuevaDescripcion}</span>`;
        tablero.id = "";
        tablero.querySelector(".editar").addEventListener("click", () => editarTablero(tablero));
        tablero.querySelector(".eliminar").addEventListener("click", () => eliminarTablero(tablero));

        if (nuevoTitulo !== titulo || nuevaDescripcion !== descripcion) {
            const response = await fetch("/api/tablero/editarTablero.php", {
                method: "POST",
                body: JSON.stringify({
                    id: tableroId,
                    nombre: nuevoTitulo,
                    descripcion: nuevaDescripcion
                })
            }).then((response) => {
                return response.json();
            });

            if (response.status !== "success") {
                alert("Hubo un error al editar el tablero");
            } else {
                const tableroIndex = tableros.findIndex(t => t.id == tableroId);
                if (tableroIndex !== -1) {
                    tableros[tableroIndex].nombre = nuevoTitulo;
                    tableros[tableroIndex].descripcion = nuevaDescripcion;
                }
            }
        }
    };

    tituloInput.addEventListener("blur", (e) => {
        setTimeout(() => {
            if (document.activeElement !== descInput) {
                finalizarEdicion();
            }
        }, 100);
    });

    descInput.addEventListener("blur", (e) => {
        setTimeout(() => {
            if (document.activeElement !== tituloInput) {
                finalizarEdicion();
            }
        }, 100);
    });

    tituloInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            descInput.focus();
        }
    });

    descInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            finalizarEdicion();
        }
    });
}

addContainer.addEventListener("click", () => addTablero(tableroLista));