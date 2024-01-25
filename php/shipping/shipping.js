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
                    let data = new FormData();
                    data.append('shippingId', this.id);
                    fetch('closeShipping.php', {
                        method: 'POST',
                        body: data
                    })
                    .then(reponse => reponse.text())
                    .then(data => {
                        document.querySelector("#confirmBack").remove();
                        for (let index = 0; index < closeShipping.length; index++) {
                            if(closeShipping[index].id == this.id) {
                                closeShipping[index].parentElement.parentElement.parentElement.parentElement.parentElement.remove();
                            }
                        }
                    })
                })
            }
        }
    }
}

const main = document.body;
const observer = new MutationObserver(creatorCallback);
const options = { childList: true, subtree: true };
observer.observe(main, options);