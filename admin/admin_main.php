<?php
session_start();
$isLoggedIn = isset($_SESSION["OK"]) ? $_SESSION["OK"] : false;
$userId = isset($_SESSION["user"]) ? $_SESSION["user"] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/gift_box.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="../css/main.css">
    <title>Dāvanu sarakstu administrācija</title>
</head>
<body>
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
                        <a href="admin_history.php"><button type="button" class="btn btn-dark me-2">Vēsture</button></a>
                        <form action="../proc.php" method="post">
                            <button class="btn btn-danger me-2" type="submit">Iziet</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div id="wishlistResults">
        <?php
        include("../db.php");

        $sql = "SELECT jk_wishlist_items.*, jk_users.name_sname
                FROM jk_wishlist_items
                JOIN jk_users ON jk_wishlist_items.user_id = jk_users.id
                ORDER BY jk_wishlist_items.date DESC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='ieraksts' data-name='" . htmlspecialchars($row['name_sname']) . "'>";
                echo "<div class='card d-flex flex-row align-items-center p-3 justify-content-between'>";
                echo "<img src='../uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' class='rounded me-3' style='width: 80px; height: 80px; object-fit: cover;'>";
            
                echo "<div class='flex-grow-1'>";
                echo "<h5 class='mb-1'>" . htmlspecialchars($row['name']) . "</h5>";
                echo "<p class='mb-1 text-muted'>" . htmlspecialchars($row['description']) . "</p>";
                echo "<p class='mb-1'><strong>Pievienojis: " . htmlspecialchars($row['name_sname']) . "</strong></p>";
                if (!empty($row['link'])) {
                    echo "<p><a href='" . htmlspecialchars($row['link']) . "' target='_blank'>Aplūkot vēlamo dāvanu</a></p>";
                }
                echo "</div>"; // Close text section
            
                // Deletion form
                echo "<div class='ms-auto'>";
                echo "<form action='delete.php' method='post' class='d-flex flex-column'>";
                // Hidden field for item ID
                echo "<input type='hidden' name='item_id' value='" . (int)$row['id'] . "'>";
                // Reason textarea
                echo "<textarea name='reason' class='form-control mb-2' placeholder='Iemesls dzēšanai...' required></textarea>";
                // Delete button
                echo "<button type='submit' class='btn btn-danger'>Dzēst</button>";
                echo "</form>";
                echo "</div>";
            
                echo "</div>"; // Close card
                echo "</div>"; // Close ieraksts
            }            
        } else {
            echo "<p>Nav atrastas nevienas dāvanas :(</p>";
        }

        $conn->close();
        ?>
    </div>
</body>

<script>
// When the user clicks on <div>, open the popup
function myFunction() {
  var popup = document.getElementById("myPopup");
  popup.classList.toggle("show");
}
</script>
<script src="../js/search.js"></script>
</html>
