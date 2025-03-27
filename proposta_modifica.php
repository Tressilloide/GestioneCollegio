<?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_proposta'])) {
        $_SESSION['id_proposta'] = $_POST['id_proposta'];
    }
    
    $id_proposta = $_SESSION['id_proposta'] ?? null;
    
    if (!$id_proposta) {
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
    if (isset($_POST['id_proposta'])) {
        $_SESSION['id_proposta'] = $_POST['id_proposta'];
    }

    // recupera l'id_proposta dalla sessione
    if (!isset($_SESSION['id_proposta'])) {
        echo "<h1>Errore: Nessuna proposta selezionata.</h1>";
        exit();
    }

    // query per ottenere i dettagli della proposta
    $query = "SELECT titolo, descrizione FROM tproposta WHERE id_proposta = '$id_proposta'";
    $result = mysqli_query($db_conn, $query);
    $proposta = mysqli_fetch_assoc($result);

    if (!$proposta) {
        echo "<h1>Errore: Proposta non trovata.</h1>";
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
        <h2>Modifica la proposta</h2>
        <h3>
        <?php 
            if(isset($_POST['update_info'])) {
                $new_titolo = mysqli_real_escape_string($db_conn, trim($_POST['titolo']));
                $new_descrizione = mysqli_real_escape_string($db_conn, trim($_POST['descrizione']));

                $query_update = "UPDATE tproposta SET titolo = '$new_titolo', descrizione = '$new_descrizione' WHERE id_proposta = '$id_proposta'";

                if (mysqli_query($db_conn, $query_update)) {
                    echo "Proposta modificata con successo!";
                    header("refresh:3; crea_proposta.php");
                    exit();
                } else {
                    echo "Errore nella modifica della proposta";
                    header("refresh:3; proposta_modifica.php");
                    exit();
                }
            }
            ?>
        </h3>
        <form method="post">
            <div class="form-group">
                <label for="nome">Titolo:</label>
                <input type="text" id="titolo" name="titolo" class="form-control" value="<?= htmlspecialchars($proposta['titolo']) ?>" required>
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
