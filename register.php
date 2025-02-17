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
                    $codice_fiscale = @mysqli_real_escape_string($db_conn, strtoupper(filtro_testo($_POST['txtCodiceFiscale'])));
                    $email    = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtEmail']));  
                    $data_nascita   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtDataNascita'])); //date("Y-n-j") data attuale; date("2024-01-07") determinata data;
                    $user_password   = @mysqli_real_escape_string($db_conn, filtro_testo($_POST['txtPassword']));
                    $password_hash = password_hash($user_password, PASSWORD_DEFAULT);
                    //I browser moderni invertono la data automaticamente usando type="date"
                    //$data_nascita   = str_replace("/", "-", $data_nascita);
                    //$timestamp      = strtotime($data_nascita);
                    //$data_nascita   = date("Y-m-d", $timestamp);

                    $query_insert = "INSERT INTO tcontatti (nome, cognome, codice_fiscale, email, data_nascita, user_password) "
                                  . "VALUES('$nome', '$cognome', '$codice_fiscale', '$email', '$data_nascita', '$password_hash')";

                    try {
                        $insert = @mysqli_query($db_conn, $query_insert);
                        if($insert){
                            $message = "Utente creato con successo!";
                        }else{
                            $message = "Errore nella creazione dell'utente!";
                        }

                        header("refresh:3; index.php");
                        //header("Location: index.php");
                    } catch (Exception $ex) {
                        $message = $ex->getMessage();

                        header("refresh:3");
                    }

                    echo $message;
                } else {
        ?>
            <div class="container">
                <h2>REGISTRATI</h2>
                <form name="frmContattiInserimento" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                    <div class="form-group">
                        <label for="txtNome">Nome:</label>
                        <input type="text" class="form-control" id="txtNome" name="txtNome" placeholder="Inserisci il nome" required >
                    </div>
                    <div class="form-group">
                        <label for="txtCognome">Cognome:</label>
                        <input type="text" class="form-control" id="txtCognome" name="txtCognome" placeholder="Inserisci il cognome"required >
                    </div>
                    <div class="form-group">
                        <label for="txtCodiceFiscale">Codice Fiscale:</label>
                        <input type="text" class="form-control" id="txtCodiceFiscale" name="txtCodiceFiscale" placeholder="Inserisci il codice fiscale"required >
                    </div>
                    <div class="form-group">
                        <label for="txtDataNascita">Data di Nascita:</label>
                        <input type="date" class="form-control" id="txtDataNascita" name="txtDataNascita"required >
                    </div>
                    <div class="form-group">
                        <label for="txtEmail">Email:</label>
                        <input type="email" class="form-control" id="txtEmail" name="txtEmail" placeholder="Inserisci l'email"required >
                    </div>
                    <div class="form-group">
                        <label for="txtPassword">Password:</label>
                        <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Inserisci la password"required >
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
</html>