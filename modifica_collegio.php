<?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_collegio'])) {
        $_SESSION['id_collegio'] = $_POST['id_collegio'];
    }
    
    $id_collegio = $_SESSION['id_collegio'] ?? null;
    
    if (!$id_collegio) {
        echo "<h1>Errore: Nessuna proposta selezionata.</h1>";
        exit();
    }

    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        header("Location: index.php");
        exit();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        ?> <h1>Non sei autorizzato ad accedere a questa pagina.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

    include 'connessione.php';

    // verifica se l'id_proposta è stato inviato
    if (isset($_POST['id_collegio'])) {
        $_SESSION['id_collegio'] = $_POST['id_collegio'];
    }

    // recupera l'id_proposta dalla sessione
    if (!isset($_SESSION['id_collegio'])) {
        echo "<h1>Errore: Nessun collegio selezionato.</h1>";
        exit();
    }

    // query per ottenere i dettagli della proposta
    $query = "SELECT data_collegio, ora_inizio, ora_fine, descrizione FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'";
    $result = mysqli_query($db_conn, $query);
    $proposta = mysqli_fetch_assoc($result);

    if (!$proposta) {
        echo "<h1>Errore: Collegio non trovato.</h1>";
        exit();
    }

    
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Credenziali</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
        }
        .form-group + .form-group {
            margin-top: 15px;
        }
    </style>
</head>
    <body>
        <div class="form-container">
            <h2>Modifica il collegio</h2>
            <h3>
            <?php 
                if(isset($_POST['update_info'])) {
                    $new_data_collegio = mysqli_real_escape_string($db_conn, trim($_POST['data_collegio']));
                    $new_ora_inizio = mysqli_real_escape_string($db_conn, trim($_POST['ora_inizio']));
                    $new_ora_fine = mysqli_real_escape_string($db_conn, trim($_POST['ora_fine']));
                    $new_descrizione = mysqli_real_escape_string($db_conn, trim($_POST['descrizione']));

                    $query_update = "UPDATE tcollegiodocenti SET data_collegio = '$new_data_collegio', ora_inizio = '$new_ora_inizio', ora_fine = '$new_ora_fine', descrizione = '$new_descrizione' WHERE id_collegio = '$id_collegio'";

                    if (mysqli_query($db_conn, $query_update)) {
                        echo "Collegio modificato con successo!";
                        header("refresh:3; crea_proposta.php");
                        exit();
                    } else {
                        echo "Errore nella modifica del collegio";
                        header("refresh:3; gestione_collegi.php");
                        exit();
                    }
                }
                ?>
            </h3>
            <form method="post">
                <div class="form-group">
                    <label for="nome">Data collegio:</label>
                    <input type="date" id="data_collegio" name="data_collegio" class="form-control" value="<?= htmlspecialchars($proposta['data_collegio']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="cognome">Ora inizio:</label>
                    <input type="time" id="ora_inizio" name="ora_inizio" class="form-control" value="<?= htmlspecialchars($proposta['ora_inizio']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="nome">Ora fine:</label>
                    <input type="time" id="ora_fine" name="ora_fine" class="form-control" value="<?= htmlspecialchars($proposta['ora_fine']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="cognome">Descrizione:</label>
                    <input type="text" id="descrizione" name="descrizione" class="form-control" value="<?= htmlspecialchars($proposta['descrizione']) ?>" required>
                </div>
                <button type="submit" name="update_info" class="btn btn-primary">Aggiorna Informazioni</button>
                <a href="crea_proposta.php" class="btn btn-secondary">Annulla</a>
            </form>
        </div>
    </body>
</html>
