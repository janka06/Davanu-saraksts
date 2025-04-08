<?php
$servername = "localhost";
$username = "u547027111_mvg";
$password = "MVGskola1";

  // Create connection
$conn = mysqli_connect($servername, $username, $password, $username);
mysqli_set_charset($conn, "utf8mb4");
  // Check connection
  // if ($conn->connect_error) {
  //   die("Connection failed: " . $conn->connect_error);
  // }
  // echo "Connected successfully";
?>