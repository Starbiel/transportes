import { imageDownloader } from "../../images/ImageDownloader.js";

document.querySelector('#fileInput2').addEventListener('change', ()=> {
    document.querySelector('#inputFileMaskText2').placeholder = 'Imagem Ok';
    if(document.querySelector('#nameDriver').value != "") {
        document.querySelector('#select').style.backgroundColor = 'Green';
    }
})

document.querySelector('#nameDriver').addEventListener('change', ()=> {
    console.log(document.querySelector('#nameDriver').value != '')
    if(document.querySelector('#inputFileMaskText2').placeholder == 'Imagem Ok' && document.querySelector('#nameDriver').value != '') {
        document.querySelector('#select').style.backgroundColor = 'Green';
    }
    else {
        document.querySelector('#select').style.backgroundColor = 'Red'
    }
})

// document.getElementById('downloadButton').addEventListener('click', function() {
//     imageDownloader('../../', 1);
// });

let selectButtons = document.getElementsByClassName('clickToAdd')

for (let index = 0; index < selectButtons.length; index++) {
    selectButtons[index].addEventListener('click', function() {
        handleClick(this);
    });

    selectButtons[index].addEventListener('touchstart', function() {
        handleClick(this);
    });
}

function handleClick(iten) {
    var data = new FormData();
    data.append('driverId', iten.id);
    fetch('driver-truck-query/updateDriver.php', {
        method: "POST",
        body:  data
    })
    .then(response => response.text())
    .then(data => {
        window.location.href = '../truck/truck.php';
    })
}
