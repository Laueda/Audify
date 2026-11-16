<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$resultats = [];
$terme = "";

if (isset($_GET['suivre'])) {
    $cible_id = $_GET['suivre'];
    $conn->query("INSERT INTO ABONNEMENT (abonne_id, abonne_a_id) VALUES ($user_id, $cible_id)");
    header("Location: rechercher_users.php?terme=" . $_GET['terme']);
    exit();
}

if (isset($_GET['ne_plus_suivre'])) {
    $cible_id = $_GET['ne_plus_suivre'];
    $conn->query("DELETE FROM ABONNEMENT WHERE abonne_id = $user_id AND abonne_a_id = $cible_id");
    header("Location: rechercher_users.php?terme=" . $_GET['terme']);
    exit();
}

if (isset($_GET['terme'])) {
    $terme = trim($_GET['terme']);
    if ($terme !== "") {
        $res = $conn->query("SELECT id, pseudo, statut FROM users WHERE pseudo LIKE '%$terme%' AND id != $user_id");
        $resultats = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        foreach ($resultats as &$utilisateur) {
            $check = $conn->query("SELECT id FROM ABONNEMENT WHERE abonne_id = $user_id AND abonne_a_id = " . $utilisateur['id']);
            $utilisateur['suivi'] = $check->fetch_assoc() ? true : false;
        }
        unset($utilisateur);
    }
}

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Rechercher un utilisateur</h2>

<form method="GET" style="display:flex; gap:0.5rem; margin-bottom:2rem;">
    <input type="text" name="terme" placeholder="Pseudo..." value="<?php echo htmlspecialchars($terme); ?>" style="<?php echo $input_style; ?>">
    <button type="submit" style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">Rechercher</button>
</form>

<?php if ($terme !== "" && empty($resultats)): ?>
    <p style="color:#666;">Aucun utilisateur trouvé.</p>
<?php endif; ?>

<?php foreach ($resultats as $utilisateur): ?>
<div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:0.75rem; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <strong style="color:#f2ead8;"><?php echo htmlspecialchars($utilisateur['pseudo']); ?></strong>
        <span style="color:#c8a415; font-size:0.85rem; margin-left:0.5rem;">(<?php echo htmlspecialchars($utilisateur['statut']); ?>)</span>
    </div>
    <?php if ($utilisateur['suivi']): ?>
        <a href="rechercher_users.php?ne_plus_suivre=<?php echo $utilisateur['id']; ?>&terme=<?php echo $terme; ?>"
           style="color:#e74c3c; text-decoration:none; border:1px solid #e74c3c; border-radius:6px; padding:0.35rem 0.9rem; font-size:0.85rem;">Se désabonner</a>
    <?php else: ?>
        <a href="rechercher_users.php?suivre=<?php echo $utilisateur['id']; ?>&terme=<?php echo $terme; ?>"
           style="color:#c8a415; text-decoration:none; border:1px solid #c8a415; border-radius:6px; padding:0.35rem 0.9rem; font-size:0.85rem;">Suivre</a>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php require_once "footer.php"; ?>