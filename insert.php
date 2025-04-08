<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gift_name = $_POST["gift_name"];
    $gift_description = $_POST["gift_description"];
    $gift_link = $_POST["gift_link"];
    $user_id = $_SESSION["user"]; // Get the logged-in user ID
    $current_date = date("Y-m-d"); // Get current date

    // File upload:
    $target_dir = "uploads/"; // Where images are stored
    $target_dir = "uploads/";
    if (!is_writable($target_dir)) {
        die("Kļūda: Mape 'uploads/' nav rakstāma.");
    } else {
        echo "Success: Mape ir rakstāma!";
    }
    $imageFileType = strtolower(pathinfo($_FILES["gift_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . "." . $imageFileType; // Unique file name
    $target_file = $target_dir . $new_filename;

    // Validate image file
    if ($_FILES["gift_image"]["size"] > 5000000) { // Limit size to 5MB
        die("Kļūda: Fails pārsniedz atļauto lielumu (5MB)");
    }
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        die("Kļūda: Tikai JPG, JPEG, PNG & GIF ir atļauti.");
    }

    // Move uploaded fil
    if (move_uploaded_file($_FILES["gift_image"]["tmp_name"], $target_file)) {
        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO jk_wishlist_items (user_id, name, link, image, description, liked, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $liked = 0; // Default value for 'liked'

        $stmt->bind_param("issssis", $user_id, $gift_name, $gift_link, $new_filename, $gift_description, $liked, $current_date);

        if ($stmt->execute()) {
            echo "Dāvana pievienota veiksmīgi!";
            header("Location: profile/index.php"); // Redirect after success
            exit();
        } else {
            echo "Kļūda: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Kļūda: Neizdevās augšupielādēt attēlu.";
    }
}
?>
