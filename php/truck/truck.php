<?php 
include "../database/server.php";
if(session_start()) {
    if(isset($_SESSION['idTruck'])) {
        unset($_SESSION['idTruck']);
    }
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if($validator->basicChecker($_POST['delete'])) {
        $sql = "SELECT id_truck, id_driver FROM truck WHERE id_truck =" . $_POST['delete'];
        $result = $conn->query($sql);
        if($result->fetch_assoc()['id_driver'] === NULL) {
            $sql = "DELETE FROM truck WHERE id_truck =" . $_POST['delete'];
            $conn->query($sql);
        }
        else {
            $sql = "UPDATE driver SET id_truck = NULL WHERE id_truck =" . $_POST['delete'];
            $conn->query($sql);
            $sql = "DELETE FROM truck WHERE id_truck =" . $_POST['delete'];
            $conn->query($sql);
        }
    }
    else if($validator->basicChecker($_POST['add'])) {
        session_start();
        $_SESSION['idTruck'] = $_POST['add'];
        header("Location: ../drivers/newDriver.php");
    }
    
}

if(!isset($_SESSION['idDriver'])) {
    function innerDiv($key, $item, $conn) {
        $div = '<td>';
        if($key == 'id_driver' && $item == '') {
            $div .= 'Sem motorista';
        }
        else if($key == 'id_driver') {
            $sql = "SELECT nome FROM driver WHERE id_driver = " . $item;
            $result = $conn->query($sql);
            $div .= $result->fetch_assoc()['nome'];
        }
        else {
            $div .= $item;
        }
        $div .= '</td>';
        return $div;
    }


    function divTruckCreator($row, $conn) {
        $div = "<tr>";
        foreach ($row as $key => $value) {
            if($key != "id_truck" && $key != "document") $div .= innerDiv($key, $value, $conn);
        }
        $div .= "<td class='formTd'>";
        $div .= "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method=\"post\">";
        $div .= "<button class='formTruckButton' value='". $row['id_truck'] . "' name='delete'><p>Excluir</p><i class=\"fa-solid fa-trash\"></i> </button>";
        $div .= "<button class='formTruckButton' value='". $row['id_truck'] . "' name='add'><p>Adicionar Motorista</p><i class=\"fa-solid fa-person-circle-plus\"></i></button>";
        $div .= "</form>";
        $div .= "</td>";
        $div .= "<td>";
        if($row['document'] != '') {
            $div .= "<button class='downloadButton' value='". $row['id_truck'] . "'>Baixar Documentos</button>";
        }
        else {
            $div .= "<form class='addDocument' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method=\"post\">";
                $div .= "<input type=\"file\" name=\"document\" enctype=\"multipart/form-data\">";
                $div .= "<input type='text' name=\"id\" style='display:none' value='". $row['id_truck'] ."'>";
            $div .= "</form>";
        }
        $div .= "</td>";
        $div .= '</tr>';
        echo $div;
    }
}
else {
    function innerDiv($key, $item) {
        $div = '<td>';
        if($key == 'id_driver' && $item == '') {
            $div .= 'Sem motorista';
        }
        else {
            $div .= $item;
        }
        $div .= '</td>';
        return $div;
    }


    function divTruckCreator($row) {
        $div = "<tr class='truck' id='" . $row['id_truck'] . "'>";
        foreach ($row as $key => $value) {
            if($key != "id_truck" && $key != "document") $div .= innerDiv($key, $value);
        }
        $div .= '</tr>';
        echo $div;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caminhões</title>
    <link rel="stylesheet" href="truck.css">
    <link rel="stylesheet" href="../../navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="module" src="truck.js"></script>
</head>
<body>
    <div id='nav-bar'>
        <button><a href="../../index.php"><p>Menu Principal</p><i class="fa-solid fa-house"></i></a></button>
        <button><a href="../drivers/newDriver.php"><p>Cadastrar Motorista</p><i class="fa-solid fa-person"></i></a></button>
        <button><a href="../travel/newTravel.php"><p>Abrir viagem</p><i class="fa-solid fa-road"></i></a></button>
        <?php if($executer->shippingOpen($conn)[0])echo "<button><a href=\"../shipping/peddingShipping.php\"><p>Acertamentos Pedentes</p><i class=\"fa-solid fa-pen\"></i></a></button>";?>
        <button><a href=""><p>Lucros</p><i class="fa-solid fa-money-bill"></i></a></button>
    </div>
    <div id="container">
        <button><a href="newTruckModule/newTruck.php">Adicionar um Novo Caminhão</a></button>
        <div>
            <div id='container-truck'>
                <?php 
                    $sql = "SELECT COUNT(id_truck) FROM truck";
                    $result = $conn->query($sql)
                ?>
                <h2><?= $result->fetch_assoc()['COUNT(id_truck)']?> Caminhões</h2>
                <table id="actual-truck">
                        <?php 
                            $sql = "SELECT truck.id_truck, marca.nome as marca, modelo.nome as modelo, truck.placa, truck.id_driver, truck.document as document FROM truck, marca, modelo WHERE marca.id_marca = truck.marca AND modelo.id_modelo = truck.modelo";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    if(isset($_SESSION['idDriver'])) {
                                        if($row['id_driver'] == '' ) {
                                        divTruckCreator($row);}
                                    }
                                    else {
                                        divTruckCreator($row, $conn);
                                    }
                                }
                            }
                        ?>
                </table>
            </div>
            <button id='submitFiles' name='addDocument'>Salvar</button>
        </div>
        <button id='return'><a href="javascript:history.go(-1)">Voltar</a></button>
    </div>
</body>
</html>