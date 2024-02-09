const buttonSearch = document.querySelector('#searchTravels');
const errorTimeStart = document.querySelector('#leftTimeStart');
const errorTimeEnd = document.querySelector('#leftTimeEnd');


buttonSearch.addEventListener('click', function() {
    document.getElementById('searchResult').innerHTML = "";
    const timeStart = document.querySelector('#timeTravelStart');
    const timeEnd = document.querySelector('#timeTravelEnd');
    const carrier = document.querySelector('#carrierList');
    if((timeStart.value == '') ^ (timeEnd.value == '')) {
        errorTimeEnd.innerHTML = 'Coloque uma data';
        return 0;
    }
    else if((timeStart.value == '') && (timeEnd.value == '') && carrier.value == '') {
        return 0;
    }
    else {
        data = new FormData();
        if(timeStart.value != '') {
            data.append('startDay', timeStart.value);
            data.append('endDay', timeEnd.value);
        }
        if(carrier.value != '') {
            data.append('carrier', carrier.value);
        }
        fetch('searchEarning.php', {
            method: 'POST',
            body: data
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('searchResult').innerHTML = data;
        })
    }
    document.querySelector('#searchForm').style.display = "none";
})