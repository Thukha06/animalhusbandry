<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    $stmt = $db->prepare("SELECT product_unit FROM product_type WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $product_unit = $result ? $result['product_unit'] : '';

    echo json_encode(['unit' => $product_unit]);
}
?>