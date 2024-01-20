<?php 
include "../../database/server.php";
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "UPDATE driver SET id_truck = NULL WHERE id_truck = ?";
    $statement = $conn->prepare($sql);
    if(isset($_SESSION['idTruck'])) {
        $statement->bind_param('i', $_SESSION['idTruck']);
    }
    else {
        $statement->bind_param('i', $_POST['truckId']);
    }
    $sql = "UPDATE truck SET id_driver = NULL WHERE id_driver = ?";
    $statement = $conn->prepare($sql);
    if(isset($_SESSION['idTruck'])) {
        $statement->bind_param('i', $_POST['driverId']);
    }
    else {
        $statement->bind_param('i', $_SESSION['idDriver']);
    }
    $statement->execute();
    $sql = "UPDATE driver SET id_truck = ? WHERE id_driver = ?";
    $statement = $conn->prepare($sql);
    if(isset($_SESSION['idTruck'])) {
        $statement->bind_param('ii', $_SESSION['idTruck'], $_POST['driverId']);
    }
    else {
        $statement->bind_param('ii', $_POST['truckId'], $_SESSION['idDriver']);
    }
        $statement->execute();
        $sql = "UPDATE truck SET id_driver = ? WHERE id_truck = ?";
        $statement = $conn->prepare($sql);
        if(isset($_SESSION['idTruck'])) {
            $statement->bind_param('ii', $_POST['driverId'], $_SESSION['idTruck']);
        }
        else {
            $statement->bind_param('ii', $_SESSION['idDriver'], $_POST['truckId']);
        }
        $statement->execute();
        session_destroy();
        $conn->close();
    }

else {
    $conn->close();
    session_destroy();
    header('Location: ../../../index.php');
}
?>