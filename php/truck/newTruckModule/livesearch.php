<?php 

include '../../database/server.php';

$xmlDoc=new DOMDocument();

$q=$_GET["q"];
$type = $_GET["p"];

if(isset($_GET["r"])) {
    $firstSearch = $_GET["r"];
}

if($type != "mark") {
    $sql = "SELECT modelo.nome AS modelo, marca.nome AS marca FROM modelo, marca WHERE modelo.id_marca = marca.id_marca AND marca.nome LIKE '". $firstSearch . "%' AND modelo.nome LIKE '" . $q . "%'";
}
else {
    $sql = "SELECT marca.nome AS marca FROM marca WHERE  marca.nome LIKE '" . $q . "%'";
}

$hint= "";

if (strlen($q)>0) {
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if($type == "mark") {
            $hint .= "<div class='optionMark' onClick = \"setOn(this.className, this.innerHTML)\">" . $row['marca'] . '</div>';
        }
        else {
            $hint .= "<div class='optionBrand' onClick = \"setOn(this.className, this.innerHTML)\">" .$row['modelo'] . '</div>';
        }
    }
  }
}

if ($hint=="") {
  $response="Sem Sugestões";
} else {
  $response=$hint;
}

echo $response;

$conn->close();
?>