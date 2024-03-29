<?php 
include "../database/server.php";
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

function addDrivers($row) {
    $option = "<option value='" . $row['id_driver'] . "'>". $row['nome'] ."</option>";
    echo $option;
}

function createInfo($info) {
    $td = "<td>$info</td>";
    return $td;
}

function createTR($row) {
    $tr = "<tr class=\"travel-open\">";
    foreach ($row as $key => $value) {
        if($key == 'day') {
            $value = str_replace('00:00:00', '', $value);
        }
        if($key != 'travel')
        $tr .= createInfo($value);
    }
    $tr .= "<td><button class=\"closeButton\" id='". $row['travel']  ."'>Fechar Viagem</button></td>";
    $tr .= "</tr>";
    echo $tr;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['formTravel'])) {
        foreach ($_POST as $key => $value) {
            if(!($validator->basicChecker($_POST[$key]))) {
                $conn->close();
                die();
            }
        }
        $date = $_POST['dateTravel'];
        $origem = $_POST['origemTravel'];
        $destiny = $_POST['destinyTravel'];
        $distance = $_POST['distanceTravel'];
        $price = $_POST['priceTravel'];
        $driverId = $_POST['driver'];
        $carrier = $_POST['carrier'];
        $resultDriverQuery = $executer->driverFullQuery($driverId, $conn);
        date_default_timezone_set('America/Sao_Paulo');
        $today = date("Y-m-d H:i:s");  
        $sql = "SELECT id_carrier FROM carrier WHERE nome = ? ";
        $statement = $conn->prepare($sql);
        $statement->bind_param("s", $carrier);
        $statement->execute() or die("<b>Error:</b> Problema para localizar esse ID<br/>" . mysqli_connect_error()); 
        $result = $statement->get_result();  
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $carrier = $row['id_carrier'];
        }
        else {
            $carrier = strtolower($carrier);
            $sql = "INSERT INTO carrier(nome) VALUES('$carrier')";
            $conn->query($sql);
            $carrier = $conn->insert_id;
        }
        $sql = "INSERT INTO travel(dia, origem, destino, valor, id_driver, id_carrier, distancia) VALUES (?,?,?,?,?,?,?)";
        $statement = $conn->prepare($sql);
        $statement->bind_param("sssdiid", $date, $origem, $destiny, $price, $driverId, $carrier, $distance);
        $statement->execute() or die("<b>Error:</b> Problema para localizar esse ID<br/>" . mysqli_connect_error());  
        header("Location: newTravel.php");
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viagem</title>
    <link rel="stylesheet" href="travel.css">
    <link rel="stylesheet" href="../../navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $minhaChaveAPI ?>&libraries=places&callback=initAutoComplete" async defer></script>
    <script src='travel.js' defer></script>
</head>
<body>
    <div id='nav-bar'>
        <button><a href="../../index.php"><p>Menu Principal</p><i class="fa-solid fa-house"></i></a></button>
        <button><a href="../drivers/newDriver.php"><p>Cadastrar Motorista</p><i class="fa-solid fa-person"></i></a></button>
        <button><a href="../truck/truck.php"><p>Caminhões</p><i class="fa-solid fa-road"></i></a></button>
        <?php if($executer->shippingOpen($conn)[0])echo "<button><a href=\"../shipping/peddingShipping.php\"><p>Acertamentos Pedentes</p><i class=\"fa-solid fa-pen\"></i></a></button>";?>
        <button><a href=""><p>Lucros</p><i class="fa-solid fa-money-bill"></i></a></button>
    </div>
    <div id="container">
        <div id="container-form">
            <div id="form-header">
                <p>Adicionar nova viagem</p>
                <button id="form-open">Abrir</button>
            </div>
            <form id="formTravel" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <label for="">Dia de Inicio</label>
                <input type="date" name="dateTravel">
                <label for="">Origem</label>
                <input id="autocomplete" class="controls" type="text" placeholder="Search Box" name='origemTravel'/>
                <label for="">Destino</label>
                <input id="autocompleteTwo" type="text" name="destinyTravel">
                <label for="">Distancia</label>
                <input type="number" name="distanceTravel" id="distanceTravel" step="0.001">
                <label for="">Valor</label>
                <input type="number" name="priceTravel" id="">
                <label for="">Motorista</label>
                <select name="driver" id="">
                    <?php 
                        $sql='SELECT id_driver, nome FROM driver WHERE ativo = 1 AND id_truck IS NOT NULL';
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()) {
                            addDrivers($row);
                        }
                    ?>
                </select>
                <label for="carrier">Transportadora</label>
                <div id='carrierContainer'>
                    <input type="text" name="carrier" id="carrierTravel">
                    <div id='resultsLive'></div>
                </div>
                <input type="submit" value="Enviar" name="formTravel">
            </form>
        </div>
        <?php 
            $sql='SELECT travel.id_travel AS travel, travel.dia AS day, driver.nome AS driver, travel.valor AS value FROM travel, driver WHERE travel.id_driver = driver.id_driver AND id_shipping IS NULL';
            $result = $conn->query($sql);
            if($result->num_rows > 0):
        ?>
        <div id="container-travel">
            <h3>Viagens em aberto</h3>
            <table>
                <thead>
                    <tr>
                        <th>Dia</th>
                        <th>Motorista</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        {
                            while($row = $result->fetch_assoc()) {
                                createTR($row);
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
            endif;
        ?>
        <button><a href="../../index.html">Voltar</a></button>
    </div>
    <script>
        let autocomplete;
        let autocompleteTwo;
        const origemInput = document.querySelector('#autocomplete');
        const destinyInput = document.querySelector('#autocompleteTwo');
        const distance = document.querySelector('#distanceTravel');
        function initAutoComplete() {
            autocomplete = new google.maps.places.Autocomplete(document.querySelector('#autocomplete'),
            {
                type: ['establishment'],
                componentRestrictions: {'country': ['BR']},
                fields: ['place_id', 'geometry', 'name']
            })
            autocompleteTwo = new google.maps.places.Autocomplete(document.querySelector('#autocompleteTwo'),
            {
                type: ['establishment'],
                componentRestrictions: {'country': ['BR']},
                fields: ['place_id', 'geometry', 'name']
            })
            autocomplete.addListener('place_changed', onPlaceChanged);
            autocompleteTwo.addListener('place_changed', onPlaceChanged);
        }
        function onPlaceChanged() {
            if(origemInput.value != '' && destinyInput.value != "") {
                var data = new FormData();
                data.append('origemInput', origemInput.value);
                data.append('destinyInput', destinyInput.value);
                fetch('cURLresult.php', {
                    method: "POST",
                    body:  data
                })
                .then(response => response.json())
                .then(data => {
                    distance.value = (data.rows[0].elements[0].distance.value)/1000;
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
            }
        }
        $("#formTravel").hide();
        $("#form-open").click(function(){
            $("#formTravel").slideToggle('slow');
            if($("#form-open").text() == "Abrir") {
                $("#form-open").text("Fechar");
            }
            else {
                $("#form-open").text("Abrir");
            }
        });
    </script>
</body>
</html>

<?php 
    $conn->close();
?>