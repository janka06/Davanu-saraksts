<?php
session_start();
$ses = $_SESSION["OK"];
$value = $_SESSION["user"];
if ($ses == TRUE) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../images/gift_box.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="../css/profile_main.css">
        <title>Manas vēlmes</title>
    </head>
    <body>

        <!-- Display success or error messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Ieraksts veiksmīgi dzēsts!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 5): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Ieraksts veiksmīgi atzīmēts kā saņemts!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['error'] == 1) {
                    echo "Kļūda dzēšot ierakstu.";
                } elseif ($_GET['error'] == 2) {
                    echo "Nederīgs pieprasījums.";
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Ieraksts veiksmīgi atjaunināts!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>


        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <!-- Mobile view toggle -->
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <!-- collapsible content -->
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <img src="../images/gift_box.png" width="40" height="40" id="giftbox" class="box me-4">
                    <a href="../main.php"><button class="btn btn-secondary" type="submit" href="profile/index.php">Atpakaļ</button></a>
                    </form>
                    <div class="navbar-nav ms-auto">
                        <div class="popup" onclick="myFunction()">
                            <img src="../images/user.png" width="40" height="40" id="user_img" class="rounded-circle me-2">
                            <span class="popuptext" id="myPopup">
                                <!-- show user name and last name on icon click -->
                            <?php
                            include("../db.php");
                            $userId = $_SESSION['user'] ?? null; 
                            if ($userId) {
                                // Use a prepared statement for better security
                                $stmt = $conn->prepare("SELECT * FROM jk_users WHERE id = ?");
                                $stmt->bind_param("i", $userId); // Assuming id is an integer
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($row = $result->fetch_assoc()) {
                                    // Check if the session user matches the query result
                                    if ($_SESSION["user"] == $row['id']) {
                                        echo $row['name_sname']; // Assuming this column exists
                                    } else {
                                        echo "ERROR: User does not match.";
                                    }
                                } else {
                                    echo "ERROR: User not found.";
                                }

                                $stmt->close();
                            } else {
                                echo "ERROR: User ID is missing.";
                            }
                            ?>
                            </span>
                        </div>
                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addToListModal">Pievienot Sarakstam</button>
                        <form action=../proc.php method="post">
                            <button class="btn btn-danger me-2" type="submit">Iziet</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Display wishlist items -->
        <div class="container wishlist-container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <!-- Keeps content centered and responsive -->
                    <?php
                    include("../db.php");
                    $sql = "SELECT * FROM jk_wishlist_items WHERE user_id = '$value'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>

                            <?php if ($row['liked'] == 0): ?>
                                <div class="your-item mb-4">
                            <?php else: ?>
                                <div class="your-item-completed mb-4">
                            <?php endif; ?>

                            <div class="list-group-item d-flex align-items-center p-3 border-bottom">
                                <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" 
                                    alt="<?= htmlspecialchars($row['name']) ?>"
                                    class="rounded me-3" 
                                    style="width: 80px; height: 80px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($row['name']) ?></h5>
                                    <p class="mb-1 text-muted"><?= htmlspecialchars($row['description']) ?></p>
                                </div>
                                <?php if ($row['liked'] == 0): ?>
                                    <div class="d-flex">
                                        <button class="btn btn-danger me-2" onclick="confirmDelete(<?= $row['id'] ?>)">Dzēst</button>
                                        <button 
                                            class="btn btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#updateItemModal"
                                            onclick="populateModalData(
                                                <?= $row['id'] ?>,
                                                '<?= addslashes($row['name']) ?>',
                                                '<?= addslashes($row['description']) ?>',
                                                '<?= addslashes($row['link']) ?>'
                                            )">
                                            Mainīt
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex">
                                        <h4>Kāds ir izvēlējies to dāvināt!</h4>
                                        <button class="btn btn-info mx-4" onclick="confirmRecieved(<?= $row['id'] ?>)">Saņēmu</button>
                                    </div>
                                <?php endif; ?>
                            </div> <!-- Closing div for list-group-item -->

                            </div> <!-- Closing div for your-item or your-item-completed -->

                        <?php endwhile;
                    else: ?>
                        <p class="text-center text-muted">Nav atrasti ieraksti.</p>
                    <?php endif; ?>
                </div> <!-- Closing div for col-lg-8 col-md-10 -->
            </div> <!-- Closing div for row justify-content-center -->
        </div> <!-- Closing div for container wishlist-container -->


        <!-- Modal code: -->
         <!-- Large Bootstrap Modal -->
        <div class="modal fade" id="addToListModal" tabindex="-1" aria-labelledby="addToListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Large modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addToListModalLabel">Pievienot dāvanu sarakstam</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../insert.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="giftName" class="form-label">Dāvanas Nosaukums</label>
                                <input type="text" class="form-control" id="giftName" name="gift_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="giftDesc" class="form-label">Apraksts</label>
                                <textarea class="form-control" id="giftDescription" name="gift_description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="giftLink" class="form-label">Saite uz dāvanu (neobligāti)</label>
                                <input type="url" class="form-control" id="giftLink" name="gift_link">
                            </div>
                            <div class="mb-3">
                                <label for="giftImage" class="form-label">Attēls</label>
                                <input type="file" class="form-control" id="giftImage" name="gift_image" accept="image/*" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Pievienot</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aizvērt</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal code: -->
         <!-- Large Bootstrap Modal -->
         <div class="modal fade" id="updateItemModal" tabindex="-1" aria-labelledby="updateItemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Large modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateItemModalLabel">Mainīt dāvanu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <form action="../config/update.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="updategiftId">
                        
                        <div class="mb-3">
                            <label for="giftName" class="form-label">Dāvanas Nosaukums</label>
                            <input type="text" class="form-control" id="updategiftName" name="gift_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="giftDescription" class="form-label">Apraksts</label>
                            <textarea class="form-control" id="updategiftDescription" name="gift_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="giftLink" class="form-label">Saite uz dāvanu (neobligāti)</label>
                            <input type="url" class="form-control" id="updategiftLink" name="gift_link">
                        </div>
                        <div class="mb-3">
                            <label for="giftImage" class="form-label">Attēls</label>
                            <input type="file" class="form-control" id="updategiftImage" name="gift_image" accept="image/*" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Atjaunināt</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aizvērt</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    <!-- JavaScript for delete confirmation -->
    <script>
    function confirmDelete(id) {
        if (confirm("Vai tiešām vēlaties dzēst šo ierakstu?")) {
            window.location.href = "../config/delete.php?id=" + id;
        }
    }
    function confirmRecieved(id) {
        if (confirm("Vai tiešām esi saņēmis šo dāvanu?")) {
            window.location.href = "../config/recieved.php?id=" + id;
        }
    }
    </script>
    </body>
    <script>
    // When the user clicks on <div>, open the popup
    function myFunction() {
    var popup = document.getElementById("myPopup");
    popup.classList.toggle("show");
    }
    </script>
    <script>
        function populateModalData(id, name, description, link) {
        // Set hidden input for ID
        document.getElementById('updategiftId').value = id;

        // Set the other form fields
        document.getElementById('updategiftName').value = name;
        document.getElementById('updategiftDescription').value = description;
        document.getElementById('updategiftLink').value = link;

        // If you need to remove the “required” attribute on file for optional changes:
        // document.getElementById('giftImage').removeAttribute('required');
    }
    </script>
    </html>
<?php
} else {
    header("Location: ../main.php");
}
?>