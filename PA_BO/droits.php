<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$success = "";

if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $conn->query("DELETE FROM DROITS WHERE id = $id");
    header("Location: droits.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['creer'])) {
    $role                = $_POST['role'];
    $voir_artistes       = isset($_POST['voir_artistes']) ? 1 : 0;
    $creer_posts         = isset($_POST['creer_posts']) ? 1 : 0;
    $commenter           = isset($_POST['commenter']) ? 1 : 0;
    $participer_concours = isset($_POST['participer_concours']) ? 1 : 0;
    $acces_backoffice    = isset($_POST['acces_backoffice']) ? 1 : 0;
    $gerer_utilisateurs  = isset($_POST['gerer_utilisateurs']) ? 1 : 0;
    $gerer_newsletters   = isset($_POST['gerer_newsletters']) ? 1 : 0;

    $conn->query("INSERT INTO DROITS (role, voir_artistes, creer_posts, commenter, participer_concours, acces_backoffice, gerer_utilisateurs, gerer_newsletters) 
                  VALUES ('$role', $voir_artistes, $creer_posts, $commenter, $participer_concours, $acces_backoffice, $gerer_utilisateurs, $gerer_newsletters)");
    $success = "Rôle créé avec succès !";
}

$droits = $conn->query("SELECT * FROM DROITS");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Familles de droits - Audify</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<h1>Audify - Back Office</h1>
<h2>Familles de droits</h2>

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

<?php if ($success): ?>
    <p class="succes"><?php echo $success; ?></p>
<?php endif; ?>

<h3>Liste des rôles</h3>
<table>
    <tr>
        <th>Rôle</th>
        <th>Voir les artistes</th>
        <th>Créer des posts</th>
        <th>Commenter</th>
        <th>Participer aux concours</th>
        <th>Accès back-office</th>
        <th>Gérer les utilisateurs</th>
        <th>Gérer les newsletters</th>
        <th>Actions</th>
    </tr>
    <?php while($d = $droits->fetch_assoc()): ?>
    <tr>
        <td><?php echo $d['role']; ?></td>
        <td><?php echo $d['voir_artistes'] ? 'Oui' : 'Non'; ?></td>
        <td><?php echo $d['creer_posts'] ? 'Oui' : 'Non'; ?></td>
        <td><?php echo $d['commenter'] ? 'Oui' : 'Non'; ?></td>
        <td><?php echo $d['participer_concours'] ? 'Oui' : 'Non'; ?></td>
        <td><?php echo $d['acces_backoffice'] ? 'Oui' : 'Non'; ?></td>
        <td><?php echo $d['gerer_utilisateurs'] ? 'Oui' : 'Non'; ?></td>
        <td><?php echo $d['gerer_newsletters'] ? 'Oui' : 'Non'; ?></td>
        <td>
            <a href="modifier_droit.php?id=<?php echo $d['id']; ?>">Modifier</a>
            | <a href="droits.php?supprimer=<?php echo $d['id']; ?>" onclick="return confirm('Supprimer ce rôle ?')" class="lien-supprimer">Supprimer</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<h3>Créer un nouveau rôle</h3>
<div class="card">
    <form method="POST">
        <label>Nom du rôle</label>
        <input type="text" name="role" required>

        <br>
        <input type="checkbox" name="voir_artistes" style="width:auto;"> Voir les artistes<br>
        <input type="checkbox" name="creer_posts" style="width:auto;"> Créer des posts<br>
        <input type="checkbox" name="commenter" style="width:auto;"> Commenter<br>
        <input type="checkbox" name="participer_concours" style="width:auto;"> Participer aux concours<br>
        <input type="checkbox" name="acces_backoffice" style="width:auto;"> Accès back-office<br>
        <input type="checkbox" name="gerer_utilisateurs" style="width:auto;"> Gérer les utilisateurs<br>
        <input type="checkbox" name="gerer_newsletters" style="width:auto;"> Gérer les newsletters<br>
        <br>

        <button type="submit" name="creer">Créer</button>
    </form>
</div>

</body>
</html>