<?php 

session_start();

include "../database/server.php";
spl_autoload_register(function($class) {
  $class = '../' . lcfirst(str_replace('\\', '/', $class) . '.php');  
  $class = str_replace('php/', '', $class);
  if(file_exists($class)) {
    require $class;
  }
});
use php\validationArea\Validation;
use php\database\Execute;

$validator = (new Validation);
$betterQuery = (new Execute);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if(isset($_POST['newDriver']) && $validator->basicChecker($_POST['nameDriver']) && $validator->basicChecker($_FILES["document"])) {
    $name = $_POST['nameDriver'];
    $telefone = $_POST['telephone'];
    $img = file_get_contents($_FILES["document"]["tmp_name"]);
    $imgType = $_FILES['document']['type'];

    $sql = "INSERT INTO driver(nome, ativo, telefone) VALUES('$name', TRUE, '$telefone')";
    if($conn->query($sql)){
      $validator->idDriver = $conn->insert_id;
      $sql = "INSERT INTO image(link, id_driver, imageType) VALUES(?, ?, ?)";
      $statement = $conn->prepare($sql);
      $statement->bind_param('sis', $img, $validator->idDriver, $imgType);
      $statement->execute();
      if(isset($_SESSION['idTruck'])) {
        session_start();
        $_SESSION['idDriver'] = $validator->idDriver;
        header("Location: ../truck/truck.php");
      }
    }
  }
  else if($validator->basicChecker($_POST['delete'])) {
    $sql = "UPDATE driver SET ativo = 0 WHERE id_driver= ? ";
    $statement = $conn->prepare($sql);
    $statement->bind_param('i', $_POST['delete']);
    $statement->execute();
  }
  else if($validator->basicChecker($_POST['add'])) {
    session_start();
    $_SESSION['idDriver'] = $_POST['add'];
    header("Location: ../truck/truck.php");
  }
}

if(!isset($_SESSION['idTruck'])) {
  function creatorTD($key, $item) {
    $div = "<td>";
    if($key == 'state') {
      $div .= 'Empregado';
    }
    else 
    $div .= $item;
    $div .= "</td>";
    return $div;
  }
  function creatorTR($row) {
    $div = "<tr>";
    foreach ($row as $key => $value) {
      if($key == 'driverId' || $key == 'name' || $key == 'plate' || $key == 'state' || $key == 'number')
      $div .= creatorTD($key, $value);
    }
    $div .="<td>";
    $div .= "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method=\"post\">";
    $div .= "<button class='formTruckButton' value='". $row['driverId'] . "' name='delete'> Demitir </button>";
    $div .= "<button class='formTruckButton' value='". $row['driverId'] . "' name='add'>Associar Caminhão</button>";
    $div .= "</form>";
    $div .="</td>";
    $div .= "</tr>";
    echo $div;
  }
}
else {
  function creatorTD($key, $item) {
    $div = "<td>";
    if($key == 'state') {
      $div .= 'Empregado';
    }
    else 
    $div .= $item;
    $div .= "</td>";
    return $div;
  }
  function creatorTR($row) {
    $div = "<tr id='".$row['driverId']."' class='clickToAdd'>";
    foreach ($row as $key => $value) {
      if($key == 'driverId' || $key == 'name' || $key == 'plate' || $key == 'state' || $key == 'number')
      $div .= creatorTD($key, $value);
    }
    $div .= "</tr>";
    echo $div;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Motorista</title>
    <link rel="stylesheet" href="driver.css">
    <link rel="stylesheet" href="../../navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link
     rel="stylesheet"
     href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"
   />
   <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script type='module' src="driver.js" defer></script>
</head>
<body>
    <div id='nav-bar'>
        <button><a href="../../index.php"><p>Menu Principal</p><i class="fa-solid fa-house"></i></a></button>
        <button><a href="../truck/truck.php"><p>Caminhões</p><i class="fa-solid fa-truck"></i></i></a></button>
        <button><a href="../travel/newTravel.php"><p>Abrir viagem</p><i class="fa-solid fa-road"></i></a></button>
        <button><a href="../shipping/peddingShipping.html"><p>Acertamentos Pedentes</p><i class="fa-solid fa-pen"></i></a></button>
        <button><a href=""><p>Lucros</p><i class="fa-solid fa-money-bill"></i></a></button>
    </div>
    <div id="container">
        <div id="add-driver">
            <h3>Novo Motorista</h3>
            <form id="login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
                <input type="text" name="nameDriver" id="nameDriver" placeholder="Nome" required>
                <input type="tel" name="telephone" id="phone" placeholder="Telefone" required>
                <div class="parentContainer">
                    <div class="controlContainer">
                      <div class="inputFileHolder">
                        <a class="btn btn-flat-browse" href="#" title="Browse"><i class="fa fa-folder-open"></i></a>
                        <input id="fileInput2" accept="image/*" name="document" class="fileInput" type="file" required>
                      </div>
                      <div class="inputFileMask">
                        <input class="inputFileMaskText2" readonly="readonly" placeholder="Escolha o documento" type="text" id="inputFileMaskText2">
                      </div>
                    </div>
                  </div>
                  <button id='select' name='newDriver' class="btn btn-flat pull-right" title="Upload file"><i class="fa fa-plus-circle"></i></button>
            </form>
        </div>
        <div id="actual-drivers">
            <table>
              <thead>
                <th>ID</th>
                <th>NOME</th>
                <th>TELEFONE</th>
                <th>CONDIÇÃO</th>
                <th>CAMINHÃO</th>
              </thead>
              <?php 
                $sql = "SELECT id_driver FROM driver";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    $resultBetterQuery = $betterQuery->driverFullQuery($row['id_driver'], $conn);
                    if($resultBetterQuery['state']) {
                      creatorTR($resultBetterQuery);
                    }
                  }
                }
              ?>
            </table>
        </div>
        <button><a href="../../index.php">Voltar</a></button>
    </div>
</body>
<script>
</script>
</html>