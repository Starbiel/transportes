<?php 

include '../database/server.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['startDay']) && isset($_POST['carrier'])) {
        $startDay = $_POST['startDay'];
        $endDay = $_POST['endDay'];
        $carrierName = $_POST['carrier'];
        $sql = "SELECT  travel.id_travel,
                        driver.nome as driverName, 
                        travel.dia,
                        shipping.final,
                        travel.valor,
                        carrier.nome as carrierName
                FROM travel
                INNER JOIN driver ON driver.id_driver = travel.id_driver
                INNER JOIN shipping ON shipping.id_shipping = travel.id_shipping
                INNER JOIN carrier ON carrier.id_carrier = travel.id_carrier
                WHERE travel.dia BETWEEN '$startDay' AND '$endDay' AND carrier.nome = '$carrierName' ";
    }
    else if(isset($_POST['carrier'])) {
        $carrierName = $_POST['carrier'];
        $sql = "SELECT  travel.id_travel,
                        driver.nome as driverName, 
                        travel.dia,
                        shipping.final,
                        travel.valor,
                        carrier.nome as carrierName
                FROM travel
                INNER JOIN driver ON driver.id_driver = travel.id_driver
                INNER JOIN shipping ON shipping.id_shipping = travel.id_shipping
                INNER JOIN carrier ON carrier.id_carrier = travel.id_carrier
                WHERE carrier.nome = '$carrierName'";
    }
    else {
        $startDay = $_POST['startDay'];
        $endDay = $_POST['endDay'];
        $sql = "SELECT  travel.id_travel,
                        driver.nome as driverName, 
                        travel.dia,
                        shipping.final,
                        travel.valor,
                        carrier.nome as carrierName
                FROM travel
                INNER JOIN driver ON driver.id_driver = travel.id_driver
                INNER JOIN shipping ON shipping.id_shipping = travel.id_shipping
                INNER JOIN carrier ON carrier.id_carrier = travel.id_carrier
                WHERE travel.dia BETWEEN '$startDay' AND '$endDay'";
    }
    $result = $conn->query($sql);
    $div = "";
    if($result->num_rows > 0) {
        $div .= "<th>Motorista</th><th>Inicio viagem</th><th>Dia acertamento</th><th>Valor viagem</th><th>Empresa</th>";
        while($row = $result->fetch_assoc()) {
            $div .= "<tr><td>". $row['driverName'] ."</td>
                         <td>". str_replace('00:00:00', '', $row['dia']) ."</td>
                         <td>". $row['final'] ."</td>
                         <td>". $row['valor'] ."</td>
                         <td>". $row['carrierName'] ."</td></tr>";
        }
    }
    else {
        $div .= "<div>Nenhum resultado</div>";
    }
    echo $div;
}

$conn->close();
?>