<?php 
namespace php\validationArea;


class Validation {

    public int $idMark = 0;
    public int $idBrand = 0;
    public string $error = '';
    public int $idDriver = 0;
    
    public function checkMark($mark, $conn) {
        if(isset($mark)) {
            if(!empty($mark)) {
                $sql = "SELECT * FROM marca WHERE NOME = '$mark'";
                $result = $conn->query($sql);
                if($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $this->idMark = $row['ID_MARCA'];
                    return true;
                }
                if($result->num_rows < 1) {
                    $this->error =  'Marca inexistente';
                    return false;
                }
            }
        }
        $this->error =  'Por Favor coloque uma marca';
        return false;
    }

    public function checkBrand($brand, $conn, $mark) {
        if(isset($brand)) {
            if(!empty($brand)) {
                $sql = "SELECT * FROM modelo WHERE NOME = '$brand'";
                $result = $conn->query($sql);
                if($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $this->idBrand = $row['ID_MODELO'];
                    return true;
                }
                else if($result->num_rows > 1) {
                    $this->error =  'Muitos modelos';
                    return false;
                }
                else {
                    $sql = "SELECT * FROM marca WHERE NOME = '$mark'";
                    $result = $conn->query();
                    if($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        $sql = "INSERT INTO modelo(ID_MARCA, NOME) VALUES( " . $row['ID_MARCA'] . "'$brand')";
                    }
                }
            }
        }
        $this->error =  'Por Favor coloque uma marca';
        return false;
    }


    function basicChecker($something) {
        if(isset($something)) {
            if(!empty($something)) {
                return true;
            }
        }
        $this->error = 'Faltou Algo';
        return false;
    }

    function formIniChecker($something) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if($this->basicChecker($something)) {
                return true;
            }
        }
        return false;
    }
}


?>