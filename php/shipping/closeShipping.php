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

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($validator->basicChecker($_POST['shippingId'])) {
        $shippingId = $_POST['shippingId'];
        date_default_timezone_set('America/Sao_Paulo');
        $today = date("Y-m-d H:i:s");  
        $sql = "UPDATE shipping SET finalizado = 1, final = '$today' WHERE id_shipping = ? ";
        $statement = $conn->prepare($sql);
        $statement->bind_param("i", $shippingId);
        $statement->execute() or die("<b>Error:</b> Problema ao finalizar<br/>" . mysqli_connect_error()); 
        echo "Feito : $shippingId";
        $conn->close();
    }
}

?>