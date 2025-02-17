<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Riservata</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('lock.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-size: 20%; /* Rimpicciolisce l'immagine al 50% della sua dimensione originale */

            height: 100vh; 
        }
    </style>
</head>
<body>
    <?php
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit();
        }

        if (isset($_SESSION['if_loggato']) && $_SESSION['if_loggato'] === true) {
    ?>
        <div class="container">
            <h2>Benvenuto, <?php echo htmlspecialchars($_SESSION['nome_utente']); ?>!</h2>
            <form name="frmLogout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button type="submit" class="btn btn-danger" name="logout">Logout</button>
            </form>
        </div>
    <?php
        } else {
    ?>
        <h1>Non puoi stare su questa pagina senza essere registrato.</h1>
    <?php
            header("refresh:3; login.php");
            exit();//se non metto exit() esplode tutto (entra lostesso nell'else), non so pk
        }
    ?>
</body>
</html>