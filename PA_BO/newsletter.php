<?php
session_start();
require_once "../config.php";
require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["creer"])) {
    $titre = $_POST["titre"];
    $contenu = $_POST["contenu"];
    $statut = "brouillon";
    $date_envoi = date("Y-m-d H:i:s");

    $conn->query("INSERT INTO NEWSLETTER (titre, contenu, date_envoi, statut) 
                  VALUES ('$titre', '$contenu', '$date_envoi', '$statut')");
    $success = "Newsletter créée avec succès !";
}

if (isset($_GET['envoyer'])) {
    $id = (int) $_GET['envoyer'];
    
    $nl = $conn->query("SELECT * FROM NEWSLETTER WHERE id = $id")->fetch_assoc();
    $abonnes = $conn->query("SELECT * FROM NEWSLETTER_ABONNES");
    
    $nb_envoyes = 0;
    while ($abonne = $abonnes->fetch_assoc()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('audify2026@gmail.com', 'Audify');
            $mail->addAddress($abonne['email'], $abonne['prenom']);

            $mail->isHTML(true);
            $mail->Subject = $nl['titre'];
            $mail->Body    = $nl['contenu'];

            $mail->send();
            $nb_envoyes++;
        } catch (Exception $e) {
            
        }
    }
    
    $conn->query("UPDATE NEWSLETTER SET statut='envoyee' WHERE id=$id");
    $success = "Newsletter envoyée à $nb_envoyes abonnés !";
}

$newsletters = $conn->query("SELECT * FROM NEWSLETTER ORDER BY date_envoi DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Newsletter - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Gestion des newsletters</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droit</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<?php if($success): ?>
    <p class="succes"><?php echo $success; ?></p>
<?php endif; ?>

<h3>Créer une newsletter</h3>
<div class="card">
    <form method="POST">
        <label>Titre</label>
        <input type="text" name="titre" required>

        <label>Contenu</label>
        <textarea name="contenu" rows="6" required></textarea>

        <button type="submit" name="creer" class="btn btn-primary">Créer</button>
    </form>
</div>

<h3>Historique des newsletters</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Date d'envoi</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>
    <?php while($nl = $newsletters->fetch_assoc()): ?>
    <tr>
        <td><?php echo $nl['id']; ?></td>
        <td><?php echo $nl['titre']; ?></td>
        <td><?php echo date('d/m/Y H:i', strtotime($nl['date_envoi'])); ?></td>
        <td><?php echo $nl['statut']; ?></td>
        <td>
            <a href="modifier_newsletter.php?id=<?php echo $nl['id']; ?>">Modifier</a>
            | <a href="newsletter.php?envoyer=<?php echo $nl['id']; ?>" onclick="return confirm('Envoyer cette newsletter à tous les abonnés ?')">Envoyer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>