<?php
session_start();
include("../db.php");

// Make sure the user is logged in
if (!isset($_SESSION['user'])) {
    die("You are not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab the item ID from the POST data
    $id = intval($_POST['id']);

    // 1) Get the old image name from the database
    $sql = "SELECT image FROM jk_wishlist_items WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $_SESSION['user']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the row exists and belongs to this user
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $oldImagePath = "../uploads/" . $row['image'];
        $oldImage     = $row['image'];

        // 3) Handle new image upload
        $imageName = $oldImage; // fallback if no new file is uploaded
        if (isset($_FILES['gift_image']) && $_FILES['gift_image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['gift_image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('', true) . "." . $ext;
            $targetPath = "../uploads/" . $imageName;

            if (move_uploaded_file($_FILES['gift_image']['tmp_name'], $targetPath)) {
                // Delete old file if it exists
                if ($oldImage && file_exists($oldImagePath) && $oldImage !== $imageName) {
                    unlink($oldImagePath);
                }
            } else {
                die("Error uploading the new image.");
            }
        }

        // 4) Get the other fields from POST
        $name        = $_POST['gift_name']        ?? '';
        $description = $_POST['gift_description'] ?? '';
        $link        = $_POST['gift_link']        ?? '';

        // 5) Update the database record
        $updateSql = "UPDATE jk_wishlist_items
                      SET name = ?, link = ?, description = ?, image = ?, edit_date = NOW()
                      WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssssii", $name, $link, $description, $imageName, $id, $_SESSION['user']);
        $updateStmt->execute();

        // 6) Redirect or show success message
        header("Location: ../profile/index.php?updated=1");
        exit;
    } else {
        die("Item not found or no permission to edit.");
    }
} else {
    die("Invalid request method.");
}
?>