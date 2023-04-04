<?php
// $servername = "localhost";
$servername = "mysql.database.svc.cluster.local";
$username = "root";
$password = "mysql-password-0123456789";
$dbname = "bricks";

$conn = new mysqli($servername, $username, $password, $dbname); // Create connection
if ($conn->connect_error) {     // Check connection
    die("Connection failed: " . $conn->connect_error);
} 

$highScore = mysqli_real_escape_string($conn, $_POST['highScore']);

$sql = "INSERT INTO `highscore` (`score`) VALUES ($highScore)"; 

if ($conn->query($sql) === TRUE) {
    echo "HighScore Inserted!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();

?>

