function showResult(str, hint) {
    if (str.length==0) {
        document.getElementById("livesearch"+hint).innerHTML="";
        document.getElementById("livesearch"+hint).style.border="0px";
    }
    else {
        var xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function() {
            if (this.readyState==4 && this.status==200) {
                document.getElementById("livesearch"+hint).innerHTML=this.responseText;
            }
        }
        if(hint == 'brand') {
            const firstSearch = document.querySelector('#markInput').value
            xmlhttp.open("GET","livesearch.php?q="+str+'&p='+hint+'&r='+firstSearch,true);
        }
        else {
            xmlhttp.open("GET","livesearch.php?q="+str+'&p='+hint,true);
        }
        xmlhttp.send();
    }
}

function setOn(classButton, value) {
    if(classButton === 'optionMark') {
        document.querySelector('#markInput').value = value;
        document.getElementById("livesearchmark").innerHTML="";
        document.getElementById("livesearchmark").style.border="0px";
    }
    else if (classButton === 'optionBrand') {
        document.querySelector('#brandInput').value = value;
        document.getElementById("livesearchbrand").innerHTML="";
        document.getElementById("livesearchbrand").style.border="0px";
    }
}