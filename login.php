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
</head>
<body>
    <h1>Login</h1>
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
            <form name="frmLogin" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                <table>
                    <tr>
                        <td>Email</td>
                        <td>
                            <input type="email" name="txtEmail" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>
                            <input type="password" name="txtPassword" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="btnLogin" value="Login">
                            <input type="reset" name="btnReset" value="Cancella">
                        </td>
                    </tr>
                </table>
            </form>
            <br>
            <a href="index.php">Torna indietro</a>
    <?php
        }
    ?>
</body>
</html>