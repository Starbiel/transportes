<?php 
namespace php\database;

spl_autoload_register(function($class) {
    $class = '../' . lcfirst(str_replace('\\', '/', $class) . '.php');  
    $class = str_replace('php/', '', $class);
    if(file_exists($class)) {
      require $class;
    }
  });

use php\validationArea\Validation;

$checker = new Validation;

class Execute {
    private $checker;
    private int $driverID = 0;
    private string $driverName = "";
    private string $driverNumber = "";
    private int $documentId = 0;
    private bool $driverState = True;
    private int $truckId = 0;
    private string $truckPlate = "";
    private ?int $shippingId = NULL;

    public function __construct() {
        $this->checker = new Validation;
    }


    public function driverFullQuery($driverId, $conn) {
        $sql = "SELECT * FROM driver WHERE id_driver= ? ";
        $statement = $conn->prepare($sql);
        $statement->bind_param("i", $driverId);
        $statement->execute() or die("<b>Error:</b> Problema para localizar esse ID<br/>" . mysqli_connect_error());
        $result = $statement->get_result();  
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->driverID = $row['id_driver'];
            $this->driverName = $row['nome'];
            $this->driverNumber = $row['telefone'] ?? "Sem telefone";
            $this->driverState = $row['ativo'];
            if(!empty($row['id_truck'])) {
                $sql = "SELECT * FROM truck WHERE id_truck= ? ";
                $statement = $conn->prepare($sql);
                $statement->bind_param("i", $row['id_truck']);
                $statement->execute() or die("<b>Error:</b> Problema para localizar esse ID<br/>" . mysqli_connect_error());
                $result = $statement->get_result(); 
                if($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $this->truckPlate = $row['placa'];
                    $this->truckId = $row['id_truck'];
                }
            }
            else {
                $this->truckPlate = "Sem caminhão";
            }
            $result->free();
            $sql = "SELECT id_shipping, finalizado, id_driver FROM shipping WHERE id_driver = $this->driverID AND finalizado = false";
            $resultTwo = $conn->query($sql);
            if($resultTwo->num_rows > 0) {
                $rowTwo = $resultTwo->fetch_assoc();
                $this->shippingId = $rowTwo['id_shipping'];
            }
            $conn->next_result();
        }
        return [
            'driverId' => $this->driverID,
            'name' => $this->driverName,
            'number' => $this->driverNumber,
            'state' => $this->driverState,
            'plate' => $this->truckPlate,
            'truckId' => $this->truckId,
            'documentId' => $this->documentId,
            'shippingId' => $this->shippingId
        ];
    }

    public function ImageLoader($ImageId, $conn, $table = 'image') {
        if($table == 'image') {
            $sql = "SELECT imageType, link FROM image WHERE id_image=?";
            $statement = $conn->prepare($sql);
            $statement->bind_param("i", $ImageId);
            $statement->execute() or die("<b>Error:</b> Problema para retornar uma imagem<br/>" . mysqli_connect_error());
            $result = $statement->get_result();  
            $row = $result->fetch_assoc();
            echo "<img src='data:image;base64,".base64_encode($row["link"])."'>";
        }
        else if($table == 'truck') {
            $sql = "SELECT imageType, document FROM truck WHERE id_truck=?";
            $statement = $conn->prepare($sql);
            $statement->bind_param("i", $ImageId);
            $statement->execute() or die("<b>Error:</b> Problema para retornar uma imagem<br/>" . mysqli_connect_error());
            $result = $statement->get_result();  
            $row = $result->fetch_assoc();
            echo "<img src='data:image;base64,".base64_encode($row["document"])."'>";
        } 
    }

        //file_get_contents($_FILES["imagem"]["tmp_name"]);
    function imgInsert($img, $type = NULL, $conn = NULL, $module = NULL, $truckId = NULL) {
        if($this->checker->basicChecker($img)) {
            $img = file_get_contents($img);
            if($conn != NULL && $module == 'image') {
                $stmt = $conn->prepare("INSERT INTO image(link, id_driver, imageType) VALUES (?,?)");
                $stmt->bind_param("bis", $img , $this->driverID, $type);
                $stmt->execute();
                return true;
            }
            else if($conn != NULL && $module == 'truck') {
                $stmt = $conn->prepare("UPDATE truck SET document = ?, imageType = ? WHERE id_truck = ?");
                $stmt->bind_param("ssi", $img , $type, $truckId);
                $stmt->execute();
            }
            return true;
        }
        return false;
    }

    public function executeQuery($conn, $query) {
        // Executa a query fornecida
        $result = $conn->query($query);

        if (!$result) {
            die("Erro na execução da query: " . $conn->error);
        }
        return $result;
    }

    public function returnInfo() {
        return [
            'id' => $this->driverID,
            'name' => $this->driverName,
            'number' => $this->driverNumber,
            'state' => $this->driverState,
            'plate' => $this->truckPlate,
            'truckId' => $this->truckId,
            'documentId' => $this->documentId
        ];
    }
}

?>