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

    <div class="container"><br>
        <?php
        $query_docenti = "SELECT COUNT(*) AS num_docenti FROM tdocente";
        $result_docenti = mysqli_query($db_conn, $query_docenti);
        $row_docenti = mysqli_fetch_assoc($result_docenti);
        echo "<h3>Numero utenti registrati: " . $row_docenti['num_docenti'] . "</h3>";
        ?>

        <br><br>

        <h3>Gestione proposte, votazioni e collegi</h3>
        <a href="crea_proposta.php" class="btn btn-primary">Gestione proposte</a><br><br>

        <h3>Gestione collegi</h3>
        <a href="gestione_collegi.php" class="btn btn-primary">Gestione collegi</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>