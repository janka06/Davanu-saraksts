<?php
session_start();
include("../db.php");

$user = $_POST['login'];
$x = $_POST['password'];

$sql = "SELECT id, password FROM jk_users WHERE login = ? AND verified = 1 AND admin = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($x, $row['password'])) {
        $_SESSION["OK"] = TRUE;
        $_SESSION["user"] = $row["id"];
        $_SESSION['role'] = 'admin'; // save the user role in session
        header("Location: admin_main.php");
        exit();
    }
}

header("Location: index.php");
exit();
?>
