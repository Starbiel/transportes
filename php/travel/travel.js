const resultsLive = document.querySelector('#resultsLive');
const carrierTravel = document.querySelector('#carrierTravel');

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