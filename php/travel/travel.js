const resultsLive = document.querySelector('#resultsLive');
const carrierTravel = document.querySelector('#carrierTravel');
const closeButtons = document.getElementsByClassName('closeButton');

carrierTravel.addEventListener('keyup', function(){
    if(this.value == "") {
        resultsLive.innerHTML = "";
        resultsLive.style.border = 0 + "px" + " solid black";
    }
    else {
        resultsLive.style.border = 1 + "px" + " solid black";
        let data = new FormData();
        data.append('carrierName', this.value);
        fetch('liveSearchcarrier.php', {
            method: 'POST',
            body: data
        })
        .then(response => response.text())
        .then(data => {
            resultsLive.innerHTML = (data);
        })
    }
})

function setValor(item) {
    console.log(this);
    carrierTravel.value = item.innerHTML;
    resultsLive.innerHTML = "";
    resultsLive.style.border = 0 + "px" + " solid black";
}

for (let index = 0; index < closeButtons.length; index++) {
    closeButtons[index].addEventListener('click', function() {
        let data = new FormData();
        data.append('travelId', this.id)
        fetch('close.php', {
            method: 'POST',
            body: data
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            this.parentElement.parentElement.remove();
        })

    })
}