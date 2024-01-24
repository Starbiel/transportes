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
    $travelId = $_POST['travelId'];
    $sql = "SELECT * FROM travel WHERE id_travel= ? ";
    $statement = $conn->prepare($sql);
    $statement->bind_param("i", $travelId);
    $statement->execute() or die("<b>Error:</b> Problema para localizar esse ID<br/>" . mysqli_connect_error());
    $result = $statement->get_result();  
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $sql = "SELECT id_shipping FROM shipping WHERE finalizado = 0 AND id_driver= " . $row['id_driver'];
        $resultTwo = $conn->query($sql);
        if($resultTwo->num_rows > 0) {
            $rowTwo = $resultTwo->fetch_assoc();
            $shippingId = $rowTwo['id_shipping'];
        }
        else { 
            $sql = "INSERT INTO shipping(inicio, finalizado, id_driver) VALUES('$today', false, " . $row['id_driver'] . ")";
            $conn->query($sql);
            $shippingId = $conn->insert_id;
        }
        $sql = "UPDATE travel SET id_shipping = $shippingId WHERE id_travel = $travelId";
        $conn->query($sql);
        echo 'Funcinou Tudo';
    }
}

?>