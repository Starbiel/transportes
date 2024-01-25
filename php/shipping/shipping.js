import { createConfirm } from "../../script/confirmWindow.js";
const closeShipping = document.getElementsByClassName('closeShipping');

for (let index = 0; index < closeShipping.length; index++) {
    closeShipping[index].addEventListener('click', function() {
        createConfirm(this.id)
    })
}


function creatorCallback(mutationsList, observer) {
    for (const mutation of mutationsList) {
        if (mutation.type === 'childList') {
            const myNewElement = document.querySelector('.acceptButton');
            if (myNewElement) {
                myNewElement.addEventListener('click', function() {
                    for (let index = 0; index < closeShipping.length; index++) {
                        if(closeShipping[index].id == this.id) {
                            const driverPayment = (closeShipping[index].parentElement.previousElementSibling.previousElementSibling.previousElementSibling);
                            const truckPart = driverPayment.nextElementSibling;
                            let data = new FormData();
                            data.append('shippingId', this.id);
                            data.append('driverPayment', driverPayment.innerHTML);
                            data.append('truckPart', truckPart.innerHTML);
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