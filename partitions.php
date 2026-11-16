<?php
require_once "config.php";
if (!isset($_SESSION['loggedin']) || $_SESSION['statut'] !== 'compositeur') { header("Location: index.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if (isset($_GET['supprimer'])) {
    $pid = $_GET['supprimer'];
    $conn->query("DELETE FROM PARTITIONS_MUSICALES WHERE id = $pid AND user_id = $user_id");
    header("Location: partitions.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre      = trim($_POST['titre']);
    $instrument = trim($_POST['instrument']);
    $visibilite = ($_POST['visibilite'] === 'privee') ? 'privee' : 'publique';
    if ($titre === "") {
        $erreur = "Le titre ne peut pas être vide.";
    } else {
        $conn->query("INSERT INTO PARTITIONS_MUSICALES (user_id, titre, instrument, visibilite, date_creation) VALUES ($user_id, '$titre', '$instrument', '$visibilite', NOW())");
        $succes = "Partition ajoutée !";
    }
}

$partitions = $conn->query("SELECT * FROM PARTITIONS_MUSICALES WHERE user_id = $user_id ORDER BY date_creation DESC");
$partitions = $partitions ? $partitions->fetch_all(MYSQLI_ASSOC) : [];

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
$group_style = "margin-bottom:1rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Mes partitions</h2>

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
            <input type="text" name="instrument" placeholder="Piano, guitare..." style="<?php echo $input_style; ?>">
        </div>
        <div style="margin-bottom:1rem; display:flex; gap:1.5rem; align-items:center;">
            <label style="color:#f2ead8; cursor:pointer;">
                <input type="radio" name="visibilite" value="publique" checked style="accent-color:#c8a415;"> Publique
            </label>
            <label style="color:#f2ead8; cursor:pointer;">
                <input type="radio" name="visibilite" value="privee" style="accent-color:#c8a415;"> Privée
            </label>
        </div>
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Ajouter
        </button>
    </form>
</div>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($partitions)): ?>
    <p style="color:#666;">Aucune partition.</p>
<?php else: ?>
    <?php foreach ($partitions as $p): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:0.75rem; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <strong style="color:#f2ead8;"><?php echo htmlspecialchars($p['titre']); ?></strong>
            <?php if ($p['instrument']): ?>
                <span style="color:#c8a415; margin-left:0.5rem; font-size:0.9rem;">— <?php echo htmlspecialchars($p['instrument']); ?></span>
            <?php endif; ?>
            <span style="color:#666; font-size:0.85rem; margin-left:0.5rem;">(<?php echo htmlspecialchars($p['visibilite']); ?>)</span>
            <div style="color:#666; font-size:0.85rem; margin-top:0.2rem;"><?php echo date('d/m/Y', strtotime($p['date_creation'])); ?></div>
        </div>
        <a href="partitions.php?supprimer=<?php echo $p['id']; ?>"
           onclick="return confirm('Supprimer ?')"
           style="color:#e74c3c; text-decoration:none; font-size:0.85rem;">Supprimer</a>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>
