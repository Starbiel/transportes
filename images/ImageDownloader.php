<?php
require '../php/database/server.php';

$registroId = $_GET['id_image'];
if($_GET['table'] == 'image') {
$sql = "SELECT link, imageType FROM image WHERE id_image = ?";
}
else {
    $sql= "SELECT document, imageType FROM truck WHERE id_truck = ?";
}
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $registroId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (empty($row['imageType'])) {
            echo("Erro ao localizar documento");
        }
        else if($row['imageType'] != 'application/pdf') {
            header('Content-Type: ' . $row['imageType']);
            echo($row['link']);
        }
        else {
            header('Content-Type: ' . $row['imageType']);
            if($row['imageType'] == 'application/pdf') {
                header('Content-Disposition: attachment; filename="documento.pdf"');
            }
            echo($row['document']);
        }
    } else {
        header('Content-Type: application/json');
        echo "Nenhum registro encontrado para o ID $registroId";
    }

    $stmt->close();
} else {
    echo "Erro ao preparar a declaração: " . $conn->error;
    
}

$conn->close();

?>