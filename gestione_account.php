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
    
    $query = "SELECT nome, cognome, email FROM tdocente WHERE email = '$email'";
    $result = mysqli_query($db_conn, $query);
    $user = mysqli_fetch_assoc($result);

    $nome = $user['nome'];
    $cognome = $user['cognome'];
    $email = $user['email'];

    if (isset($_POST['update_info'])) {
        $new_nome = mysqli_real_escape_string($db_conn, trim($_POST['nome']));
        $new_cognome = mysqli_real_escape_string($db_conn, trim($_POST['cognome']));
        
        $query = "UPDATE tdocente SET nome = '$new_nome', cognome = '$new_cognome' WHERE email = '$email'";
        
        if (mysqli_query($db_conn, $query)) {
            echo "<script>alert('Informazioni aggiornate con successo!'); window.location.href='areariservata.php';</script>";
            exit();
        } else {
            echo "Errore durante l'aggiornamento: " . mysqli_error($db_conn);
        }
    }

    if (isset($_POST['request_reset'])) {
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $query = "UPDATE tdocente SET reset_token = '$token', reset_expiry = '$expiry' WHERE email = '$email'";
        
        if (mysqli_query($db_conn, $query)) {
            $reset_link = "https://collegiodocenti.altervista.org/reset_password.php?token=$token";
            $message = "Clicca sul seguente link per reimpostare la tua password: $reset_link";
            mail($email, "Reset Password Collegio Docenti", $message, "From: no-reply@collegiodocenti.altervista.org");
            echo "<script>alert('Email di reset inviata! Controlla la tua casella di posta.');</script>";
        } else {
            echo "Errore durante la richiesta di reset: " . mysqli_error($db_conn);
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

        <!-- Modifica Nome e Cognome -->
        <form method="post">
            <h4>Informazioni Personali</h4>
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($nome); ?>" required>
            </div>
            <div class="form-group">
                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" class="form-control" value="<?php echo htmlspecialchars($cognome); ?>" required>
            </div>
            <button type="submit" name="update_info" class="btn btn-primary">Aggiorna Informazioni</button>
        </form>

        <hr>

        <!-- Richiesta Reset Password -->
        <form method="post">
            <h4>Reset Password</h4>
            <p>Riceverai un'email con il link per reimpostare la password.</p>
            <button type="submit" name="request_reset" class="btn btn-warning">Invia Email di Reset</button>
        </form>
    </div>
</body>
</html>
