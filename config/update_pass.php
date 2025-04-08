<?php
if (mail("arvis.zalais123@gmail.com", "Test", "This is a test email", "From: no-reply@ymvg.lv")) {
    echo "Mail sent!";
} else {
    echo "Mail failed.";
}

// include("../db.php");

// $sql = "SELECT id, password FROM jk_users";
// $result = $conn->query($sql);

// while ($row = $result->fetch_assoc()) {
//     $id = $row["id"];
//     $plain_password = $row["password"];

//     // Skip if already hashed
//     if (password_needs_rehash($plain_password, PASSWORD_BCRYPT)) {
//         $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

//         // Update password in database
//         $update_sql = "UPDATE jk_users SET password = ? WHERE id = ?";
//         $stmt = $conn->prepare($update_sql);
//         $stmt->bind_param("si", $hashed_password, $id);
//         $stmt->execute();
//     }
// }

// echo "Password encryption completed.";
?>
