<?php
    include 'connessione.php';
    include 'funzioni.php';
    session_start();

    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        ?> <h1>Non sei loggato, corri a loggarti.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    $messaggio = '';
    $mostra_form_voto = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['verifica_otp_collegio'])) {
            $otp_collegio = mysqli_real_escape_string($db_conn, $_POST['otp_collegio']);
            $email = mysqli_real_escape_string($db_conn, $_SESSION['email_utente']);

            // Verifica OTP del collegio
            $query_collegio = "SELECT id_collegio FROM tcollegiodocenti WHERE otp = '$otp_collegio'";
            $result_collegio = mysqli_query($db_conn, $query_collegio);

            if ($result_collegio && mysqli_num_rows($result_collegio) > 0) {
                $row_collegio = mysqli_fetch_assoc($result_collegio);
                $_SESSION['id_collegio'] = $row_collegio['id_collegio'];

                // Inserisci nella tabella partecipa
                $id_collegio = $row_collegio['id_collegio'];
                $query_docente = "SELECT id_docente FROM tdocente WHERE email = '$email'";
                $result_docente = mysqli_query($db_conn, $query_docente);

                if ($result_docente && mysqli_num_rows($result_docente) > 0) {
                    $row_docente = mysqli_fetch_assoc($result_docente);
                    $id_docente = $row_docente['id_docente'];
                    $ora_entrata = date('H:i:s');

                    // Inserisci il record nella tabella partecipa
                    $query_partecipa = "INSERT INTO partecipa (id_collegio, id_docente, ora_entrata, ora_uscita) 
                                        VALUES ('$id_collegio', '$id_docente', '$ora_entrata', NULL)";
                    if (mysqli_query($db_conn, $query_partecipa)) {
                        $messaggio = 'Accesso al collegio registrato con successo. Inserisci l\'OTP della votazione.';
                    } else {
                        $messaggio = 'Errore durante la registrazione dell\'accesso al collegio.';
                    }
                } else {
                    $messaggio = 'Errore: docente non trovato.';
                }
            } else {
                $messaggio = 'OTP del collegio non valida.';
            }
        } elseif (isset($_POST['verifica_otp'])) {
            $otp = mysqli_real_escape_string($db_conn, $_POST['otp']);
            $email = mysqli_real_escape_string($db_conn, $_SESSION['email_utente']);

            // Verifica OTP della votazione
            $query_otp = "SELECT id_votazione FROM tvotazione WHERE otp = '$otp'";
            $result_otp = mysqli_query($db_conn, $query_otp);

            if ($result_otp && mysqli_num_rows($result_otp) > 0) {
                $row_otp = mysqli_fetch_assoc($result_otp);
                $id_votazione = $row_otp['id_votazione'];

                $query_ammesso = "SELECT a.id_docente 
                                  FROM ammesso a
                                  JOIN tdocente d ON a.id_docente = d.id_docente
                                  WHERE a.id_votazione = '$id_votazione' AND d.email = '$email'";
                $result_ammesso = mysqli_query($db_conn, $query_ammesso);

                if ($result_ammesso && mysqli_num_rows($result_ammesso) > 0) {
                    $row_ammesso = mysqli_fetch_assoc($result_ammesso);
                    $_SESSION['id_votazione'] = $id_votazione;
                    $_SESSION['id_docente'] = $row_ammesso['id_docente'];
                    $mostra_form_voto = true;
                } else {
                    $messaggio = 'Non sei ammesso a votare per questa votazione.';
                }
            } else {
                $messaggio = 'OTP della votazione non valida.';
            }
        } elseif (isset($_POST['invia_voto'])) {
            $id_votazione = $_SESSION['id_votazione'];
            $id_docente = $_SESSION['id_docente'];
            $voto = mysqli_real_escape_string($db_conn, $_POST['voto']);
            $ora = date('H:i:s');

        
            $query = "INSERT INTO effettua (id_docente, id_votazione, voto, ora) VALUES ('$id_docente', '$id_votazione', '$voto', '$ora')";
            if (mysqli_query($db_conn, $query)) {
                $messaggio = 'Voto inviato con successo!';
                unset($_SESSION['id_votazione']);
                unset($_SESSION['id_docente']);
            } else {
                $messaggio = 'Errore nell\'invio del voto.';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Votazione</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 0;
        }
        .container {
            background: white;
            color: black;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Votazione</h1>
        <?php if ($messaggio): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($messaggio); ?></div>
        <?php endif; ?>

        <?php if ($mostra_form_voto): ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="voto">Voto:</label>
                    <select class="form-control" id="voto" name="voto" required>
                        <option value="1">Favorevole</option>
                        <option value="0">Astenuto</option>
                        <option value="-1">Contrario</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="invia_voto">Invia Voto</button>
            </form>
        <?php else: ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="otp">OTP:</label>
                    <input type="text" class="form-control" id="otp" name="otp" required>
                </div>
                <button type="submit" class="btn btn-primary" name="verifica_otp">Verifica OTP</button>
            </form>
        <?php endif; ?>
        <br>
        <a href="areariservata.php" class="btn btn-primary">Torna indietro</a>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>