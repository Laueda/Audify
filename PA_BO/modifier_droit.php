<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"];
$droit = $conn->query("SELECT * FROM DROITS WHERE id = $id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role                = $_POST['role'];
    $voir_artistes       = isset($_POST['voir_artistes']) ? 1 : 0;
    $creer_posts         = isset($_POST['creer_posts']) ? 1 : 0;
    $commenter           = isset($_POST['commenter']) ? 1 : 0;
    $participer_concours = isset($_POST['participer_concours']) ? 1 : 0;
    $acces_backoffice    = isset($_POST['acces_backoffice']) ? 1 : 0;
    $gerer_utilisateurs  = isset($_POST['gerer_utilisateurs']) ? 1 : 0;
    $gerer_newsletters   = isset($_POST['gerer_newsletters']) ? 1 : 0;

    $conn->query("UPDATE DROITS SET 
        role='$role',
        voir_artistes=$voir_artistes,
        creer_posts=$creer_posts,
        commenter=$commenter,
        participer_concours=$participer_concours,
        acces_backoffice=$acces_backoffice,
        gerer_utilisateurs=$gerer_utilisateurs,
        gerer_newsletters=$gerer_newsletters
        WHERE id=$id");

    header("Location: droits.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un rôle - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Modifier un rôle</h2>

<nav>
    <a href="index.php">Dashboard</a>
    <a href="users.php">Gestion utilisateurs</a>
    <a href="droits.php">Familles de droits</a>
    <a href="stats.php">Statistiques</a>
    <a href="utilisateur.php">Logs utilisateurs</a>
    <a href="newsletter.php">Newsletter</a>
    <a href="moderation.php">Modération</a>
    <a href="connectes.php">Connectés</a>
    <a href="commandes.php">Commandes playlists</a>
    <a href="../logout.php">Déconnexion</a>
</nav>

<div class="card">
    <form method="POST">
        <label>Nom du rôle</label>
        <input type="text" name="role" value="<?php echo $droit['role']; ?>" required>

        <br>
        <input type="checkbox" name="voir_artistes" style="width:auto;" <?php echo $droit['voir_artistes'] ? 'checked' : ''; ?>> Voir les artistes<br>
        <input type="checkbox" name="creer_posts" style="width:auto;" <?php echo $droit['creer_posts'] ? 'checked' : ''; ?>> Créer des posts<br>
        <input type="checkbox" name="commenter" style="width:auto;" <?php echo $droit['commenter'] ? 'checked' : ''; ?>> Commenter<br>
        <input type="checkbox" name="participer_concours" style="width:auto;" <?php echo $droit['participer_concours'] ? 'checked' : ''; ?>> Participer aux concours<br>
        <input type="checkbox" name="acces_backoffice" style="width:auto;" <?php echo $droit['acces_backoffice'] ? 'checked' : ''; ?>> Accès back-office<br>
        <input type="checkbox" name="gerer_utilisateurs" style="width:auto;" <?php echo $droit['gerer_utilisateurs'] ? 'checked' : ''; ?>> Gérer les utilisateurs<br>
        <input type="checkbox" name="gerer_newsletters" style="width:auto;" <?php echo $droit['gerer_newsletters'] ? 'checked' : ''; ?>> Gérer les newsletters<br>
        <br>

        <button type="submit">Enregistrer</button>
        <a href="droits.php">Annuler</a>
    </form>
</div>

</body>
</html>
