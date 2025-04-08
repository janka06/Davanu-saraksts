<?php
session_start();
$isLoggedIn = isset($_SESSION["OK"]) ? $_SESSION["OK"] : false;
$userId = isset($_SESSION["user"]) ? $_SESSION["user"] : null;

include("../db.php");

// Get table name from URL, if provided
$tableName = isset($_GET['table']) ? $conn->real_escape_string($_GET['table']) : null;

// --- Deletion Logic for jk_users ---
if ($tableName === 'jk_users' && isset($_GET['delete_id'])) {
    // OPTIONAL: Check if current user has admin privileges before proceeding
    // if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    //     header("Location: admin_database.php?table=jk_users&error=unauthorized");
    //     exit();
    // }

    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM jk_users WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        // Redirect to avoid resubmission of deletion if the page is refreshed.
        header("Location: admin_database.php?table=jk_users&success=1");
        exit();
    } else {
        header("Location: admin_database.php?table=jk_users&error=1");
        exit();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/gift_box.png">
    <title>Datubāzes Menedžements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/main.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .list-group-item a {
            text-decoration: none;
            font-weight: bold;
            color: #007bff;
        }
        .list-group-item a:hover {
            color: #0056b3;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
    <script>
        function filterTables() {
            let input = document.getElementById("tableSearch").value.toLowerCase();
            let items = document.querySelectorAll(".table-list li");

            items.forEach(item => {
                let text = item.textContent.toLowerCase();
                item.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body class="container mt-4">
    <h1>Datubāzes Menedžements</h1>

    <?php if (!$tableName): ?>
        <!-- Show List of Tables -->
        <h2>Pieejamās tabulas</h2>
        <ul class="list-group table-list">
            <?php
            $tablesResult = $conn->query("SHOW TABLES LIKE 'jk%'");
            if (!$tablesResult) {
                die("<p class='text-danger'>Kļūda iegūstot tabulas: " . $conn->error . "</p>");
            }

            while ($table = $tablesResult->fetch_array()):
                $table = $table[0];
            ?>
                <li class="list-group-item">
                    <a href="admin_database.php?table=<?php echo urlencode($table); ?>">
                        <?php echo htmlspecialchars($table); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <!-- Show Table Data -->
        <h2>Tabula: <span class="text-primary"><?php echo htmlspecialchars($tableName); ?></span></h2>
        <a href="admin_database.php" class="btn btn-secondary mb-3">Atpakaļ</a>

        <?php
        $result = $conn->query("SELECT * FROM `$tableName`");
        if (!$result) {
            die("<p class='text-danger'>Kļūda: " . $conn->error . "</p>");
        }

        if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <?php while ($field = $result->fetch_field()): ?>
                                <th><?php echo htmlspecialchars($field->name); ?></th>
                            <?php endwhile; ?>
                            <?php if ($tableName === "jk_users"): ?>
                                <th>Darbības</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reset pointer if needed
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                                <?php if ($tableName === "jk_users"): ?>
                                    <td>
                                        <a class="btn btn-danger" 
                                           onclick="return confirm('Vai tiešām vēlaties dzēst lietotāju?')"
                                           href="admin_database.php?table=jk_users&delete_id=<?php echo $row['id']; ?>">
                                           Dzēst
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Tabulā nav ierakstu.</p>
        <?php endif; ?>
    <?php endif; ?>
    <div class="d-flex justify-content-center mt-3">
        <a href="admin_main.php" class="btn btn-dark">Administrācijas Sākumlapa</a>
    </div>
</body>
</html>
