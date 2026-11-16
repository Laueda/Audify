<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if (isset($_GET['supprimer'])) {
    $sid = $_GET['supprimer'];
    $conn->query("DELETE FROM SONDAGE_VOTE WHERE sondage_id = $sid");
    $conn->query("DELETE FROM SONDAGE_CHOIX WHERE sondage_id = $sid");
    $conn->query("DELETE FROM SONDAGE WHERE id = $sid AND user_id = $user_id");
    header("Location: sondages.php"); exit();
}

if (isset($_GET['voter']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $sid = $_GET['voter'];

    $res = $conn->query("SELECT date_fin FROM SONDAGE WHERE id = $sid");
    $sondage = $res->fetch_assoc();

    if ($sondage['date_fin'] < date('Y-m-d')) {
        $erreur = "Ce sondage est terminé.";
    } else {
        $choix_id = $_POST['choix_id'];
        $check = $conn->query("SELECT id FROM SONDAGE_VOTE WHERE sondage_id = $sid AND user_id = $user_id");
        $existe = $check->fetch_assoc();
        if (!$existe) {
            $conn->query("INSERT INTO SONDAGE_VOTE (sondage_id, choix_id, user_id) VALUES ($sid, $choix_id, $user_id)");
        }
        header("Location: sondages.php"); exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_GET['voter'])) {
    $question = trim($_POST['question']);
    $date_fin = $_POST['date_fin'];
    $choix    = array_filter(array_map('trim', $_POST['choix'] ?? []));

    if ($question === "" || count($choix) < 2 || $date_fin === "") {
        $erreur = "Une question, une date de fin et au moins 2 choix sont obligatoires.";
    } else {
        $conn->query("INSERT INTO SONDAGE (user_id, question, date_creation, date_fin) VALUES ($user_id, '$question', NOW(), '$date_fin')");
        $sondage_id = $conn->insert_id;
        foreach ($choix as $c) {
            $conn->query("INSERT INTO SONDAGE_CHOIX (sondage_id, libelle) VALUES ($sondage_id, '$c')");
        }
        $succes = "Sondage créé !";
    }
}

$res = $conn->query("SELECT s.*, u.pseudo FROM SONDAGE s JOIN users u ON s.user_id = u.id ORDER BY s.date_creation DESC");
$sondages = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

foreach ($sondages as &$s) {
    $sid = $s['id'];
    $res_choix = $conn->query("SELECT c.id, c.libelle, COUNT(v.id) AS nb FROM SONDAGE_CHOIX c LEFT JOIN SONDAGE_VOTE v ON v.choix_id = c.id WHERE c.sondage_id = $sid GROUP BY c.id");
    $s['choix'] = $res_choix ? $res_choix->fetch_all(MYSQLI_ASSOC) : [];
    $s['total'] = array_sum(array_column($s['choix'], 'nb'));
    $vote = $conn->query("SELECT choix_id FROM SONDAGE_VOTE WHERE sondage_id = $sid AND user_id = $user_id");
    $row = $vote->fetch_assoc();
    $s['mon_vote'] = $row ? $row['choix_id'] : null;
}
unset($s);

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Sondages</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
    <form method="POST">
        <div style="margin-bottom:1rem;">
            <label style="<?php echo $label_style; ?>">Question *</label>
            <input type="text" name="question" required style="<?php echo $input_style; ?>">
        </div>
        <div style="margin-bottom:1rem;">
            <label style="<?php echo $label_style; ?>">Choix (minimum 2)</label>
            <input type="text" name="choix[]" placeholder="Choix 1" required style="<?php echo $input_style; ?> margin-bottom:0.5rem;">
            <input type="text" name="choix[]" placeholder="Choix 2" required style="<?php echo $input_style; ?> margin-bottom:0.5rem;">
            <input type="text" name="choix[]" placeholder="Choix 3 (optionnel)" style="<?php echo $input_style; ?> margin-bottom:0.5rem;">
            <input type="text" name="choix[]" placeholder="Choix 4 (optionnel)" style="<?php echo $input_style; ?>">
        </div>
        <div style="margin-bottom:1rem;">
            <label style="<?php echo $label_style; ?>">Date de fin *</label>
            <input type="date" name="date_fin" required style="<?php echo $input_style; ?> color-scheme:dark;">
        </div>
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Publier
        </button>
    </form>
</div>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($sondages)): ?>
    <p style="color:#666;">Aucun sondage.</p>
<?php else: ?>
    <?php foreach ($sondages as $s): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1.25rem; margin-bottom:1rem;">
        <strong style="color:#f2ead8;"><?php echo htmlspecialchars($s['question']); ?></strong><br>
        <small style="color:#666;">Par <?php echo htmlspecialchars($s['pseudo']); ?> — <?php echo date('d/m/Y', strtotime($s['date_creation'])); ?></small>
        <small style="color:#c8a415; margin-left:0.5rem;">Fin : <?php echo date('d/m/Y', strtotime($s['date_fin'])); ?></small>

        <?php if ($s['mon_vote']): ?>
            <div style="margin-top:1rem;">
                <?php foreach ($s['choix'] as $c): ?>
                <?php $pct = $s['total'] > 0 ? round($c['nb'] / $s['total'] * 100) : 0; ?>
                <div style="margin-bottom:0.75rem;">
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#f2ead8; margin-bottom:0.25rem;">
                        <span><?php echo htmlspecialchars($c['libelle']); ?> <?php echo $c['id'] == $s['mon_vote'] ? '<span style="color:#c8a415;">✓</span>' : ''; ?></span>
                        <span style="color:#c8a415;"><?php echo $pct; ?>% (<?php echo $c['nb']; ?>)</span>
                    </div>
                    <div style="background:#333; border-radius:4px; height:8px;">
                        <div style="background:#c8a415; width:<?php echo $pct; ?>%; height:8px; border-radius:4px;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <small style="color:#666;"><?php echo $s['total']; ?> vote<?php echo $s['total'] > 1 ? 's' : ''; ?></small>
            </div>
        <?php else: ?>
            <?php if ($s['date_fin'] < date('Y-m-d')): ?>
                <p style="color:#e74c3c; font-size:0.85rem; margin-top:0.75rem;">Ce sondage est terminé.</p>
            <?php else: ?>
            <form method="POST" action="sondages.php?voter=<?php echo $s['id']; ?>" style="margin-top:1rem;">
                <?php foreach ($s['choix'] as $c): ?>
                <label style="display:flex; align-items:center; gap:0.5rem; color:#f2ead8; margin-bottom:0.5rem; cursor:pointer;">
                    <input type="radio" name="choix_id" value="<?php echo $c['id']; ?>" required style="accent-color:#c8a415;">
                    <?php echo htmlspecialchars($c['libelle']); ?>
                </label>
                <?php endforeach; ?>
                <button type="submit"
                        style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.35rem 1rem; cursor:pointer; font-size:0.9rem; margin-top:0.5rem;">
                    Voter
                </button>
            </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($s['user_id'] == $user_id): ?>
            <div style="margin-top:0.75rem; border-top:1px solid #333; padding-top:0.75rem;">
                <a href="sondages.php?supprimer=<?php echo $s['id']; ?>"
                   onclick="return confirm('Supprimer ce sondage ?')"
                   style="color:#e74c3c; text-decoration:none; font-size:0.85rem;">Supprimer</a>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>