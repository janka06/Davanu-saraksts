<?php
session_start();
require_once "../db.php";

// Check if user is logged in
$isLoggedIn = $_SESSION["OK"] ?? false;
$userId = $_SESSION["user"] ?? null;

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

// Fetch deleted items from jk_admin_delete
$deletedItems = [];
$sql = "
    SELECT ad.*, 
           adminUser.name_sname AS admin_name, 
           userUser.name_sname  AS original_name
    FROM jk_admin_delete ad
    JOIN jk_users adminUser ON ad.admin_id = adminUser.id
    JOIN jk_users userUser  ON ad.user_id  = userUser.id
    ORDER BY ad.id DESC
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deletedItems[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/gift_box.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../css/main.css">
    <title>Dzēstie ieraksti</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <!-- Search form -->
            <form class="d-flex me-lg-3" role="search" onsubmit="return false;">
                <input class="form-control me-2" type="search" id="searchInput" placeholder="Personas vārds, uzvārds">
            </form>
            <div class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                    <div class="popup" onclick="myFunction()">
                        <img src="../images/user.png" width="40" height="40" class="rounded-circle me-2">
                        <span class="popuptext" id="myPopup">Administrators</span>
                    </div>
                    <!-- Example: Link back to admin main page or user’s page -->
                    <a href="admin_main.php" class="btn btn-dark me-2">Admin Galvenā Lapa</a>
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

<!-- You can reuse your error messages logic here if needed -->

<div id="wishlistResults">
    <?php if (!empty($deletedItems)): ?>
        <?php foreach ($deletedItems as $item): ?>
            <div class="ieraksts" data-name="<?= htmlspecialchars($item['original_name']) ?>">
                <div class="card d-flex flex-row align-items-center p-3 justify-content-between">
                    <!-- If you had an image in jk_admin_delete, you could display it here.
                         For now, let's just skip or use a placeholder: -->
                    <!--
                    <img src="uploads/<?= htmlspecialchars($item['some_image_column'] ?? 'placeholder.jpg') ?>" 
                         alt="Deleted item"
                         class="rounded me-3" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                    -->

                    <div class="flex-grow-1">
                        <!-- Who deleted it? -->
                        <h5 class="mb-1">
                            Dzēsa: <?= htmlspecialchars($item['admin_name']) ?>
                        </h5>
                        <!-- Who originally posted? -->
                        <p class="mb-1 text-muted">
                            Sākotnējais lietotājs: <?= htmlspecialchars($item['original_name']) ?>
                        </p>
                        <!-- The post content -->
                        <p class="mb-1">
                            <strong>Ieraksts:</strong> 
                            <?= nl2br(htmlspecialchars($item['post_content'])) ?>
                        </p>
                        <!-- Reason for deletion -->
                        <p class="mb-1">
                            <strong>Iemesls:</strong> 
                            <?= nl2br(htmlspecialchars($item['reason'])) ?>
                        </p>
                    </div>
                    <!-- Right side (ms-auto) can remain empty or have extra actions -->
                    <div class="ms-auto">
                        <!-- Possibly a button or additional info -->
                        <!-- <button class="btn btn-secondary">Kaut kas</button> -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nav dzēstu ierakstu :(</p>
    <?php endif; ?>
</div>

<script>
function myFunction() {
    var popup = document.getElementById("myPopup");
    popup.classList.toggle("show");
}
</script>
<script src="../js/search.js"></script>
</body>
</html>
