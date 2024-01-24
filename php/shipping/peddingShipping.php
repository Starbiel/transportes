<?php 
include_once '../database/server.php';
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

function pInsert($info) {
    $p = "<p>$info</p>";
    return $p;
}

function singleInfoAplicator($row) {
    $div = "<div class='singleInfo'>";
    foreach ($row as $key => $value) {
        if($key == 'name' || $key == 'plate' || $key == 'shippingStart' || $key == 'shippingSum') {
            $div .= pInsert($value);
        }
    }
    $div .= "</div>";
    $driverPart = ($row['shippingSum']*0.16);
    $truckPart = ($row['shippingSum']*0.10);
    $div .= "<div class='singleResults'>
                <p>$driverPart</p>
                <p>$truckPart</p>
            <button class='moreCalc'>Calculos extras +</button>
        </div>";
    return $div;
}

function extraCalcs() {
    return "<div class='extraCalcDiv'>
                <div class='driverResult'>
                    <div class='driverHeader'>
                        <h3>Motorista</h3>
                        <button class='moreInput'>+</button>
                    </div>
                    <input type=\"text\"><input type=\"text\"><input type=\"text\">
                </div>
                <div class='truckResult'>
                    <div class='driverHeader'>
                        <h3>Caminhão</h3>
                    </div>
                    <input type=\"text\"><input type=\"text\"><input type=\"text\">
                </div>
            </div>";
}

function singleShippingCretor($row) {
    $div = "<div class='singleShipping'>";
    $div .= "<div>";
    $div .=  singleInfoAplicator($row);
    $div .= "</div>";
    $div .= extraCalcs();
    $div .="</div>";
    echo $div;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acertamentos</title>
    <link rel="stylesheet" href="../../navbar.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="shipping.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div id='nav-bar'>
        <button><a href="../../index.php"><p>Menu Principal</p><i class="fa-solid fa-house"></i></a></button>
        <button><a href="../drivers/newDriver.php"><p>Cadastrar Motorista</p><i class="fa-solid fa-person"></i></a></button>
        <button><a href="../truck/truck.php"><p>Caminhões</p><i class="fa-solid fa-road"></i></a></button>
        <button><a href=""><p>Lucros</p><i class="fa-solid fa-money-bill"></i></a></button>
    </div>
    <div id='container'>
        <div id='shippings'>
            <?php 
                $sql = "SELECT id_driver FROM shipping WHERE finalizado = 0";
                $result = $conn->query($sql);
                if($result->num_rows >0) {
                    while($row = $result->fetch_assoc()) {
                        $resultFullQuery = $executer->driverFullQuery($row['id_driver'], $conn);
                        singleShippingCretor($resultFullQuery);
                    }
                }
            ?>
            <div class='singleShipping'>
                <div>
                    <div class='singleInfo'>
                        <p>Nome Caminhoneiro</p>
                        <p>AAA-1234</p>
                        <p>13-05-2024</p>
                        <p>19000</p>
                    </div>
                    <div class='singleResults'>
                        <p>19000*0,16</p>
                        <p>19000*0,1</p>
                        <button class='moreCalc'>Calculos extras +</button>
                    </div>
                </div>
                <div class='extraCalcDiv'>
                    <div class='driverResult'>
                        <div class='driverHeader'>
                            <h3>Motorista</h3>
                            <button class='moreInput'>+</button>
                        </div>
                        <input type="text"><input type="text"><input type="text">
                    </div>
                    <div class='truckResult'>
                        <div class='driverHeader'>
                            <h3>Caminhão</h3>
                        </div>
                        <input type="text"><input type="text"><input type="text">
                    </div>
                </div>
            </div>
        </div>
        <button><a href="../../index.php">Voltar</a></button>
    </div>
</body>
    <script>
        $(".extraCalcDiv").hide();
        $(".moreCalc").click(function(){
            var extraCalcDiv = $(this).parent().parent().siblings(".extraCalcDiv");
            extraCalcDiv.slideToggle('slow');
        });
    </script>
</html>

<?php 
$conn->close();
?>