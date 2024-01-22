<?php 


if(session_start()) {
    session_destroy();
}
include_once 'php/database/server.php';
require(__DIR__ . '/php/database/execute.php');

use php\database\Execute;

$executer = new Execute;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="container">
        <button><a href="php/drivers/newDriver.php">Cadastrar Motorista</a></button>
        <button><a href="php/truck/truck.php">Caminhões</a></button>
        <button><a href="php/travel/newTravel.php">Abrir viagem</a></button>
        <?php if($executer->shippingOpen($conn)[0])echo "<button><a href=\"../shipping/peddingShipping.php\"><p>Acertamentos Pedentes</p><i class=\"fa-solid fa-pen\"></i></a></button>";?>
        <button><a href="">Lucros</a></button>
    </div>
</body>
</html>

<?php 
$conn->close();
?>