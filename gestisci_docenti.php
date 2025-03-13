<?php
session_start();
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

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $file = $_FILES['file']['tmp_name'];
    $handle = fopen($file, "r");
    if ($handle !== FALSE) {
        require 'connessione.php'; // Assicurati di avere un file per la connessione al database

        // Salta l'intestazione del file CSV
        fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $nome_completo = mysqli_real_escape_string($db_conn, $data[0]);
            $email = mysqli_real_escape_string($db_conn, $data[1]);

            // Dividi il nome completo in nome e cognome
            $nome_parts = explode(' ', $nome_completo, 2);
            $nome = $nome_parts[0];
            $cognome = isset($nome_parts[1]) ? $nome_parts[1] : '';

            $password = bin2hex(random_bytes(4)); // Genera una password casuale di 8 caratteri
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO tdocente (nome, cognome, email, user_password) VALUES ('$nome', '$cognome', '$email', '$hashed_password')";
            mysqli_query($db_conn, $query);
        }
        fclose($handle);
        $message = "Docenti preregistrati con successo.";
    } else {
        $message = "Errore nell'apertura del file.";
    }
} else {
    $message = "Errore nel caricamento del file.";
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Pagina Admin</title>
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
            align-items: flex-start;
            height: 100vh;
            padding: 0;
            flex-direction: column;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .center-container {
            display: flex;
            justify-content: space-evenly;
            margin-top: 80px;
            margin-left: 15%;
            margin-right: 15%;
            width: 70%;
            padding: 20px;
        }

        .center-box {
            width: 45%;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
        }

        @media (max-width: 768px) {
            .center-container {
                flex-direction: column;
                align-items: center;
                width: 90%;
                margin-left: auto;
                margin-right: auto;
            }

            .center-box {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Gestisci Docenti</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#" class="nav-link text-danger" onclick="document.getElementById('logoutForm').submit();">Logout</a>
                        <form id="logoutForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: none;">
                            <input type="hidden" name="logout">
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="center-container">
        <div class="center-box">
            <h2>Carica CSV per preregistrare i docenti</h2>
            <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Seleziona file CSV:</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Carica</button>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>
