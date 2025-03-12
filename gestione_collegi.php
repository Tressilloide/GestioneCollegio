<?php
session_start();

if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
    ?>
    <h1>Non sei loggato, corri a loggarti.</h1> <?php
    header("refresh:2; index.php");
    exit();
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    ?>
    <h1>Non sei autorizzato ad accedere a questa pagina.</h1>
    <?php
    header("refresh:2; index.php");
    // TEST
    exit();
}

include 'connessione.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crea_collegio'])) {
        $data_collegio = mysqli_real_escape_string($db_conn, $_POST['data_collegio']);
        $ora_inizio = mysqli_real_escape_string($db_conn, $_POST['ora_inizio']);
        $ora_fine = mysqli_real_escape_string($db_conn, $_POST['ora_fine']);
        $descrizione = mysqli_real_escape_string($db_conn, $_POST['descrizione']);

        $query_collegio = "INSERT INTO tcollegiodocenti (data_collegio, ora_inizio, ora_fine, descrizione) 
                               VALUES ('$data_collegio', '$ora_inizio', '$ora_fine', '$descrizione')";

        if (mysqli_query($db_conn, $query_collegio)) {
            echo "<h2>Collegio creato con successo!</h2>";
        } else {
            echo "<h2>Errore nella creazione del collegio: " . mysqli_error($db_conn) . "</h2>";
        }
        // Redirect per evitare duplicati
        header("Location: admin.php");
        exit();
    }
}

// Recupera i collegi esistenti per il menu a tendina
$collegi_result = mysqli_query($db_conn, "SELECT id_collegio, descrizione FROM tcollegiodocenti");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Gestione collegi</title>
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
    <div class="container"><br>
        <h2>Crea un nuovo collegio</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="data_collegio">Data Collegio:</label>
                <input type="date" class="form-control" id="data_collegio" name="data_collegio" required>
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
                <label for="descrizione">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione" name="descrizione" required>
            </div>
            <button type="submit" class="btn btn-primary" name="crea_collegio">Crea Collegio</button>
        </form>
        <br><br>
        <h2>Seleziona un collegio esistente</h2>
        <form method="get" action="crea_votazione.php">
            <div class="form-group">
                <label for="id_collegio">Collegio:</label>
                <select class="form-control" id="id_collegio" name="id_collegio" required>
                    <?php
                    if ($collegi_result && mysqli_num_rows($collegi_result) > 0) {
                        while ($row = mysqli_fetch_assoc($collegi_result)) {
                            echo "<option value='" . $row['id_collegio'] . "'>" . htmlspecialchars($row['descrizione']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nessun collegio disponibile</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Seleziona Collegio</button>
        </form>
    </div>
</body>

</html>