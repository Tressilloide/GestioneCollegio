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
    <style>
        body {
            background-image: url('images/register.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        table {
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid;
        }
        .input-group .form-control,
        .input-group .input-group-addon {
            width: 50%;
        }
        .image-text {
            position: absolute;
            top: 50%; 
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2em;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px; 
            border-radius: 20px;
            text-align: center; 
            box-shadow: 0 2px 10px rgba(0, 0, 255, 0.3); 
            color: #344ceb;
            font-weight: bold;
            margin-bottom: 20px;
        }
        label {
            font-size: 0.67em;
            color: black;
        }
        button[name="btnLogin"] {
            background-color: #344ceb;
            color: white;
        }
        button[name="btnLogin"]:hover {
            background-color: #2a3b9d;
        }
        button[name="btnReset"] {
            background-color: #344ceb;
            color: white;
        }
        button[name="btnReset"]:hover {
            background-color: #2a3b9d;
        }
        a[name="btnBack"] {
            background-color: #344ceb;
            color: white;
        }
        a[name="btnBack"]:hover {
            background-color: #2a3b9d;
        }
    </style>
</head>
<body>
    <?php
        if (isset($_POST['btnLogin'])) {
            $email = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtEmail']));
            $user_password = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtPassword']));

            $query = "SELECT user_password, nome FROM tcontatto WHERE email = '$email'";
            $result = @mysqli_query($db_conn, $query);

            if ($result && mysqli_num_rows($result) > 0) { //se result è true e nrighe > 0
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
            <form name="frmLogin" method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="setEmail()">
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
                <div class="form-group">
                    <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()"> Mostra Password
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
    <script>
        function setEmail() {
            var emailPrefix = document.getElementById('txtEmailPrefix').value;
            var email = emailPrefix + '@buonarroti.tn.it';
            document.getElementById('txtEmail').value = email;
        }

        function togglePasswordVisibility() {
            var passwordField = document.getElementById('txtPassword');
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</html>
