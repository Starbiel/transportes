<?php 
include "../database/server.php";
define('DiasSemanaBR', [
    0 => 'Seg',
    1 => 'Ter',
    2 => 'Qua',
    3 => 'Qui',
    4 => 'Sex',
    5 => 'Sab',
    6 => 'Dom'
]);
$minhaChaveAPI = $_SERVER['MINHA_CHAVE_API'];
spl_autoload_register(function($class) {
    $class = '../' . lcfirst(str_replace('\\', '/', $class) . '.php');  
    $class = str_replace('php/', '', $class);
    if(file_exists($class)) {
        require $class;
    }
});
use php\validationArea\Validation;
use php\database\Execute;
$executer = new Execute;
$validator = (new Validation);

function takeCarrier($row) {
    echo "<option value='" . $row['nome'] . "'>" . $row['nome'] . "</option>";
}

function createTrDriver($driverQuery) {
    $tr = "<tr>";
    foreach ($driverQuery as $key => $value) {
        if($key == 'name' || $key == 'number' || $key == 'earning' ) {
            $tr .= "<td>$value</td>";
        }
    }
    $tr .= "</tr>";
    echo $tr;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="earning.css">
    <script src="earning.js" defer></script>
</head>
<body>
    <div id='nav-bar'>
        <button><a href="../../index.php"><p>Menu Principal</p><i class="fa-solid fa-house"></i></a></button>
        <button><a href="../drivers/newDriver.php"><p>Cadastrar Motorista</p><i class="fa-solid fa-person"></i></a></button>
        <button><a href="../truck/truck.php"><p>Caminhões</p><i class="fa-solid fa-truck"></i></a></button>
        <button><a href="../travel/newTravel.php"><p>Viagens</p><i class="fa-solid fa-road"></i></a></button>
        <?php if($executer->shippingOpen($conn)[0])echo "<button><a href=\"../shipping/peddingShipping.php\"><p>Acertamentos Pedentes</p><i class=\"fa-solid fa-pen\"></i></a></button>";?>
    </div>
    <div id='container'>
        <div id='mainDash'>
            <div id='lastShipping'>
                <?php 
                $sql = "SELECT 
                            shipping.id_shipping, 
                            shipping.inicio, 
                            shipping.final, 
                            driver.nome, 
                            SUM(travel.valor) as earning 
                        FROM 
                            shipping 
                            JOIN driver ON shipping.id_driver = driver.id_driver 
                            JOIN travel ON shipping.id_driver = travel.id_driver
                        WHERE 
                            shipping.finalizado = 1 
                            AND travel.id_shipping = shipping.id_shipping 
                        GROUP BY 
                            shipping.id_shipping, 
                            shipping.inicio, 
                            shipping.final, 
                            driver.nome
                        ORDER BY
                            shipping.id_shipping DESC";
    
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo '<h2>Ultimo Acertamento</h2><p>Acertamento Numero:' . $row['id_shipping'] . "</p>";
                    echo '<p>Inicio: ' . $row['inicio'] . "</p>";
                    echo '<p>Final: ' . $row['final'] . "</p>";
                    echo '<p>Motorista: ' . $row['nome'] . "</p>";
                    echo 'Lucro total: R$' . $row['earning']; 
                }
                else {
                    Echo "<h2>Não houve acertos finalizados ainda</h2>";
                }
                
                ?>
            </div>
            <div id='timeSet'>
                <h1>Pesquisar Tempo</h1>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <select name="time" id="">
                        <option value="month">Mês</option>
                        <option value="semester">Semestre</option>
                        <option value="year">Ano</option>
                    </select>
                    <input type="submit" value="Enviar">
                </form>
            </div>
            <div id='timeEarning'>
                <?php 
                $actualDate = new DateTime();
                $variable = '-1 month';
                if(isset($_POST['time'])) {
                    if($validator->basicChecker($_POST['time'])) {
                        if($_POST['time'] == "semester") {
                            $variable = '-6 month';
                        }
                        else if($_POST['time'] == "year") {
                            $variable = '-12 month';
                        }
                    }
                }
                $actualDate->modify($variable);
                $dataReady = $actualDate->format('Y-m-d');
                $dataBrazilFormat = $actualDate->format('d-m-Y');
                $sqlOne = "SELECT 
                            sum(valor) as totalEarning
                        FROM
                            travel
                        WHERE 
                            dia >= '$dataReady'";
                $resultOne = $conn->query($sqlOne);
                $sqlTwo = "SELECT 
                                sum(driverPayment) as driverPayments,
                                sum(toll) as tolls,
                                sum(diesel) as diesel
                            FROM
                                shipping
                            WHERE 
                                inicio >= '$dataReady'";
                $resultTwo = $conn->query($sqlTwo);
                if($resultOne->num_rows > 0 && $resultTwo->num_rows > 0) {
                    $row = $resultOne->fetch_assoc();
                    $rowTwo = $resultTwo->fetch_assoc();
                    echo "<h3>Lucro desde $dataBrazilFormat</h3>";
                    echo "<p>Bruto: R$". round($row['totalEarning'], 2) . "</p>";
                    echo "<p>Liquido: R$". round(($row['totalEarning']-$rowTwo['driverPayments']-$rowTwo['tolls']-$rowTwo['diesel']), 2) ."</p>";
                }
                else {
                    echo '<h2>Sem resultado<h2>';
                }
                ?>
            </div>
        </div>
        <div id='specificDash'>
            <div id='driversEarnings'>
                <table>
                    <h2>Motoristas</h2>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Ganhos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sql = "SELECT id_driver FROM driver WHERE ativo = 1";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $resultFullQuery = $executer->driverFullQuery($row['id_driver'], $conn);
                                    createTrDriver($resultFullQuery);
                                }
                            }
                            else {
                                echo "Sem motoristas ativos";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id='searchShipping'>
                <div id='fakeForm'>
                        <div>
                            <button id='searchTravels'>Pesquisar Viagens</button>
                            <div id='openForm'>Fechar</div>
                        </div>
                        <div id='searchForm'>
                            <label for="timeTravel">Periodo de tempo</label>
                            <input type="date" name="timeStart" id="timeTravelStart">
                            <span id='leftTimeStart'></span>
                            <input type="date" name="timeEnd" id="timeTravelEnd">
                            <span id='leftTimeEnd'></span>
                            <label>
                                Empresa:
                                <input list='carriers' id='carrierList'>
                            </label>
                            <datalist id='carriers'>
                            <?php 
                                $sql = "SELECT * FROM carrier";
                                $result = $conn->query($sql);
                                if($result->num_rows >0) {
                                    while($row = $result->fetch_assoc()) {
                                        takeCarrier($row);
                                    }
                                }
                            ?>
                            </datalist>
                        </div>
                </div>
                <div id='tableResult'>
                    <table id='searchResult'>

                    </table>
                </div>
            </div>
            <div id='earningGrafics'>
                <canvas id="myChart" style="width:100%;max-width:100%"></canvas>
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
            <?php 
        date_default_timezone_set('America/Sao_Paulo');
        $cont = 0;
        $days =  array();
        $earning =  array();
        if($variable == '-1 month') {
            $time = 30;
        }
        else if ($variable == '-6 month') {
            $time = 30*6;
        }
        else {
            $time = 30*12;
        }
        for($i = $time; $i >= 0; $i--) {
            $earningDay = 0;
            $someDate = date("Y-m-d" , strtotime("-$i days"));  
            $days[$time-$i] = $someDate;
            $sql = "SELECT  sum(travel.valor) as sum,
                            shipping.final
                    FROM travel
                    INNER JOIN shipping ON shipping.id_shipping = travel.id_shipping
                    WHERE shipping.final LIKE '$someDate%'
                    GROUP BY travel.id_shipping";
            $result = $conn->query($sql);

            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $earningDay += $row['sum'];
                }
                $earning[$time-$i] = $earningDay;
            }
            else {
                $earning[$time-$i] = 0;
            }
            }
            $days[$time] = "Hoje";
            echo "<script>
                const xValues = ['" . (implode("','", $days)) . "'];
                const yValues = [" . (implode(",", $earning)) . "];
                var ctx = document.getElementById('myChart').getContext('2d');
                var grafico = new Chart(ctx, {
                    type: \"line\",
                    data: {
                        labels: xValues,
                        datasets: [{
                        fill: false,
                        lineTension: 0.3,
                        backgroundColor: \"rgba(0,0,255,1.0)\",
                        borderColor: \"rgba(0,0,255,0.1)\",
                        data: yValues
                        }]
                    },
                    options: {
                        legend: {display: false},
                        scales: {
                            yAxes: [{ticks: {min: 0, max:100000}}],
                        }
                    }
                    });
            </script>";
        ?>
        <script>
        $("#openForm").click(function(){
            $("#searchForm").slideToggle('slow');
            if($("#openForm").text() == "Abrir") {
                $("#openForm").text("Fechar");
            }
            else {
                $("#openForm").text("Abrir");
            }
        });
        </script>
</html>