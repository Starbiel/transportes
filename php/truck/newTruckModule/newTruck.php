<?php 
include '../../database/server.php';

spl_autoload_register(function($class) {
    $class = '../' . '../' . lcfirst(str_replace('\\', '/', $class) . '.php');  
    $class = str_replace('php/', '', $class);
    if(file_exists($class)) {
        require $class;
    }
});
use php\validationArea\Validation;
use php\database\Execute;
$executer = new Execute;
$validator = (new Validation);
$markErr = $brandErr = $colorErr = $plateErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if($validator->checkMark($_POST['mark'], $conn)) {
        if($validator->checkBrand($_POST['brand'], $conn, $_POST['mark'])) {
            if($validator->basicChecker($_POST['color']) && $validator->basicChecker($_POST['plate'])) {
                $sql = "INSERT INTO truck(modelo, marca, cor, placa) VALUES($validator->idBrand, $validator->idMark, '". $_POST['color'] . "', '". $_POST['plate'] ."')";
                if(!$conn->query($sql)) {
                    $plateErr = 'Placa Repetida';
                }
                else {
                    $executer->imgInsert($_FILES['truckDocument']['tmp_name'], $_FILES['truckDocument']['type'], $conn, 'truck', $conn->insert_id);
                    $conn->close();
                    header('Location: ../truck.php');
                };
            }
            else {
                $plateErr = $validator->error;
            }
        }
        else {
            $brandErr = $validator->error;
        }
    }
    else {
        $markErr = $validator->error;
    }
}
    $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="scriptNewTruck.js" defer></script>
    <link rel="stylesheet" href="styleNewTruck.css">
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
        <div class="dynamicSearch">
            <span class=''><?= $markErr ?></span>
            <input class='searchInput' id='markInput' type="text" size="30" onkeyup="showResult(this.value, 'mark')" placeholder="Marca" require name='mark'> 
            <div id="livesearchmark"></div>
        </div>
        <div class="dynamicSearch">
            <span class=''><?= $brandErr ?></span>
            <input class='searchInput' id='brandInput' type="text" size="30" onkeyup="showResult(this.value, 'brand')" placeholder="Modelo" require name='brand'>
            <div id="livesearchbrand"></div>
        </div>
        <input type="text" name="color" id="colorInput" placeholder="Cor" require>
        <span><?= $plateErr ?></span>
        <input type="text" name="plate" id="plateInput" placeholder="Placa" maxlength="7" require>
        <input type="file" name="truckDocument" id="document">
        <input type="submit" value="Enviar">
    </form>

</body>
</html>