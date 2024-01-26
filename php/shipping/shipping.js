import { createConfirm } from "../../script/confirmWindow.js";
const closeShipping = document.getElementsByClassName('closeShipping');
const moreInputButton = document.getElementsByClassName('moreInput');

for (let index = 0; index < closeShipping.length; index++) {
    closeShipping[index].addEventListener('click', function() {
        createConfirm(this.id)
    })
}

//ADD NEW INPUTS

for (let index = 0; index < moreInputButton.length; index++) {
    moreInputButton[index].addEventListener('click', function() {
        const newInput = document.createElement('input');
        newInput.type = 'number'
        newInput.classList.add("inputOf"+index);
        newInput.addEventListener('change', function() {
            let data = new FormData();
            let shippingId = this.parentElement.parentElement.previousElementSibling.firstChild.lastChild.querySelector('td:nth-child(4)').firstChild.id;
            var element = this.parentElement;
            var inputs = element.querySelectorAll('input');
            let inputsValues = [];
            inputs.forEach(function(input) {            
                inputsValues.push(input.value);
            });
            data.append('extras', JSON.stringify(inputsValues));
            data.append('shippingId', shippingId)
            fetch('takeShipping.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                const elementChanges = (this.parentElement.parentElement.previousElementSibling.firstChild.lastChild.querySelector('td:nth-child(1)').lastChild)
                elementChanges.innerHTML = "R$" + data;
            })
        })
        const elementParent = this.parentElement.parentElement;
        elementParent.appendChild(newInput)
    })
}


//CLOSE SHIPPING

function creatorCallback(mutationsList, observer) {
    for (const mutation of mutationsList) {
        if (mutation.type === 'childList') {
            const myNewElement = document.querySelector('.acceptButton');
            if (myNewElement) {
                myNewElement.addEventListener('click', function() {
                    for (let index = 0; index < closeShipping.length; index++) {
                        if(closeShipping[index].id == this.id) {
                            let inputsApply = document.getElementsByClassName("inputOf"+index);
                            let inputsValues = [];
                            for (let i = 0; i < inputsApply.length; i++) {
                                if(inputsApply[i].value != 0) {
                                    inputsValues.push(inputsApply[i].value);
                                }
                            }
                            let data = new FormData();
                            data.append('shippingId', this.id);
                            data.append('extras', JSON.stringify(inputsValues));
                            fetch('closeShipping.php', {
                                method: 'POST',
                                body: data
                            })
                            .then(reponse => reponse.text())
                            .then(data => {
                                console.log(data);
                                document.querySelector("#confirmBack").remove();
                                for (let index = 0; index < closeShipping.length; index++) {
                                    if(closeShipping[index].id == this.id) {
                                        closeShipping[index].parentElement.parentElement.parentElement.parentElement.parentElement.remove();
                                    }
                                }
                            })
                        }
                    }
                })
            }
        }
    }
}

const main = document.body;
const observer = new MutationObserver(creatorCallback);
const options = { childList: true, subtree: true };
observer.observe(main, options);