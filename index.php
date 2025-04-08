<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/gift_box.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="css/index.css">
    <title>Dāvanu Saraksts</title>
</head>
<body>
    <div class="loginbox">
    <h1>Dāvanu Saraksts!</h1>

    <!-- Show success/error messages -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success" role="alert">
            Reģistrācija veiksmīga! Varat pieslēgties.
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php
            if ($_GET['error'] == 1) {
                echo "Nepareizs e-pasts vai parole.";
            } elseif ($_GET['error'] == 2) {
                echo "Konts vēl nav verificēts. Lūdzu, pārbaudiet savu e-pastu.";
            }
            ?>
        </div>
    <?php endif; ?>

        <form action="cfg.php" method="post">
            <label for="epasts">E-pasts</label><br>
            <input name="login" class="form-control" id="epasts" type="email" placeholder="epasts@epasts.lv" required/>
            <label for="pswd">Parole</label><br>
            <input name="password" class="form-control" id="pswd" type="password" placeholder="parole" required/>
            <!-- An element to toggle between password visibility -->
            <input type="checkbox" onclick="myFunction()">Rādīt paroli<br><br>
            <div class="d-grid gap-3">
                <button class="btn btn-dark" id="pieslegt" type="submit">Pieslēgties</button>
                <a class="btn btn-light" href="main.php">Pieslēgties kā Viesis</a>
                <a href="register.php">Nav konta, Reģistrējies šeit!</a>
            </div>
        </form>
    </div>
    <script>
    function myFunction() {
        var x = document.getElementById("pswd");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    } 
    </script>
</body>
</html>