<?php
session_start();
include("../db.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input

    // Step 1: Get the image filename
    $sql = "SELECT image FROM jk_wishlist_items WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $_SESSION["user"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = "../uploads/" . $row['image']; // Adjust path if needed

        // Step 2: Delete the file if it exists
        if (!empty($row['image']) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Step 3: Delete the database entry
        $deleteStmt = $conn->prepare("DELETE FROM jk_wishlist_items WHERE id = ? AND user_id = ?");
        $deleteStmt->bind_param("ii", $id, $_SESSION["user"]);

        if ($deleteStmt->execute()) {
            header("Location: ../profile/index.php?success=1");
        } else {
            header("Location: ../profile/index.php?error=1");
        }

        $deleteStmt->close();
    } else {
        header("Location: ../profile/index.php?error=2"); // Item not found or unauthorized access
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../profile/index.php?error=2"); // Invalid request
}
exit();
