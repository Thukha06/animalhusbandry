
<?php
// Database configuration
$servername = "localhost";
$dbname = "animal_husbandry";
$username = "root";
$password = "";

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);

    // Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // The line below confirm a successful connection
    http_response_code(200); // OK
} catch (PDOException $e) {
    // The line below confirm a connection failure and log the actual error
    http_response_code(503); // Service Unavailable

    header("Location: 500.php");
    error_log("Connection failed: " . $e->getMessage());
}
?>