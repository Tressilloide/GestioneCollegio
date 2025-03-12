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
    <h1>Non sei autorizzato ad accedere a questa pagina.</h1> <?php
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
            ?>
            <h2>Proposta già esistente!</h2> <?php
        } else {
            $query_proposta = "INSERT INTO tproposta (titolo, descrizione) VALUES ('$titolo', '$descrizione_proposta')";

            if (mysqli_query($db_conn, $query_proposta)) {
                ?>
                <h2>Proposta creata con successo!</h2><?php
            } else {
                ?>
                <h2>Errore nella creazione della proposta</h2>
                <?php
            }
        }
        //evita duplicato inserimento
        header("Location: crea_proposta.php");
        exit();
    } elseif (isset($_POST['salva_modifiche'])) {
        foreach ($_POST['proposte'] as $id => $proposta) {
            $titolo = mysqli_real_escape_string($db_conn, $proposta['titolo']);
            $descrizione = mysqli_real_escape_string($db_conn, $proposta['descrizione']);
            $query_update = "UPDATE tproposta SET titolo = '$titolo', descrizione = '$descrizione' WHERE id = $id";
            mysqli_query($db_conn, $query_update);
        }
        header("Location: crea_proposta.php");
        exit();
    }
}

// Recupera tutte le proposte dal database
$query_proposte = "SELECT * FROM tproposta";
$result_proposte = mysqli_query($db_conn, $query_proposte);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Crea Proposta</title>
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
    <h1>Proposte</h1>

    <div class="container">
        <h3>Crea una nuova proposta</h3>
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
            <a href="admin.php" class="btn btn-secondary btn-block">Torna indietro</a>
        </form>
        <br><br><br>
        <h3>Modifica proposte</h3>
        <form method="post" action="">
            <?php while ($row = mysqli_fetch_assoc($result_proposte)) { ?>
                <div class="form-group">
                    <label for="titolo_<?php echo $row['id']; ?>">Titolo:</label>
                    <input type="text" class="form-control" id="titolo_<?php echo $row['id']; ?>"
                        name="proposte[<?php echo $row['id']; ?>][titolo]" value="<?php echo $row['titolo']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="descrizione_<?php echo $row['id']; ?>">Descrizione:</label>
                    <input type="text" class="form-control" id="descrizione_<?php echo $row['id']; ?>"
                        name="proposte[<?php echo $row['id']; ?>][descrizione]" value="<?php echo $row['descrizione']; ?>"
                        required>
                </div>
            <?php } ?>
            <button type="submit" class="btn btn-primary" name="salva_modifiche">Salva Modifiche</button>
            <a href="admin.php" class="btn btn-secondary btn-block">Torna indietro</a>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>