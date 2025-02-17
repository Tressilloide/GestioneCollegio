<?php
    include 'connessione.php';
    include 'funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Inserimento contatto</title>
        <meta charset="UTF-8">
         <style>
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
        </style>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    </head>
    <body>
        <?php
            if (!isset($error_message)) {
                if (isset($_POST['btnInserisci'])) {
                    $nome           = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtNome']))));
                    $cognome        = @mysqli_real_escape_string($db_conn, ucwords(strtolower(filtro_testo($_POST['txtCognome']))));
                    $email    = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtEmail']));  
                    $user_password   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtPassword']));
                    $confirm_password = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtConfirmPassword']));
                    
                    if ($user_password !== $confirm_password) {
                        $message = "Le password non coincidono!";
                    } else {
                        $password_hash = password_hash($user_password, PASSWORD_DEFAULT);

                        $query_insert = "INSERT INTO tcontatto (nome, cognome, email, user_password) "
                                      . "VALUES('$nome', '$cognome', '$email', '$password_hash')";

                        try {
                            $insert = @mysqli_query($db_conn, $query_insert);
                            if($insert){
                                $message = "Utente creato con successo!";
                            }else{
                                $message = "Errore nella creazione dell'utente!";
                            }

                            header("refresh:3; index.php");
                        } catch (Exception $ex) {
                            $message = $ex->getMessage();

                            header("refresh:3");
                        }
                    }

                    echo $message;
                } else {
        ?>
            <div class="container">
                <h2>REGISTRATI</h2>
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
                        <input type="text" class="form-control" id="txtNome" name="txtNome" placeholder="Inserisci il nome" required >
                    </div>
                    <div class="form-group">
                        <label for="txtCognome">Cognome:</label>
                        <input type="text" class="form-control" id="txtCognome" name="txtCognome" placeholder="Inserisci il cognome" required >
                    </div>
                    <div class="form-group">
                        <label for="txtPassword">Password:</label>
                        <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Inserisci la password" required >
                    </div>
                    <div class="form-group">
                        <label for="txtConfirmPassword">Conferma Password:</label>
                        <input type="password" class="form-control" id="txtConfirmPassword" name="txtConfirmPassword" placeholder="Conferma la password" required >
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()"> Mostra Password
                    </div>
                    <button type="submit" class="btn btn-primary" name="btnInserisci">Registrati</button>
                    <button type="reset" class="btn btn-secondary" name="btnReset">Cancella</button>
                    <a href="index.php" class="btn btn-primary">Torna indietro</a>
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