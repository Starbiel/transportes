

<?php 
include "php/database/server.php";

$imageId = 1; // Substitua pelo ID correto da imagem
$sql = "SELECT imageType, link FROM image WHERE id_image = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $imageData = $result->fetch_assoc();

        if ($imageData) {
            echo $imageData['imageType'];
        } else {
            echo "Imagem não encontrada para o ID $imageId";
        }
    } else {
        echo "Erro ao executar a consulta: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Erro ao preparar a declaração: " . $conn->error;
}

$conn->close();
?>