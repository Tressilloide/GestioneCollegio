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
        $descrizione_votazione = mysqli_real_escape_string($db_conn, $_POST['descrizione_votazione']);
        $ora_inizio = mysqli_real_escape_string($db_conn, $_POST['ora_inizio']);
        $ora_fine = mysqli_real_escape_string($db_conn, $_POST['ora_fine']);
        $id_proposta = mysqli_real_escape_string($db_conn, $_POST['id_proposta']);
        $id_collegio = mysqli_real_escape_string($db_conn, $_POST['id_collegio']);

        // Genera un OTP di 3 cifre
        $otp = rand(100, 999);

        $query_votazione = "INSERT INTO tvotazione (descrizione, ora_inizio, ora_fine, id_proposta, id_collegio, otp) 
                            VALUES ('$descrizione_votazione', '$ora_inizio', '$ora_fine', '$id_proposta', '$id_collegio', '$otp')";

        if (mysqli_query($db_conn, $query_votazione)) {
            $id_votazione = mysqli_insert_id($db_conn);
            // Redirect to visualizza_otp.php with the OTP and ID votazione
            header("Location: visualizza_otp.php?otp=$otp&id_votazione=$id_votazione");
            exit();
        } else {
            echo "<h2>Errore nella creazione della votazione: " . mysqli_error($db_conn) . "</h2>";
        }
    }

    // Recupera le proposte esistenti per il menu a tendina
    $proposte_result = mysqli_query($db_conn, "SELECT id_proposta, titolo FROM tproposta");

    // Recupera l'ID del collegio dalla URL
    $id_collegio = isset($_GET['id_collegio']) ? $_GET['id_collegio'] : '';

    // Recupera il titolo del collegio dal database
    $collegio_titolo = '';
    if ($id_collegio) {
        $collegio_result = mysqli_query($db_conn, "SELECT descrizione FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'");
        if ($collegio_result && mysqli_num_rows($collegio_result) > 0) {
            $collegio_row = mysqli_fetch_assoc($collegio_result);
            $collegio_titolo = $collegio_row['descrizione'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Crea Votazione</title>
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
    <h1>Crea una nuova votazione</h1>

    <div class="container">
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
                <label for="collegio_titolo">Collegio:</label>
                <input type="text" class="form-control" id="collegio_titolo" name="collegio_titolo" value="<?php echo htmlspecialchars($collegio_titolo); ?>" readonly>
                <input type="hidden" name="id_collegio" value="<?php echo htmlspecialchars($id_collegio); ?>">
            </div>
            <button type="submit" class="btn btn-primary" name="crea_votazione">Crea Votazione</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>