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
        <!-- errors or success -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success" role="alert">
                Reģistrācija veiksmīga! Lūdzu, pārbaudiet savu e-pastu, lai verificētu kontu.
            </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    if ($_GET['error'] == 1) {
                        echo "Reģistrācijas laikā radās kļūda." . urldecode($_GET['msg']);
                    } elseif ($_GET['error'] == 2) {
                        echo "E-pasts nav nosūtīts. Lūdzu, mēģiniet vēlreiz.";
                    } elseif ($_GET['error'] == 4) {
                        echo "Šis e-pasts jau ir reģistrēts!";
                    }
                    ?>
                </div>
        <?php endif; ?>
        <!-- register form -->
        <form action="new_acc.php" method="post" id="registrationForm">
            <h1>Dāvanu Sarakstu Reģistrācija</h1>
            <input name="name_sname" class="form-control" placeholder="Vārds, Uzvārds" required/><br>
            <input name="login" class="form-control" placeholder="epasts@epasts.lv" type="email" required/><br>
            <input name="pswd_1" class="form-control" type="password" id="pswd" placeholder="Parole" required/>
            <!-- An element to toggle between password visibility -->
            <input type="checkbox" onclick="myFunction()">Rādīt paroli<br><br>
            <input name="pass2" class="form-control" type="password" id="confirm_pswd" placeholder="Ievadiet vēlreiz paroli" required/>
            <!-- An element to toggle between password visibility -->
            <input type="checkbox" onclick="myFunction2()">Rādīt paroli<br><br>
            <div class="d-grid gap-3">
                <button class="btn btn-dark" id="pieslegt" type="submit">Reģistrēties</button>
                <a href="index.php">Ir konts? Pieslēdzies šeit!</a>
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
    function myFunction2() {
        var x = document.getElementById("confirm_pswd");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    } 
    </script>
    <script src="js/register.js"></script>
</body>
</html>