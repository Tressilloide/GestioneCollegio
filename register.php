<?php
    include 'connessione.php';
    include 'funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Inserimento contatto</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <style>
            body {
                background-attachment: fixed;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            table {
                border-collapse: collapse;
            }
            td, th {
                border: 1px solid;
            }
            .register-container {
                position: absolute;
                background: rgba(255, 255, 255, 0.9);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 255, 0.3);
                max-width: 400px;
                width: 100%;
                text-align: center;
            }
            .input-group .form-control,
            .input-group {
                width: 100%;
            }
            .input-group-addon {
                width: 50%;
                text-align: center;
            }
            .image-text {
                position: absolute;
                top: 50%; 
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 1.5em; /* Ridotto per miglior adattabilità */
                background-color: rgba(255, 255, 255, 0.8);
                padding: 10px 20px; 
                border-radius: 20px;
                text-align: center; 
                box-shadow: 0 2px 10px rgba(0, 0, 255, 0.3); 
                color: #344ceb;
                font-weight: bold;
                width: 90%;
                max-width: 400px;
            }
            label {
                font-size: 0.9em;
                color: black;
            }
            button, a.btn {
                width: 100%; /* Rende i pulsanti più accessibili su mobile */
                margin-top: 5px;
            }
            button[name="btnInserisci"] {
                background-color:#344ceb;
                color: white;
            }
            button[name="btnInserisci"]:hover {
                background-color: #2a3b9d;
            }
            button[name="btnReset"] {
                background-color:#344ceb;
                color: white;
            }
            button[name="btnReset"]:hover {
                background-color: #2a3b9d;
            }
            a[name="btnBack"] {
                background-color:#344ceb;
                color: white;
            }
            a[name="btnBack"]:hover {
                background-color: #2a3b9d;
            }
            @media screen and (max-width: 600px) {
                .image-text {
                    font-size: 1.2em;
                }
            }
        </style>
    </head>
    <body>
        
        <div class="register-container">
                <h2 style="font-weight: bold;">REGISTRAZIONE</h2>
                    <?php
                        if (!isset($error_message)) {
                            if (isset($_POST['btnInserisci'])) {
                                $nome = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtNome']))));
                                $cognome = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtCognome']))));
                                $email = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtEmail']));  
                                $user_password = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtPassword']));
                                $confirm_password = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtConfirmPassword']));
                                
                                if ($user_password !== $confirm_password) {
                                    $message = "Le password non coincidono!";
                                } else {
                                    $password_hash = password_hash($user_password, PASSWORD_DEFAULT);
                                    $query_insert = "INSERT INTO tdocente (nome, cognome, email, user_password) VALUES('$nome', '$cognome', '$email', '$password_hash')";

                                    try {
                                        $insert = @mysqli_query($db_conn, $query_insert);
                                        $message = $insert ? "Utente creato con successo!" : "Errore nella creazione dell'utente!";
                                        header("refresh:3; index.php");
                                    } catch (Exception $ex) {
                                        $message = $ex->getMessage();
                                        header("refresh:3");
                                    }
                                }
                                echo $message;
                            } else {
                    ?>
                <form name="frmContattiInserimento" method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="setEmail()">
                    <div class="form-group">
                        <label for="txtEmail">Email:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="txtEmailPrefix" placeholder="nome.cognome" required>
                            <span class="input-group-addon">@buonarroti.tn.it</span>
                        </div>
                        <input type="hidden" id="txtEmail" name="txtEmail">
                    </div>
                    <div class="form-group">
                        <label for="txtNome">Nome:</label>
                        <input type="text" class="form-control" id="txtNome" name="txtNome" placeholder="Inserisci il nome" required>
                    </div>
                    <div class="form-group">
                        <label for="txtCognome">Cognome:</label>
                        <input type="text" class="form-control" id="txtCognome" name="txtCognome" placeholder="Inserisci il cognome" required>
                    </div>
                    <div class="form-group">
                        <label for="txtPassword">Password:</label>
                        <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Inserisci la password" required>
                    </div>
                    <div class="form-group">
                        <label for="txtConfirmPassword">Conferma Password:</label>
                        <input type="password" class="form-control" id="txtConfirmPassword" name="txtConfirmPassword" placeholder="Conferma la password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="btnInserisci">Registrati</button>
                    <button type="reset" class="btn btn-primary" name="btnReset">Cancella</button>
                    <a href="index.php" class="btn btn-primary" name="btnBack">Torna indietro</a>
                </form>
        </div>
        <?php
                }
            } else {
                echo $error_message;
                header("refresh:2; index.php");
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

        function autoFillNameAndSurname() {
            var emailPrefix = document.getElementById('txtEmailPrefix').value;
            var parts = emailPrefix.split('.');
            if (parts.length === 2) {
                document.getElementById('txtNome').value = capitalizeFirstLetter(parts[0]);
                document.getElementById('txtCognome').value = capitalizeFirstLetter(parts[1]);
            }
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
        }

        function togglePasswordVisibility() {
            var passwordField = document.getElementById('txtPassword');
            var confirmPasswordField = document.getElementById('txtConfirmPassword');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                confirmPasswordField.type = "text";
            } else {
                passwordField.type = "password";
                confirmPasswordField.type = "password";
            }
        }

        document.getElementById('txtEmailPrefix').addEventListener('input', autoFillNameAndSurname);
        document.getElementById('txtEmailPrefix').addEventListener('blur', autoFillNameAndSurname);
    </script>
</html>
