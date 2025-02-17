<?php
    include 'connessione.php';
    include 'funzioni.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <?php
        if (isset($_POST['btnLogin'])) {
            $email = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtEmail']));
            $user_password = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtPassword']));

            $query = "SELECT user_password, nome FROM tcontatti WHERE email = '$email'";
            $result = @mysqli_query($db_conn, $query);

            if ($result && mysqli_num_rows($result) > 0) { //se result è tru e nrighe >0
                $row = mysqli_fetch_assoc($result); //prendo la riga (email unique quindi solo 1 riga max)
                if (password_verify($user_password, $row['user_password'])) { //confronto le 2 password (eseguo l'hash della pasword inserita e lo confronto con quello nel db)
                    echo "Login effettuato con successo!";
                    $_SESSION['nome_utente'] = $row['nome'];
                    $_SESSION['email_utente'] = $email;
                    $_SESSION['if_loggato'] = true;
                    header("refresh:3; areariservata.php");
                } else {
                    $_SESSION['if_loggato'] = false;
                    echo "Password errata!";
                }
            } else {//email non trovata
                echo "Email non trovata (ricontrolla l'email inserita, se non sei registrato, registrati in fretta :) )";
                $_SESSION['if_loggato'] = false;

            }
        } else {
    ?>
        <div class="container">
            <h2>LOGIN</h2>
            <form name="frmLogin" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                <div class="form-group">
                    <label for="txtEmail">Email:</label>
                    <input type="email" class="form-control" id="txtEmail" name="txtEmail" required placeholder="Inserisci l'email">
                </div>
                <div class="form-group">
                    <label for="txtPassword">Password:</label>
                    <input type="password" class="form-control" id="txtPassword" name="txtPassword" required placeholder="Inserisci la password">
                </div>
                <button type="submit" class="btn btn-primary" name="btnLogin">Login</button>
                <button type="reset" class="btn btn-secondary" name="btnReset">Cancella</button>
                <a href="index.php" class="btn btn-primary">Torna indietro</a>
            </form>
        </div>
    <?php
        }
    ?>
</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</html>