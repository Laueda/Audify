<?php
session_start();
require_once "../config.php";
require_once "function.php";


if (isset($_SESSION["loggedin"]) && $_SESSION["role"] == "admin") {
    header("Location: index.php");
    exit();
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['role'] != 'admin') {
                logAction($conn, $user['id'], 'login_failed', 'PA_BO/login.php');
                $erreur = "Accès refusé. Vous n'êtes pas administrateur.";
            } else {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $user['id'];
                $_SESSION["username"] = $user['pseudo'];
                $_SESSION["role"] = $user['role'];
                logAction($conn, $user['id'], 'login_success', 'PA_BO/login.php');
                header("Location: index.php");
                exit();
            }
        } else {
            logAction($conn, null, 'login_failed', 'PA_BO/login.php');
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        logAction($conn, null, 'login_failed', 'PA_BO/login.php');
        $erreur = "Aucun compte trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Connexion Admin</h2>

<div class="card">
    <?php if($erreur): ?>
        <p class="erreur"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Mot de passe</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

</body>
</html>
