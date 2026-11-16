<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dest_id = $_POST['destinataire_id'];
    $contenu = trim($_POST['contenu']);
    if ($dest_id === 0 || $contenu === "") {
        $erreur = "Choisis un destinataire et écris un message.";
    } elseif ($dest_id === $user_id) {
        $erreur = "Tu ne peux pas t'envoyer un message à toi-même.";
    } else {
        $conn->query("INSERT INTO MESSAGE (expediteur_id, destinataire_id, contenu, date_envoi) VALUES ($user_id, $dest_id, '$contenu', NOW())");
        $succes = "Message envoyé !";
    }
}

$res = $conn->query("SELECT m.*, u.pseudo AS autre_pseudo, u.id AS autre_id FROM MESSAGE m JOIN users u ON u.id = IF(m.expediteur_id = $user_id, m.destinataire_id, m.expediteur_id) WHERE m.expediteur_id = $user_id OR m.destinataire_id = $user_id ORDER BY m.date_envoi DESC");
$tous = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

$conversations = []; $vus = [];
foreach ($tous as $m) {
    if (!in_array($m['autre_id'], $vus)) {
        $vus[] = $m['autre_id'];
        $conversations[] = $m;
    }
}


$mes_abonnements = $conn->query("SELECT abonne_a_id FROM ABONNEMENT WHERE abonne_id = $user_id");
$mes_abonnements = $mes_abonnements ? array_column($mes_abonnements->fetch_all(MYSQLI_ASSOC), 'abonne_a_id') : [];


$mes_abonnes = $conn->query("SELECT abonne_id FROM ABONNEMENT WHERE abonne_a_id = $user_id");
$mes_abonnes = $mes_abonnes ? array_column($mes_abonnes->fetch_all(MYSQLI_ASSOC), 'abonne_id') : [];


$mes_amis_id = array_intersect($mes_abonnements, $mes_abonnes);

$users = [];
if (!empty($mes_amis_id)) {
    $liste_id = implode(',', $mes_amis_id);
    $res_users = $conn->query("SELECT id, pseudo FROM users WHERE id IN ($liste_id) ORDER BY pseudo ASC");
    $users = $res_users ? $res_users->fetch_all(MYSQLI_ASSOC) : [];
}

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
$group_style = "margin-bottom:1rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Messages</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<?php if (empty($users)): ?>
    <p style="color:#666; margin-bottom:1.5rem;">Tu n'as pas encore d'amis. Va dans "Rechercher des amis" pour t'abonner à d'autres utilisateurs !</p>
<?php else: ?>
<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
    <form method="POST">
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Destinataire</label>
            <select name="destinataire_id" required style="<?php echo $input_style; ?>">
                <option value="">-- Choisir --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['pseudo']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="<?php echo $group_style; ?>">
            <label style="<?php echo $label_style; ?>">Message</label>
            <textarea name="contenu" rows="4" required style="<?php echo $input_style; ?> resize:vertical;"></textarea>
        </div>
        <button type="submit"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.25rem; cursor:pointer;">
            Envoyer
        </button>
    </form>
</div>
<?php endif; ?>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($conversations)): ?>
    <p style="color:#666;">Aucun message.</p>
<?php else: ?>
    <?php foreach ($conversations as $conv): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:0.75rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem;">
            <strong style="color:#c8a415;"><?php echo htmlspecialchars($conv['autre_pseudo']); ?></strong>
            <small style="color:#666;"><?php echo date('d/m H:i', strtotime($conv['date_envoi'])); ?></small>
        </div>
        <p style="color:#f2ead8; margin:0 0 0.25rem;"><?php echo htmlspecialchars($conv['contenu']); ?></p>
        <?php if ($conv['expediteur_id'] == $user_id): ?>
            <small style="color:#555;">Envoyé par vous</small>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>