<?php
$servername = "localhost";
$username = "root";
$password = "linuxville";
$dbname = "imagens";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    $stmt = $conn->prepare("DELETE FROM img WHERE id = ?");
    $stmt->bind_param('i', $id);

    $stmt->execute();

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$conn->close();
?>
