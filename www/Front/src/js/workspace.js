const workspace = document.querySelector(".listsContainer");
const addListBtn = document.getElementById("addListBtn");
let lists = [];
let currentTableroId = getTableroIdFromURL();

function getTableroIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('tablero');
}

document.addEventListener("DOMContentLoaded", async () => {
    if (!currentTableroId) {
        alert("No se encontró el tablero");
        window.location = "/";
        return;
    }

    await cargarListas();
});

document.querySelector("#link").addEventListener("click", async () => {
    const response = await fetch(`/api/tablero/getLinkInvitacion.php?tablero=${currentTableroId}`).then(response => response.json());

    if (response.status == "success") {
        try {
            await navigator.clipboard.writeText(response.result);
            alert("Link de Invitacion Copiado!");
        } catch (err) {
            alert("Hubo un error al copiar el link");
        }
    } else {
        alert(response.ErrMessage);
    }
});

async function cargarListas() {
    try {
        const response = await fetch(`/api/lista/getListas.php?tablero=${currentTableroId}`).then(response => response.json());
        
        if (response.status === "success") {
            lists = response.result || [];
            renderizarListas();
        } else {
            alert(response.ErrMessage);
            window.location = "/Front/workspace/dashboard.html";
        }
    } catch (error) {
        console.error("Error al cargar listas:", error);
    }
}

function renderizarListas() {
    workspace.innerHTML = '';
    
    lists.forEach(lista => {
        crearListaElemento(lista, false);
    });
}

function crearListaElemento(listaData, isNew = true) {
    const listElement = document.createElement("div");
    listElement.classList.add("list");

    const listHeader = document.createElement("header");
    listHeader.classList.add("listHeader");

    const titleContainer = document.createElement("div");

    if (isNew) {
        const headerInput = document.createElement("input");
        headerInput.type = "text";
        headerInput.placeholder = "Nombre de la lista...";
        headerInput.classList.add("titleInput");
        titleContainer.appendChild(headerInput);

        let isCreating = true;

        const guardarLista = async () => {
            if (!isCreating) return;
            isCreating = false;

            const nombre = headerInput.value.trim() || "Sin título";
            
            try {
                const response = await fetch("/api/lista/agregarLista.php", {
                    method: "POST",
                    body: JSON.stringify({
                        tableroId: currentTableroId,
                        nombre: nombre
                    })
                }).then(response => response.json());

                if (response.status === "success") {
                    listaData.lista = nombre;
                    
                    const titleElement = crearTituloEditable(nombre);
                    titleContainer.innerHTML = '';
                    titleContainer.appendChild(titleElement);
                    
                    lists.push(listaData);
                } else {
                    alert("Error al crear la lista");
                    listElement.remove();
                }
            } catch (error) {
                console.error("Error al crear lista:", error);
                listElement.remove();
            }
        };

        headerInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                guardarLista();
            }
        });

        headerInput.addEventListener("blur", () => {
            setTimeout(() => {
                guardarLista();
            }, 100);
        });

        headerInput.focus();
    } else {
        const titleElement = crearTituloEditable(listaData.lista);
        titleContainer.appendChild(titleElement);
    }

    const delListBtn = document.createElement("button");
    delListBtn.innerText = "X";
    delListBtn.addEventListener("click", () => eliminarLista(listElement));

    const listBody = document.createElement("div");
    listBody.classList.add("listBody");

    const listy = document.createElement("ul");
    listy.classList.add("tasks");

    if (!isNew && listaData.tareas) {
        listaData.tareas.forEach(tarea => {
            crearTareaElemento(tarea, listy, false);
        });
    }

    const addTaskBtn = document.createElement("button");
    addTaskBtn.classList.add("btn");
    addTaskBtn.innerText = "Agregar tarea";

    listHeader.appendChild(titleContainer);
    listHeader.appendChild(delListBtn);
    
    listElement.appendChild(listHeader);
    listElement.appendChild(listBody);
    listBody.appendChild(listy);
    listElement.appendChild(addTaskBtn);
    workspace.appendChild(listElement);

    addTaskBtn.addEventListener("click", () => {
        crearTareaElemento({
            descripcion: "Nueva tarea",
            completada: false
        }, listy, true);
    });

    return listElement;
}

function crearTituloEditable(texto) {
    const titleElement = document.createElement("p");
    titleElement.classList.add("listTitle");
    titleElement.textContent = texto;
    makeEditable(titleElement);
    return titleElement;
}

function makeEditable(titleElement) {
    titleElement.addEventListener("dblclick", () => {
        const currentText = titleElement.textContent;
        const titleContainer = titleElement.parentElement;
        
        const input = document.createElement("input");
        input.classList.add("titleInput");
        input.type = "text";
        input.value = currentText;
        input.classList.add("editTitleInput");

        titleContainer.innerHTML = '';
        titleContainer.appendChild(input);
        input.focus();
        input.select();

        let isEditing = true;

        const guardarTitulo = async () => {
            if (!isEditing) return;
            isEditing = false;
            
            const nuevoTitulo = input.value.trim() || "Sin título";
            
            try {
                const response = await fetch("/api/lista/editarLista.php", {
                    method: "POST",
                    body: JSON.stringify({
                        tableroId: currentTableroId,
                        titulo: currentText,
                        newTitulo: nuevoTitulo
                    })
                }).then(response => response.json());

                if (response.status === "success") {
                    const newTitle = crearTituloEditable(nuevoTitulo);
                    titleContainer.innerHTML = '';
                    titleContainer.appendChild(newTitle);

                    const listaIndex = lists.findIndex(l => l.lista == currentText);
                    if (listaIndex !== -1) {
                        lists[listaIndex].lista = nuevoTitulo;
                    }
                } else {
                    alert("Error al editar la lista");
                    const newTitle = crearTituloEditable(currentText);
                    titleContainer.innerHTML = '';
                    titleContainer.appendChild(newTitle);
                }
            } catch (error) {
                console.error("Error al editar lista:", error);
                const newTitle = crearTituloEditable(currentText);
                titleContainer.innerHTML = '';
                titleContainer.appendChild(newTitle);
            }
        };

        input.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                guardarTitulo();
            }
        });

        input.addEventListener("blur", () => {
            setTimeout(() => {
                guardarTitulo();
            }, 100);
        });
    });
}

function crearTareaElemento(tareaData, contenedorLista, isNew = true) {
    const newTask = document.createElement("li");

    const taskContainer = document.createElement("div");
    taskContainer.classList.add("task-container");

    const newCheckBox = document.createElement("input");
    newCheckBox.type = "checkbox";
    newCheckBox.checked = tareaData.estado || false;

    if (isNew) {
        const taskInput = document.createElement("input");
        taskInput.type = "text";
        taskInput.placeholder = "Nueva tarea...";
        taskInput.classList.add("taskInput");
        taskContainer.appendChild(taskInput);

        let isCreating = true;

        const guardarTarea = async () => {
            if (!isCreating) return;
            isCreating = false;

            const descripcion = taskInput.value.trim() || "Tarea sin título";
            
            try {
                const listaTitulo = contenedorLista.closest('.list').querySelector('.listTitle').textContent;
                const response = await fetch("/api/tarea/agregarTarea.php", {
                    method: "POST",
                    body: JSON.stringify({
                        lista: listaTitulo,
                        tableroId: currentTableroId,
                        titulo: descripcion
                    })
                }).then(response => response.json());

                if (response.status === "success") {
                    const taskSpan = document.createElement("span");
                    taskSpan.textContent = descripcion;
                    makeTaskEditable(taskSpan);
                    taskContainer.innerHTML = '';
                    taskContainer.appendChild(newCheckBox);
                    taskContainer.appendChild(taskSpan);
                    
                    const listaIndex = lists.findIndex(l => l.lista == listaTitulo);
                    if (listaIndex !== -1) {
                        if (!lists[listaIndex].tareas) lists[listaIndex].tareas = [];
                        lists[listaIndex].tareas.push({
                            descripcion: descripcion,
                            completada: false
                        });
                    }
                } else {
                    alert("Error al crear la tarea");
                    newTask.remove();
                }
            } catch (error) {
                console.error("Error al crear tarea:", error);
                newTask.remove();
            }
        };

        taskInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                guardarTarea();
            }
        });

        taskInput.addEventListener("blur", () => {
            setTimeout(() => {
                guardarTarea();
            }, 100);
        });

        taskInput.focus();
    } else {
        const taskSpan = document.createElement("span");
        taskSpan.textContent = tareaData.titulo;
        makeTaskEditable(taskSpan);
        taskContainer.appendChild(newCheckBox);
        taskContainer.appendChild(taskSpan);
    }

    const DelTaskBtn = document.createElement("button");
    DelTaskBtn.innerText = "X";
    DelTaskBtn.addEventListener("click", () => eliminarTarea(newTask));

    newTask.appendChild(taskContainer);
    newTask.appendChild(DelTaskBtn);
    contenedorLista.appendChild(newTask);

    newCheckBox.addEventListener("change", () => actualizarEstadoTarea(newTask, newCheckBox.checked));

    return newTask;
}

function makeTaskEditable(taskSpan) {
    taskSpan.addEventListener("click", () => {
        const currentText = taskSpan.textContent;
        const taskContainer = taskSpan.parentElement;
        const newTask = taskContainer.parentElement;
        const listaTitulo = newTask.closest('.list').querySelector('.listTitle').textContent;
        
        const input = document.createElement("input");
        input.type = "text";
        input.value = currentText;
        input.classList.add("taskInput");

        const checkbox = taskContainer.querySelector('input[type="checkbox"]');
        taskContainer.innerHTML = '';
        taskContainer.appendChild(checkbox);
        taskContainer.appendChild(input);
        input.focus();
        input.select();

        let isEditing = true;

        const guardarTarea = async () => {
            if (!isEditing) return;
            isEditing = false;
            
            const nuevaDescripcion = input.value.trim() || "Tarea sin título";
            
            try {
                const response = await fetch("/api/tarea/editarTarea.php", {
                    method: "POST",
                    body: JSON.stringify({
                        lista: listaTitulo,
                        tableroId: currentTableroId,
                        titulo: currentText,
                        newTitulo: nuevaDescripcion,
                        estado: checkbox.checked
                    })
                }).then(response => response.json());

                if (response.status === "success") {
                    const newSpan = document.createElement("span");
                    newSpan.textContent = nuevaDescripcion;
                    makeTaskEditable(newSpan);
                    taskContainer.innerHTML = '';
                    taskContainer.appendChild(checkbox);
                    taskContainer.appendChild(newSpan);

                    const listaIndex = lists.findIndex(l => l.lista == listaTitulo);
                    if (listaIndex !== -1 && lists[listaIndex].tareas) {
                        const tareaIndex = lists[listaIndex].tareas.findIndex(t => t.descripcion == currentText);
                        if (tareaIndex !== -1) {
                            lists[listaIndex].tareas[tareaIndex].descripcion = nuevaDescripcion;
                        }
                    }
                } else {
                    alert("Error al editar la tarea");
                    const newSpan = document.createElement("span");
                    newSpan.textContent = currentText;
                    makeTaskEditable(newSpan);
                    taskContainer.innerHTML = '';
                    taskContainer.appendChild(checkbox);
                    taskContainer.appendChild(newSpan);
                }
            } catch (error) {
                console.error("Error al editar tarea:", error);
                const newSpan = document.createElement("span");
                newSpan.textContent = currentText;
                makeTaskEditable(newSpan);
                taskContainer.innerHTML = '';
                taskContainer.appendChild(checkbox);
                taskContainer.appendChild(newSpan);
            }
        };

        input.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                guardarTarea();
            }
        });

        input.addEventListener("blur", () => {
            setTimeout(() => {
                guardarTarea();
            }, 100);
        });
    });
}

async function actualizarEstadoTarea(taskElement, completada) {
    const taskSpan = taskElement.querySelector('.task-container span');
    const descripcion = taskSpan.textContent;
    const listaTitulo = taskElement.closest('.list').querySelector('.listTitle').textContent;
    
    try {
        const response = await fetch("/api/tarea/editarTarea.php", {
            method: "POST",
            body: JSON.stringify({
                lista: listaTitulo,
                tableroId: currentTableroId,
                titulo: descripcion,
                estado: completada
            })
        }).then(response => response.json());

        if (response.status !== "success") {
            console.error("Error al actualizar tarea");
            const checkbox = taskElement.querySelector('input[type="checkbox"]');
            checkbox.checked = !completada;
        }
    } catch (error) {
        console.error("Error al actualizar tarea:", error);
        const checkbox = taskElement.querySelector('input[type="checkbox"]');
        checkbox.checked = !completada;
    }
}

async function eliminarLista(listElement) {
    const listaTitulo = listElement.querySelector(".listTitle").textContent;
    
    if (confirm("¿Estás seguro de que quieres eliminar esta lista y todas sus tareas?")) {
        try {
            const response = await fetch("/api/lista/eliminarLista.php", {
                method: "POST",
                body: JSON.stringify({
                    tableroId: currentTableroId,
                    titulo: listaTitulo
                })
            }).then(response => response.json());

            if (response.status === "success") {
                lists = lists.filter(l => l.lista != listaTitulo);
                listElement.remove();
            } else {
                alert("Error al eliminar la lista");
            }
        } catch (error) {
            console.error("Error al eliminar lista:", error);
            alert("Error al eliminar la lista");
        }
    }
}

async function eliminarTarea(taskElement) {
    const taskSpan = taskElement.querySelector('.task-container span');
    const descripcion = taskSpan.textContent;
    const listaTitulo = taskElement.closest('.list').querySelector('.listTitle').textContent;
    
    if (confirm("¿Estás seguro de que quieres eliminar esta tarea?")) {
        try {
            const response = await fetch("/api/tarea/eliminarTarea.php", {
                method: "POST",
                body: JSON.stringify({
                    lista: listaTitulo,
                    tableroId: currentTableroId,
                    titulo: descripcion
                })
            }).then(response => response.json());

            if (response.status === "success") {
                const listaIndex = lists.findIndex(l => l.lista == listaTitulo);
                if (listaIndex !== -1 && lists[listaIndex].tareas) {
                    lists[listaIndex].tareas = lists[listaIndex].tareas.filter(t => t.descripcion != descripcion);
                }
                taskElement.remove();
            } else {
                alert("Error al eliminar la tarea");
            }
        } catch (error) {
            console.error("Error al eliminar tarea:", error);
            alert("Error al eliminar la tarea");
        }
    }
}

function addList() {
    const nuevaLista = {
        lista: "",
        tareas: []
    };
    
    crearListaElemento(nuevaLista, true);
}

addListBtn.addEventListener("click", addList);