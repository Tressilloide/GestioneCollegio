<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Riservata</title>
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
        <h1>Benvenuto, <?php echo htmlspecialchars($_SESSION['nome_utente']); ?>!</h1>
        <form name="frmLogout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <button type="submit" name="logout">Logout</button>
        </form>
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