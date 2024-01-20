<?php 

include "../database/server.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $carrierName = $_POST['carrierName'];
    $sql = "SELECT nome FROM carrier WHERE nome LIKE '$carrierName%'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $hint = '';
        while($row = $result->fetch_assoc()) {
            $nome = strtoupper($row['nome']);
            $hint .= "<div class='carrierNameHint' onclick='setValor(this)'>$nome</div>";
        }
        echo $hint;
    }
    else {
        echo "<div>Sem Resultados</div>";
    }
}
?>