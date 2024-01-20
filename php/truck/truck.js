import { imageDownloader } from "../../images/ImageDownloader.js";


let downloadButton = document.getElementsByClassName('downloadButton');

for (let index = 0; index < downloadButton.length; index++) {
    downloadButton[index].addEventListener('click', function() {
        imageDownloader('../../', this.value, 'trucker');
    });   
}

document.getElementById('submitFiles').addEventListener('click', function() {
    var forms = document.querySelectorAll('.addDocument');
    var fetchPromises = [];
    var fetchPromise = forms.forEach(function(form) {
        var formData = new FormData(form);
        console.log(formData);
        fetch('addDocument/addDocument.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
        })
        .catch(error => console.error('Erro na solicitação fetch:', error));
        fetchPromises.push(fetchPromise);
    });
    Promise.all(fetchPromises).then(function() {
        location.reload();
    });
});

let associedTruck = document.getElementsByClassName('truck');

for (let index = 0; index < associedTruck.length; index++) {
    associedTruck[index].addEventListener('click', function() {
        var data = new FormData();
        data.append('truckId', this.id);
        console.log(this.id);
        fetch('../drivers/driver-truck-query/updateDriver.php', {
            method: "POST",
            body:  data
        })
        .then(response => response.text())
        .then(data => {
            window.location.href = '../drivers/newDriver.php';
        })
    })
    
}