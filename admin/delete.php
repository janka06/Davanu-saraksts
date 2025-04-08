<?php
session_start();
include("../db.php");

// 1. Check if admin is logged in
if (!isset($_SESSION["OK"]) || !isset($_SESSION["user"])) {
    // Not logged in, or no user in session
    header("Location: index.php");
    exit;
}

// This is the admin's user ID from the session
$adminId = $_SESSION["user"];

// (Optional) Double-check in the DB that this user has admin = 1
$stmtCheck = $conn->prepare("SELECT admin FROM jk_users WHERE id = ?");
$stmtCheck->bind_param("i", $adminId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
if ($resultCheck->num_rows === 1) {
    $rowCheck = $resultCheck->fetch_assoc();
    if ($rowCheck['admin'] != 1) {
        // This user is not actually an admin
        echo "Access denied: You are not an admin.";
        exit;
    }
} else {
    echo "Access denied: Invalid user session.";
    exit;
}

// 2. Get POST data
$itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : "";

// 3. Fetch the item from jk_wishlist_items
$sqlItem = "SELECT * FROM jk_wishlist_items WHERE id = ?";
$stmtItem = $conn->prepare($sqlItem);
$stmtItem->bind_param("i", $itemId);
$stmtItem->execute();
$resultItem = $stmtItem->get_result();

if ($resultItem->num_rows !== 1) {
    echo "Item not found.";
    exit;
}

$itemData = $resultItem->fetch_assoc();

// 4. Insert a record into jk_admin_delete
$userId    = $itemData['user_id'];       // The user who originally posted
$content   = $itemData['description'];   // Or whatever you consider the "post content"

$sqlInsert = "INSERT INTO jk_admin_delete (admin_id, user_id, post_content, reason, time)
              VALUES (?, ?, ?, ?, NOW())";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("iiss", $adminId, $userId, $content, $reason);
$stmtInsert->execute();

// 5. Delete the item from jk_wishlist_items
$sqlDelete = "DELETE FROM jk_wishlist_items WHERE id = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("i", $itemId);
$stmtDelete->execute();

// Redirect or show success message
header("Location: admin_main.php");
exit;
?>
