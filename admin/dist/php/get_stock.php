<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $breed_id = $_POST['breed_id'];

    $stmt = $db->prepare("SELECT stock_animal FROM breed_animal WHERE breed_id = :breed_id");
    $stmt->bindParam(':breed_id', $breed_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stock_animal = ($result['stock_animal'] != NULL) ? $result['stock_animal'] : "No data";

    echo json_encode(['stock' => $stock_animal]);
}
?>

