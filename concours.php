<?php
require_once "config.php";
if (!isset($_SESSION['loggedin']) || $_SESSION['statut'] !== 'musicien') { header("Location: index.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if (isset($_GET['supprimer'])) {
    $cid = $_GET['supprimer'];
    $conn->query("DELETE FROM CONCOURS WHERE id = $cid AND organisateur_id = $user_id");
    header("Location: concours.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre      = trim($_POST['titre']);
    $instrument = trim($_POST['instrument']);
    $lieu       = trim($_POST['lieu']);
    $date       = $_POST['date'];
    if ($titre === "" || $date === "") {
        $erreur = "Le titre et la date sont obligatoires.";
    } else {
        $conn->query("INSERT INTO CONCOURS (organisateur_id, titre, instrument, lieu, date_concours) VALUES ($user_id, '$titre', '$instrument', '$lieu', '$date')");
        $succes = "Concours créé !";
    }
}

$res = $conn->query("SELECT c.*, u.pseudo FROM CONCOURS c JOIN users u ON c.organisateur_id = u.id ORDER BY c.date_concours ASC");
$concours = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$instruments = ['Piano', 'Guitare', 'Violon', 'Chant', 'Batterie', 'Flûte', 'Autre'];

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
$group_style = "margin-bottom:1rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Concours</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
    <form method="POST">
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Titre *</label>
            <input type="text" name="titre" required style="<?php echo $input_style; ?>">
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Instrument</label>
            <select name="instrument" style="<?php echo $input_style; ?>">
                <?php foreach ($instruments as $i): ?>
                    <option><?php echo $i; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Lieu</label>
            <input type="text" name="lieu" style="<?php echo $input_style; ?>">
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Date *</label>
            <input type="date" name="date" required style="<?php echo $input_style; ?> color-scheme:dark;">
        </div>
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Publier
        </button>
    </form>
</div>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($concours)): ?>
    <p style="color:#666;">Aucun concours.</p>
<?php else: ?>
    <?php foreach ($concours as $c): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <strong style="color:#f2ead8; font-size:1.05rem;"><?php echo htmlspecialchars($c['titre']); ?></strong>
                <span style="color:#c8a415; margin-left:0.5rem; font-size:0.9rem;"><?php echo htmlspecialchars($c['instrument']); ?></span>
                <?php if ($c['lieu']): ?>
                    <div style="color:#999; font-size:0.9rem; margin-top:0.25rem;">Lieu : <?php echo htmlspecialchars($c['lieu']); ?></div>
                <?php endif; ?>
                <div style="color:#999; font-size:0.9rem; margin-top:0.25rem;">Date : <?php echo date('d/m/Y', strtotime($c['date_concours'])); ?></div>
                <div style="color:#666; font-size:0.85rem; margin-top:0.25rem;">Par <?php echo htmlspecialchars($c['pseudo']); ?></div>
            </div>
            <?php if ($c['organisateur_id'] == $user_id): ?>
                <a href="concours.php?supprimer=<?php echo $c['id']; ?>"
                   onclick="return confirm('Supprimer ?')"
                   style="color:#e74c3c; text-decoration:none; font-size:0.85rem;">Supprimer</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>
