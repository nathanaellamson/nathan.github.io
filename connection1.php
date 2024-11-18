
<?php
// db.php
$host = 'localhost'; // your database host
$db = 'cms'; // your database name
$user = 'root'; // your database username
$pass = ''; // your database password

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the charset to UTF-8 (optional but recommended)
$conn->set_charset("utf8");


?>

