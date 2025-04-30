<?php
$host = "localhost";
$port = 3307; // use 3307 if you changed MySQL port in XAMPP
$user = "root";
$password = ""; // replace this with your real password
$dbname = "db";

$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
