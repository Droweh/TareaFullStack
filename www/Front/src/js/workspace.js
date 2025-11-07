const workspace = document.querySelector(".listsContainer");
let lists = [];

function makeEditable(titleElement) {
    titleElement.addEventListener("dblclick", () => {
        const currentText = titleElement.textContent;
        const input = document.createElement("input");
        input.classList.add("titleInput")
        input.type = "text";
        input.value = currentText;
        input.classList.add("editTitleInput");

        titleElement.replaceWith(input);
        input.focus();

        input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            const newTitle = document.createElement("p");
            newTitle.classList.add("listTitle");
            newTitle.textContent = input.value.trim() || "Sin título";

            makeEditable(newTitle);

            input.replaceWith(newTitle);
        }
        });

        input.addEventListener("blur", () => { //por si da click afuera revertir cambios
        const newTitle = document.createElement("p");
        newTitle.classList.add("listTitle");
        newTitle.textContent = input.value.trim() || "Sin título";
        makeEditable(newTitle);
        input.replaceWith(newTitle);
        });
    });
}
function makeTaskEditable(taskText) {
taskText.addEventListener("click", () => {
    const currentText = taskText.textContent;
    const input = document.createElement("input");
    input.type = "text";
    input.value = currentText;
    input.classList.add("taskEditInput");

    taskText.replaceWith(input);
    input.focus();

    input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        const newSpan = document.createElement("span");
        newSpan.textContent = input.value.trim() || "Tarea sin título";
        makeTaskEditable(newSpan);
        input.replaceWith(newSpan);
    }
    });

    input.addEventListener("blur", () => {
    const newSpan = document.createElement("span");
    newSpan.textContent = input.value.trim() || "Tarea sin título";
    makeTaskEditable(newSpan);
    input.replaceWith(newSpan);
    });
});
}
function saveListTitle(input) {
    const newTitle = document.createElement("p");
    newTitle.classList.add("listTitle");
    newTitle.textContent = input.value.trim() || "Sin título";
    makeEditable(newTitle);
    input.replaceWith(newTitle);
    
    
}
function addList(){
    const listElement = document.createElement("div");
    listElement.classList.add("list");

    const listHeader = document.createElement("header");
    listHeader.classList.add("listHeader");

    const headerInput = document.createElement("input");
    headerInput.type = "text";
    headerInput.placeholder = "Nombre de la lista...";
    headerInput.classList.add("titleInput");
    listHeader.appendChild(headerInput);

    headerInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") saveListTitle(headerInput);
    });
    headerInput.addEventListener("blur", () => saveListTitle(headerInput));

    const delListBtn = document.createElement("button");
    delListBtn.innerText = "X";
    delListBtn.addEventListener("click", () =>{
        const parent = delListBtn.parentElement;
        const grandpa = parent.parentElement;
        grandpa.remove();
    });
    const listBody = document.createElement("div");
    listBody.classList.add("listBody");

    const listy = document.createElement("ul");
    listy.classList.add("tasks");

    const addTaskBtn = document.createElement("button");
    addTaskBtn.classList.add("btn");
    addTaskBtn.innerText = "Agregar tarea";

    listElement.appendChild(listHeader);
    listHeader.appendChild(delListBtn);
    listElement.appendChild(listBody);
    listBody.appendChild(listy);
    listElement.appendChild(addTaskBtn);
    workspace.appendChild(listElement);

    headerInput.focus();

    addTaskBtn.addEventListener("click", () => {
        const newTask = document.createElement("li");

        const newCheckBox = document.createElement("input");
        newCheckBox.type = "checkbox";

        const taskText = document.createElement("span");
        taskText.textContent = "Nueva tarea";
        makeTaskEditable(taskText);

        const DelTaskBtn = document.createElement("button");
        DelTaskBtn.innerText = "X";

        newTask.appendChild(newCheckBox);
        newTask.appendChild(taskText);
        newTask.appendChild(DelTaskBtn);
        listy.appendChild(newTask);

        taskText.click();
        
        DelTaskBtn.addEventListener("click", () =>{
            const parent = DelTaskBtn.parentElement;
            parent.remove();
        })
    });

}

const addListBtn = document.getElementById("addListBtn");
addListBtn.addEventListener("click", addList);
