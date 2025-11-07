const editButtons = document.querySelectorAll(".editBtn");

        editButtons.forEach(button => {
            button.addEventListener("click", () => {
                const editable = button.parentElement.querySelector("#editable");
               console.log("Button clicked");
                if(!editable){
                    console.log("No se ecnotnro texto editable");
                    return;
                }
                const tag = editable.tagName;
                const input = document.createElement("input");
                input.type = "text";
                input.value = editable.textContent;

                editable.replaceWith(input);
                input.focus();

                input.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        const newElement = document.createElement(tag);
                        newElement.id = 'editable';
                        newElement.textContent = input.value;
                        input.replaceWith(newElement);
                    }
                });
            });
        });