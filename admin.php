<?php
    session_start();

    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        ?> <h1>Non sei loggato, corri a loggarti.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        ?> <h1>Non sei autorizzato ad accedere a questa pagina.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    include 'connessione.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['crea_proposta'])) {
            $titolo = mysqli_real_escape_string($db_conn, $_POST['titolo']);
            $descrizione_proposta = mysqli_real_escape_string($db_conn, $_POST['descrizione_proposta']);

            // Controlla se la proposta esiste già
            $query_check = "SELECT * FROM tproposta WHERE titolo = '$titolo' AND descrizione = '$descrizione_proposta'";
            $result_check = mysqli_query($db_conn, $query_check);

            if (mysqli_num_rows($result_check) > 0) {
                echo "<h2>Proposta già esistente!</h2>";
            } else {
                $query_proposta = "INSERT INTO tproposta (titolo, descrizione) VALUES ('$titolo', '$descrizione_proposta')";

                if (mysqli_query($db_conn, $query_proposta)) {
                    echo "<h2>Proposta creata con successo!</h2>";
                } else {
                    echo "<h2>Errore nella creazione della proposta: " . mysqli_error($db_conn) . "</h2>";
                }
            }
            // Redirect per evitare duplicati
            header("Location: admin.php");
            exit();
        } elseif (isset($_POST['crea_votazione'])) {
            $descrizione_votazione = mysqli_real_escape_string($db_conn, $_POST['descrizione_votazione']);
            $ora_inizio = mysqli_real_escape_string($db_conn, $_POST['ora_inizio']);
            $ora_fine = mysqli_real_escape_string($db_conn, $_POST['ora_fine']);
            $id_proposta = mysqli_real_escape_string($db_conn, $_POST['id_proposta']);
            $id_contatto = mysqli_real_escape_string($db_conn, $_POST['id_contatto']);

            $query_votazione = "INSERT INTO tvotazione (descrizione, ora_inizio, ora_fine, id_proposta, id_contatto) 
                                VALUES ('$descrizione_votazione', '$ora_inizio', '$ora_fine', '$id_proposta', '$id_contatto')";

            if (mysqli_query($db_conn, $query_votazione)) {
                echo "<h2>Votazione creata con successo!</h2>";
            } else {
                echo "<h2>Errore nella creazione della votazione: " . mysqli_error($db_conn) . "</h2>";
            }
            // Redirect per evitare duplicati
            header("Location: admin.php");
            exit();
        }
    }

    // Recupera le proposte esistenti per il menu a tendina
    $proposte_result = mysqli_query($db_conn, "SELECT id_proposta, titolo FROM tproposta");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Pagina Admin</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
        body {
            background-image: url('images/admin.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-size: 20%;
            height: 100vh; 
        }
    </style>
</head>
<body>
    <h1>Benvenuto nella pagina riservata agli admin</h1>

    <div class="container">
        <h2>Crea una nuova proposta</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="titolo">Titolo:</label>
                <input type="text" class="form-control" id="titolo" name="titolo" required>
            </div>
            <div class="form-group">
                <label for="descrizione_proposta">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione_proposta" name="descrizione_proposta" required>
            </div>
            <button type="submit" class="btn btn-primary" name="crea_proposta">Crea Proposta</button>
        </form>

        <h2>Crea una nuova votazione</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="descrizione_votazione">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione_votazione" name="descrizione_votazione" required>
            </div>
            <div class="form-group">
                <label for="ora_inizio">Ora Inizio:</label>
                <input type="time" class="form-control" id="ora_inizio" name="ora_inizio" required>
            </div>
            <div class="form-group">
                <label for="ora_fine">Ora Fine:</label>
                <input type="time" class="form-control" id="ora_fine" name="ora_fine" required>
            </div>
            <div class="form-group">
                <label for="id_proposta">Proposta:</label>
                <select class="form-control" id="id_proposta" name="id_proposta" required>
                    <?php
                        if ($proposte_result && mysqli_num_rows($proposte_result) > 0) {
                            while ($row = mysqli_fetch_assoc($proposte_result)) {
                                echo "<option value='" . $row['id_proposta'] . "'>" . htmlspecialchars($row['titolo']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>Nessuna proposta disponibile</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_contatto">ID Contatto:</label>
                <input type="number" class="form-control" id="id_contatto" name="id_contatto" required>
            </div>
            <button type="submit" class="btn btn-primary" name="crea_votazione">Crea Votazione</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
