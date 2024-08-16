<?php
// Include your database connection
include('database.php'); 

if (isset($_POST['animal_id'])) {
    $animal_id = $_POST['animal_id'];
    $breed_id = $_POST['breed_id'];

    // Debugging line to check received animal_id
    error_log("Received animal_id: " . $animal_id);

    // Prepare the SQL statement to prevent SQL injection
    $sql = "SELECT * FROM breed_technology WHERE animal_id = :animal_id ORDER BY breed_type ASC";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo '<option value="">Select Breed Type</option>';
        foreach ($result as $row) {
            if ($row['breed_id'] == $breed_id) {
                echo '<option value="'.$row['breed_id'].'" selected>'.$row['breed_type'].'</option>';
            } else {
                echo '<option value="'.$row['breed_id'].'">'.$row['breed_type'].'</option>';
            }
        }
    } else {
        echo '<option value="">No Breed Types Available</option>';
    }
} else {
    echo '<option value="">Invalid Request</option>';
}
?>
