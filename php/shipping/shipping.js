import { createConfirm } from "../../script/confirmWindow.js";
const closeShipping = document.getElementsByClassName('closeShipping');
const moreInputButton = document.getElementsByClassName('optionsInput');

for (let index = 0; index < closeShipping.length; index++) {
    closeShipping[index].addEventListener('click', function() {
        createConfirm(this.id)
    })
}

//ADD NEW INPUTS TO DRIVER and TRUCK

for (let index = 0; index < moreInputButton.length; index++) {
    let buttons = moreInputButton[index].querySelectorAll('button');
    for (let i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function() {
            const newInput = document.createElement('input');
            newInput.type = 'number'
            newInput.classList.add("inputOf"+index);
            const a = document.createAttribute("subType");
            a.value = this.className;
            newInput.setAttributeNode(a);
            newInput.step = 0.01;
            newInput.addEventListener('change', function() {
                let data = new FormData();
                let shippingId = this.parentElement.parentElement.previousElementSibling.firstElementChild.lastChild.querySelector('td:nth-child(4)').firstChild.id;
                let element = this.parentElement;
                let inputs = element.querySelectorAll('input');
                let inputsValues = [];
                inputs.forEach(function(input) {   
                    let single = [input.value, input.getAttribute('subtype')];         
                    inputsValues.push(single);
                });
                data.append('extras', JSON.stringify(inputsValues));
                data.append('shippingId', shippingId)
                data.append('returnParam', element.className)
                fetch('takeShipping.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    let elementChanges = "";
                    if(element.className == 'truckResult') {
                        return 0;
                    }
                    else if(element.className == 'driverResult') {
                        elementChanges = (this.parentElement.parentElement.previousElementSibling.firstElementChild.lastChild.querySelector('td:nth-child(1)').lastChild)
                    }
                    elementChanges.innerHTML = "R$" + data;
                })
            })
            const elementParent = this.parentElement.parentElement.parentElement;
            elementParent.appendChild(newInput)
        })
    }
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
                            let inputsApplyDriver = document.getElementsByClassName('driverResult')[index].querySelectorAll('input');
                            let inputsValuesDriver = [];
                            let inputsApplyTruck = document.getElementsByClassName('truckResult')[index].querySelectorAll('input');
                            let inputsValuesTruck = [];
                            for (let i = 0; i < inputsApplyDriver.length; i++) {
                                let auxArray = [inputsApplyDriver[i].value, inputsApplyDriver[i].getAttribute('subtype')]
                                if(inputsApplyDriver[i].value != 0) {
                                    inputsValuesDriver.push(auxArray);
                                }
                            }
                            for (let j = 0; j < inputsApplyTruck.length; j++) {
                                let auxArray = [inputsApplyDriver[i].value, inputsApplyDriver[i].getAttribute('subtype')]
                                if(inputsApplyTruck[j].value != 0) {
                                    inputsValuesTruck.push(auxArray);
                                }
                            }
                            let data = new FormData();
                            data.append('shippingId', this.id);
                            data.append('extrasDriver', JSON.stringify(inputsValuesDriver));
                            data.append('extrasTruck', JSON.stringify(inputsValuesTruck));
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