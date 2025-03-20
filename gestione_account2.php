<?php
    include 'connessione.php';
    session_start();

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        header("Location: login.php");
        exit();
    }

    $email = $_SESSION['email_utente'];

    // Recupera la password e altre informazioni dell'utente
    $query = "SELECT user_password, nome, cognome, email FROM tdocente WHERE email = '$email'";
    $result = mysqli_query($db_conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Dati utente
    $nome = $user['nome'];
    $cognome = $user['cognome'];
    $email = $user['email'];
    $db_password = $user['user_password']; // La password attuale memorizzata nel database

    if (isset($_POST['update_info'])) {
        $old_password = mysqli_real_escape_string($db_conn, trim($_POST['expsw']));
        $new_password = mysqli_real_escape_string($db_conn, trim($_POST['npsw']));

        // Verifica se la vecchia password è corretta
        if (password_verify($old_password, $db_password)) {
            // La vecchia password è corretta, quindi aggiorna la password
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT); // Hash della nuova password

            $update_query = "UPDATE tdocente SET user_password = '$new_password_hashed' WHERE email = '$email'";

            if (mysqli_query($db_conn, $update_query)) {
                echo "<script>alert('Password aggiornata con successo!'); window.location.href='areariservata.php';</script>";
                exit();
            } else {
                echo "Errore durante l'aggiornamento: " . mysqli_error($db_conn);
            }
        } else {
            echo "<script>alert('La password attuale non è corretta.');</script>";
        }
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
    <!-- Navbar fissa in cima -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Benvenuto, <?php echo htmlspecialchars($_SESSION['nome_utente']); ?>!</a>
            </div>
            <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="areariservata.php">Area Riservata</a></li>
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

    <div class="form-container">
        <h2>Modifica le tue credenziali</h2>

        <form method="post">
            <h4>Cambia la tua password</h4>
            <div class="form-group">
                <label for="expsw">Password attuale:</label>
                <input type="password" id="expsw" name="expsw" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="npsw">Nuova password:</label>
                <input type="password" id="npsw" name="npsw" class="form-control" required>
            </div>
            <button type="submit" name="update_info" class="btn btn-primary">Aggiorna Password</button>
        </form>
    </div>
</body>
</html>
