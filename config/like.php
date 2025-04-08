<?php
session_start();
include("../db.php");

// check if user logged in
if (!isset($_SESSION["OK"]) || !isset($_SESSION["user"])) {
    die (header("Location: ../main.php?error=6"));
}

$userID = $_SESSION["user"];

// check if item ID is provided
if (!isset($_POST["itemID"])) {
    die("Invalid request.");
}

$itemId = intval($_POST["itemID"]);

// Update the liked attribute in the database
$stmt = $conn->prepare("UPDATE jk_wishlist_items SET liked = 1, liked_by = ? WHERE id = ?");
$stmt->bind_param("ii", $userID, $itemId);

if ($stmt->execute()) {
    // Redirect back to main.php after liking the item
    header("Location: ../main.php");
} else {
    echo "Error updating record: " . $conn->error();
}

$stmt->close();
$conn->close();
?>