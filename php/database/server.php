<?php 

$servername = "localhost";
$username = "root";
$password = "gabriel1234d";
$database = '';

$conn = mysqli_connect($servername, $username, $password);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


$sql = 'CREATE DATABASE IF NOT EXISTS trucker';

if($conn->query($sql)) {
  $database = "trucker";
  $conn = mysqli_connect($servername, $username, $password, $database);  
}
else {
    die('Erro ao criar o banco de dados');
}

$sql = 'CREATE TABLE IF NOT EXISTS carrier (
    id_carrier INT AUTO_INCREMENT,
    nome VARCHAR(200) UNIQUE NOT NULL,
    PRIMARY KEY (id_trans)
);' ;

$conn->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS truck (
	id_truck INT AUTO_INCREMENT,
    modelo VARCHAR(200) NOT NULL,
    marca VARCHAR(200) NOT NULL,
	cor VARCHAR(50),
    placa VARCHAR(7) NOT NULL,
    CONSTRAINT PK_truck PRIMARY KEY (id_truck)
);';

$conn->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS driver (
	id_driver INT AUTO_INCREMENT,
    nome VARCHAR(200) NOT NULL,
    id_truck INT,
	CONSTRAINT PK_driver PRIMARY KEY (id_driver),
    CONSTRAINT FK_driver FOREIGN KEY  (id_truck) REFERENCES truck(id_truck)
);';

$conn->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS shipping (
	id_shipping INT AUTO_INCREMENT,
    inicio DATETIME,
    final DATETIME,
    finalizado BOOL,
    CONSTRAINT PK_shipping PRIMARY KEY (id_shipping)
);';

$conn->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS travel (
	id_travel INT AUTO_INCREMENT,
    dia DATETIME,
    origem VARCHAR(200),
    destino VARCHAR(200),
    valor FLOAT,
    id_driver INT,
    id_shipping INT,
    id_carrier INT,
    CONSTRAINT PK_travel PRIMARY KEY (id_travel),
    CONSTRAINT FK_travel1 FOREIGN KEY  (id_driver) REFERENCES driver(id_driver),
    CONSTRAINT FK_travel2 FOREIGN KEY  (id_shipping) REFERENCES shipping(id_shipping),
    CONSTRAINT FK_travel3 FOREIGN KEY  (id_carrier) REFERENCES carrier(id_carrier)
);';

$conn->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS image(
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    link longblob NOT NULL,
    imageType varchar(200) NOT NULL,
    id_driver INT,
    CONSTRAINT fk_image FOREIGN KEY (id_driver) REFERENCES driver(id_driver)
    )';
    
$conn->query($sql); 
?>