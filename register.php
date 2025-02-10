<?php
    include 'connessione.php';
    include 'funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Contatto inserimento</title>
        <meta charset="UTF-8">
         <style>
            table {
                border-collapse: collapse;
            }
            td, th {
                border: 1px solid;
            }
        </style>
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
                    <form name="frmContattiInserimento" method="post" action="<?=$_SERVER['PHP_SELF']?>">
                        <table>
                            <tr>
                                <td>Nome</td>
                                <td>
                                    <input type="text" name="txtNome">
                                </td>
                            </tr>
                            <tr>
                                <td>Cognome</td>
                                <td>
                                    <input type="text" name="txtCognome">
                                </td>
                            </tr>
                            <tr>
                                <td>Codice Fiscale</td>
                                <td>
                                    <input type="text" name="txtCodiceFiscale">
                                </td>
                            </tr>
                            <tr>
                                <td>Data di nascita</td>
                                <td>
                                    <input type="date" name="txtDataNascita">
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>
                                    <input type="email" name="txtEmail">
                                </td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td>
                                    <input type="password" name="txtPassword">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <input type="submit" name="btnInserisci" value="Inserisci">
                                    <input type="reset"  name="btnReset" value="Cancella">
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <input type="button" value="Torna indietro" onClick="javascript:history.back()">
                    <button onClick="javascript:history.back()">Torna indietro</button>
                    <a href="index.php">Torna indietro</a>
        <?php
                }
            } else {
                echo $error_message;

                header("refresh:3; index.php");
            }
        ?>
    </body>
</html>