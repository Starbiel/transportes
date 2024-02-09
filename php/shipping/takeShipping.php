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
use php\database\Setting;
$executer = new Execute;
$validator = (new Validation);
$settingCalc = new Setting;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($validator->basicChecker($_POST['shippingId'])) {
        $shippingId = $_POST['shippingId'];
        $extras = json_decode($_POST['extras'], true);
        $sql = "SELECT id_driver FROM shipping WHERE id_shipping = $shippingId";
        $result = $conn->query($sql);
        if($result->num_rows >0 ) {
            $row = $result->fetch_assoc();
            $resultFullQuery = $executer->driverFullQuery($row['id_driver'], $conn);
            $driverPayment = $settingCalc->driverPaymentCalc($resultFullQuery['shippingSum'], $extras);
            $truckPart = $settingCalc->truckerPartCalc($resultFullQuery['shippingSum'], $extras);
            if($_POST['returnParam'] == 'truckResult') {
                echo $truckPart;
            }
            else {
                echo $driverPayment['driverPayment'];
            }
        }
    }
}