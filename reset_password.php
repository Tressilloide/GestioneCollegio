<?php
    include 'connessione.php';
    session_start();

    if (isset($_GET['token'])) {
        $token = mysqli_real_escape_string($db_conn, $_GET['token']);
        $query = "SELECT email FROM tdocente WHERE reset_token = '$token' AND reset_expiry > NOW()";
        $result = mysqli_query($db_conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $email = $user['email'];
        } else {
            echo "<script>alert('Token non valido o scaduto.'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }

    if (isset($_POST['update_password'])) {
        $new_password = mysqli_real_escape_string($db_conn, trim($_POST['new_password']));
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        $query = "UPDATE tdocente SET user_password = '$hashed_password', reset_token = NULL, reset_expiry = NULL WHERE email = '$email'";
        
        if (mysqli_query($db_conn, $query)) {
            echo "<script>alert('Password aggiornata con successo!'); window.location.href='login.php';</script>";
            exit();
        } else {
            echo "Errore durante l'aggiornamento della password: " . mysqli_error($db_conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Reimposta la tua password</h2>
        <form method="post">
            <div class="form-group">
                <label for="new_password">Nuova Password:</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <button type="submit" name="update_password" class="btn btn-primary">Aggiorna Password</button>
        </form>
    </div>
</body>
</html>
