
<?php
$host = 'localhost'; // Database host
$db_name = ''; // Database name
$username = ''; // Database username
$password = ''; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db_name :" . $e->getMessage());
}
?>
