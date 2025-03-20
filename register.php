<?php
    include 'connessione.php';
    include 'funzioni.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 255, 0.3);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .form-group label {
            font-weight: bold;
            text-align: left;
            display: block;
        }
        .btn-primary {
            background-color: #344ceb;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
        .btn-primary:hover {
            background-color: #2a3b9d;
        }
        .input-group-addon {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="font-weight: bold;">LOGIN</h2>
        <?php
            if (isset($_POST['btnLogin'])) {
                $email = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtEmail']));
                $user_password = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtPassword']));
    
                $query = "SELECT user_password, nome FROM tdocente WHERE email = '$email'";
                $result = @mysqli_query($db_conn, $query);
    
                if ($result && mysqli_num_rows($result) > 0) { //se result è true e nrighe > 0
                    $row = mysqli_fetch_assoc($result); //prendo la riga (email unique quindi solo 1 riga max)
                    if (password_verify($user_password, $row['user_password'])) { //confronto le 2 password (eseguo l'hash della pasword inserita e lo confronto con quello nel db)
                        echo "Login effettuato con successo!";
                        $_SESSION['nome_utente'] = $row['nome'];
                        $_SESSION['email_utente'] = $email;
                        $_SESSION['if_loggato'] = true;
                        if($email == 'collaboratori@buonarroti.tn.it'){
                            $_SESSION['is_admin'] = true;
                        } else {
                            $_SESSION['is_admin'] = false;
                        }
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
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="setEmail()">
            <div class="form-group">
                <label for="txtEmail">Email:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="txtEmailPrefix" placeholder="nome.cognome" required>
                    <span class="input-group-addon">@buonarroti.tn.it</span>
                </div>
                <input type="hidden" id="txtEmail" name="txtEmail">
            </div>
            <div class="form-group">
                <label for="txtPassword">Password:</label>
                <input type="password" class="form-control" id="txtPassword" name="txtPassword" required placeholder="Inserisci la password">
            </div>
            <div class="form-group text-left">
                <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()"> Mostra Password
            </div>
            <button type="submit" class="btn btn-primary" name="btnLogin">Login</button>
            <a href="index.php" class="btn btn-secondary btn-block">Torna indietro</a>
        </form>
        <?php } ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        function setEmail() {
            var emailPrefix = document.getElementById('txtEmailPrefix').value;
            var email = emailPrefix + '@buonarroti.tn.it';
            document.getElementById('txtEmail').value = email;
        }

        function togglePasswordVisibility() {
            var passwordField = document.getElementById('txtPassword');
            passwordField.type = (passwordField.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>
