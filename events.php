<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if (isset($_GET['supprimer'])) {
    $eid = $_GET['supprimer'];
    $conn->query("DELETE FROM EVENEMENT WHERE id = $eid AND organisateur_id = $user_id");
    header("Location: events.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre            = trim($_POST['titre']);
    $type             = trim($_POST['type']);
    $lieu             = trim($_POST['lieu']);
    $date             = $_POST['date'];
    $heure            = $_POST['heure'];
    $lien_reservation = trim($_POST['lien_reservation']);
    $nb_places        = $_POST['nb_places'];

    if ($titre === "" || $date === "") {
        $erreur = "Le titre et la date sont obligatoires.";
    } else {
        $conn->query("INSERT INTO EVENEMENT (organisateur_id, titre, type, lieu, date_evenement, heure_evenement, lien_reservation, nb_places) VALUES ($user_id, '$titre', '$type', '$lieu', '$date', '$heure', '$lien_reservation', '$nb_places')");
        $succes = "Événement créé !";
    }
}

$res = $conn->query("SELECT e.*, u.pseudo FROM EVENEMENT e JOIN users u ON e.organisateur_id = u.id ORDER BY e.date_evenement ASC");
$evenements = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$types = ['Concert', 'Opera', 'Session impro', 'Debat', 'Festival'];

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
$group_style = "margin-bottom:1rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Événements</h2>

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
            <label style="<?php echo $label_style; ?>">Type</label>
            <select name="type" style="<?php echo $input_style; ?>">
                <?php foreach ($types as $t): ?>
                    <option><?php echo $t; ?></option>
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
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Heure</label>
            <input type="time" name="heure" style="<?php echo $input_style; ?> color-scheme:dark;">
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Nombre de places disponibles</label>
            <input type="number" name="nb_places" min="1" style="<?php echo $input_style; ?>">
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Lien de réservation</label>
            <input type="url" name="lien_reservation" placeholder="https://..." style="<?php echo $input_style; ?>">
        </div>
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Publier
        </button>
    </form>
</div>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($evenements)): ?>
    <p style="color:#666;">Aucun événement.</p>
<?php else: ?>
    <?php foreach ($evenements as $ev): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <strong style="color:#f2ead8; font-size:1.05rem;"><?php echo htmlspecialchars($ev['titre']); ?></strong>
                <span style="color:#c8a415; margin-left:0.5rem; font-size:0.9rem;"><?php echo htmlspecialchars($ev['type']); ?></span>
                <?php if ($ev['lieu']): ?>
                    <div style="color:#999; font-size:0.9rem; margin-top:0.25rem;">Lieu : <?php echo htmlspecialchars($ev['lieu']); ?></div>
                <?php endif; ?>
                <div style="color:#999; font-size:0.9rem; margin-top:0.25rem;">Date : <?php echo date('d/m/Y', strtotime($ev['date_evenement'])); ?></div>
                <?php if ($ev['heure_evenement']): ?>
                    <div style="color:#999; font-size:0.9rem; margin-top:0.25rem;">Heure : <?php echo $ev['heure_evenement']; ?></div>
                <?php endif; ?>
                <?php if ($ev['nb_places']): ?>
                    <div style="color:#999; font-size:0.9rem; margin-top:0.25rem;">Places disponibles : <?php echo $ev['nb_places']; ?></div>
                <?php endif; ?>
                <?php if ($ev['lien_reservation']): ?>
                    <div style="margin-top:0.5rem;">
                        <a href="<?php echo htmlspecialchars($ev['lien_reservation']); ?>" target="_blank" style="color:#c8a415; text-decoration:none;">🎟️ Réserver</a>
                    </div>
                <?php endif; ?>
                <div style="color:#666; font-size:0.85rem; margin-top:0.25rem;">Par <?php echo htmlspecialchars($ev['pseudo']); ?></div>
            </div>
            <?php if ($ev['organisateur_id'] == $user_id): ?>
                <a href="events.php?supprimer=<?php echo $ev['id']; ?>"
                   onclick="return confirm('Supprimer ?')"
                   style="color:#e74c3c; text-decoration:none; font-size:0.85rem;">Supprimer</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>