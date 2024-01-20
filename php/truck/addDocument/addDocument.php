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

$validator = new Validation;
$executer = new Execute;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($validator->basicChecker($_POST) && $validator->basicChecker($_FILES)) {
        $img = $_FILES['document']['tmp_name'];
        $type = $_FILES['document']['type'];
        $truckId = $_POST['id'];
        $executer->imgInsert($img, $type, $conn, 'truck', $truckId);
    }
}

?>