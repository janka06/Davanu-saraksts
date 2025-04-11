<?php
session_start();
$isLoggedIn = isset($_SESSION["OK"]) ? $_SESSION["OK"] : false;
if (!$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

include("../db.php");
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dāvanu vēsture</title>
    <link rel="icon" href="../images/gift_box.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>

<!-- Navigation bar (same as admin_main.php) -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <form class="d-flex me-lg-3" role="search" onsubmit="return false;">
                <input class="form-control me-2" type="search" id="searchInput" placeholder="Personas vārds, uzvārds" aria-label="Search">
            </form>
            <div class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                    <div class="popup" onclick="myFunction()">
                        <img src="../images/user.png" width="40" height="40" id="user_img" class="rounded-circle me-2">
                        <span class="popuptext" id="myPopup">
                            Administrators
                        </span>
                    </div>
                    <a href="admin_deleted.php"><button type="button" class="btn btn-dark me-2">Dzēstie ieraksti</button></a>
                    <a href="admin_database.php"><button type="button" class="btn btn-dark me-2">Datubāze</button></a>
                    <a href="admin_main.php"><button type="button" class="btn btn-secondary me-2">Atpakaļ</button></a>
                    <form action="../proc.php" method="post">
                        <button class="btn btn-danger me-2" type="submit">Iziet</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Dāvanu saraksta vēsture</h2>

    <?php
    $sql = "SELECT items.*, 
                   users.name_sname AS added_by,
                   liked_users.name_sname AS liked_by_name
            FROM jk_wishlist_items AS items
            JOIN jk_users AS users ON items.user_id = users.id
            LEFT JOIN jk_users AS liked_users ON items.liked_by = liked_users.id
            ORDER BY items.date DESC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
    <div class="card mb-3 shadow-sm border-0" data-name="<?= strtolower($row['added_by']) ?>">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="rounded me-3 border" style="width: 100px; height: 100px; object-fit: cover;">
                <div class="flex-grow-1">
                    <h5 class="card-title mb-1"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="mb-1 text-muted"><?= htmlspecialchars($row['description']) ?></p>
                    <?php if (!empty($row['link'])): ?>
                        <p class="mb-1"><a href="<?= htmlspecialchars($row['link']) ?>" target="_blank">Skatīt saiti</a></p>
                    <?php endif; ?>
                    <ul class="list-unstyled small mb-0">
                        <li><strong>Pievienoja:</strong> <?= htmlspecialchars($row['added_by']) ?></li>
                        <li><strong>Pievienots:</strong> <?= htmlspecialchars($row['date']) ?></li>
                        <li><strong>Labots:</strong> <?= htmlspecialchars($row['edit_date']) ?></li>
                        <li><strong>Iegādāsies:</strong> <?= $row['liked_by_name'] ? " " . htmlspecialchars($row['liked_by_name']) . "" : "" ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
        endwhile;
    else:
        echo "<p class='text-center'>Vēsture nav pieejama.</p>";
    endif;

    $conn->close();
    ?>
</div>
<script>
// Popup for admin name
function myFunction() {
    var popup = document.getElementById("myPopup");
    popup.classList.toggle("show");
}

// Search filter
document.getElementById("searchInput").addEventListener("input", function () {
    const input = this.value.toLowerCase();
    const cards = document.querySelectorAll(".card[data-name]");

    cards.forEach(card => {
        const name = card.getAttribute("data-name");
        if (name.includes(input)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
