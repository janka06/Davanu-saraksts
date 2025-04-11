<?php
session_start();
require_once "db.php";

$isLoggedIn = $_SESSION["OK"] ?? false;
$userId = isset($_SESSION["user"]) ? (int)$_SESSION["user"] : null;

// Fetch user details if logged in
$userName = "ERROR: User not found.";
if ($isLoggedIn && $userId) {
    $stmt = $conn->prepare("SELECT name_sname FROM jk_users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($fetchedUserName);
    if ($stmt->fetch()) {
        $userName = $fetchedUserName;
    }
    $stmt->close();
}

// Fetch wishlist items
$wishlistItems = [];
$sql = "SELECT jk_wishlist_items.*, jk_users.name_sname 
        FROM jk_wishlist_items 
        JOIN jk_users ON jk_wishlist_items.user_id = jk_users.id 
        ORDER BY jk_wishlist_items.date DESC";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wishlistItems[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/gift_box.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <title>Dāvanu Saraksts</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <form class="d-flex me-lg-3" role="search" onsubmit="return false;">
                <input class="form-control me-2" type="search" id="searchInput" placeholder="Personas vārds, uzvārds">
            </form>
            <div class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                    <div class="popup" onclick="myFunction()">
                        <img src="images/user.png" width="40" height="40" class="rounded-circle me-2">
                        <span class="popuptext" id="myPopup"><?= htmlspecialchars($userName) ?></span>
                    </div>
                    <a href="profile/index.php" class="btn btn-dark me-2">Mans dāvanu saraksts</a>
                    <form action="proc.php" method="post" class="d-inline">
                        <button class="btn btn-danger me-2" type="submit">Iziet</button>
                    </form>
                <?php else: ?>
                    <a href="register.php" class="btn btn-dark me-2">Reģistrēties</a>
                    <a href="index.php" class="btn btn-light me-2">Pieslēgties</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Error messages -->
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php
        if ($_GET['error'] == 6) {
            echo "Piekļuve aizliegta, jums ir jābūt pieslēgtam ar savu kontu.";
        } ?>
    </div>
<?php endif; ?>

<div id="wishlistResults">
    <?php if (!empty($wishlistItems)): ?>
        <?php foreach ($wishlistItems as $item): ?>
            <div class="ieraksts" data-name="<?= htmlspecialchars($item['name_sname']) ?>">
            <?php
                if ($item['liked'] == 1 && (int)$item['liked_by'] === $userId) {
                    $cardClass = 'closed-card liked-by-you';
                } elseif ($item['liked'] == 1) {
                    $cardClass = 'closed-card';
                } else {
                    $cardClass = 'card';
                }                
            ?>
            <div class="<?= $cardClass ?> d-flex flex-row align-items-center p-3 justify-content-between">
                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                         class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                        <p class="mb-1 text-muted"><?= htmlspecialchars($item['description']) ?></p>
                        <p class="mb-1"><strong>Pievienojis: <?= htmlspecialchars($item['name_sname']) ?></strong></p>
                        <?php if (!empty($item['link'])): ?>
                            <p><a href="<?= htmlspecialchars($item['link']) ?>" target="_blank">Aplūkot vēlamo dāvanu</a></p>
                        <?php endif; ?>
                    </div>
                    <div class="ms-auto">
                    <?php if ($item['liked'] == 0 AND $item['user_id'] != $userId): // Show the button only if 'liked' is 0 ?>
                        <form action="config/like.php" method="post">
                            <input type="hidden" name="itemID" value="<?= $item['id']?>">
                            <button type="submit" class="btn btn-info">Iegādāšos!</button>
                        </form>
                    <?php elseif ($item['liked'] == 0 AND $item['user_id'] == $userId): // Show the 'liked' icon if 'liked' is 1?>
                        <h4>Šī ir tava dāvana :p</h4>
                    <?php elseif ($userId !== null && $item['liked'] == 1 && (int)$item['liked_by'] === $userId): ?>
                        <h4>Tu esi iegādājies šo dāvanu! :D</h4>
                    <?php else: ?>
                        <h4>Kāds to jau ir iegādājies! :D</h4>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nav atrastas nevienas dāvanas :(</p>
    <?php endif; ?>
</div>

<script>
function myFunction() {
    var popup = document.getElementById("myPopup");
    popup.classList.toggle("show");
}
</script>
<script src="js/search.js"></script>

</body>
</html>
