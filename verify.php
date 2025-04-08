<?php
include("db.php");

if (isset($_GET["email"]) && isset($_GET["token"])) {
    $email = trim($_GET["email"]);
    $token = trim($_GET["token"]);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?error=1"); // Invalid email format
        exit();
    }

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT id FROM jk_users WHERE login = ? AND token = ? AND verified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mark user as verified
        $update = "UPDATE jk_users SET verified = 1, token = NULL WHERE login = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Redirect to login page with success message
        header("Location: index.php?success=1");
        exit();
    } else {
        // Log error for debugging without exposing details to users
        error_log("Verification failed - No record found for email: $email and token: $token");
        
        // Redirect with error message
        header("Location: index.php?error=2");
        exit();
    }
} else {
    // Redirect if required parameters are missing
    header("Location: index.php?error=1");
    exit();
}
?>