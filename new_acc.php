<?php
include("db.php");
$vards_uzvards = $_POST["name_sname"];
$epasts = $_POST["login"];
$parole = $_POST["pswd_1"];
// var_dump($_POST);
// $epasts = trim($_POST["login"]);

if (!filter_var($epasts, FILTER_VALIDATE_EMAIL)) {
    die("Kļūda: Nederīgs e-pasts.");
}

// Check if email already exists using prepared statements
$check_email = "SELECT id FROM jk_users WHERE login = ?";
$stmt = $conn->prepare($check_email);
$stmt->bind_param("s", $epasts);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: register.php?error=4"); // Email already exists
    exit();
}

// Hash the password using bcrypt
$hashed_password = password_hash($parole, PASSWORD_BCRYPT);

// Generate token for email verifiation
$token = bin2hex(random_bytes(16));

$sql = "INSERT INTO jk_users (name_sname, password, login, admin, verified, token) 
        VALUES (?, ?, ?, 0, 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $vards_uzvards, $hashed_password, $epasts, $token);

if ($stmt->execute()) {
    $verification_link = "https://mvg.lv/jk/verify.php?email=" . urlencode($epasts) . "&token=$token";

    // Send verification email
    $subject = "Verificē savu e-pastu";
    $message = "Uzspied uz saites, lai verificētu savu e-pastu: $verification_link";
    $headers = "From: no-reply@mvg.lv\r\n";
    $headers .= "Reply-To: no-reply@mvg.lv\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($epasts, $subject, $message, $headers)) {
        header("Location: register.php?success=1");
        exit();
    } else {
        header("Location: register.php?error=2"); // Email sending failed
        exit();
    }
} else {
    header("Location: register.php?error=1&msg=" . urlencode($conn->error)); // SQL error
    exit();
}
?>