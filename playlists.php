<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['id'];
$erreur  = "";
$succes  = "";

if (isset($_GET['supprimer'])) {
    $pid = $_GET['supprimer'];
    $conn->query("DELETE FROM PLAYLIST WHERE id = $pid AND user_id = $user_id");
    header("Location: playlists.php"); exit();
}

if (isset($_GET['noter']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $playlist_id = $_GET['noter'];
    $note = $_POST['note'];
  
    $check = $conn->query("SELECT id FROM NOTE_PLAYLIST WHERE user_id = $user_id AND playlist_id = $playlist_id");
    $existe = $check->fetch_assoc();

    if ($existe) {
        $conn->query("UPDATE NOTE_PLAYLIST SET note = $note WHERE user_id = $user_id AND playlist_id = $playlist_id");
    } else {
        $conn->query("INSERT INTO NOTE_PLAYLIST (user_id, playlist_id, note) VALUES ($user_id, $playlist_id, $note)");
    }
    header("Location: playlists.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_GET['noter'])) {
    $nom  = trim($_POST['nom']);
    $type = ($_POST['visibilite'] === 'privee') ? 'privee' : 'publique';
    if ($nom === "") {
        $erreur = "Le nom ne peut pas être vide.";
    } else {
        $conn->query("INSERT INTO PLAYLIST (user_id, nom, type, date_creation) VALUES ($user_id, '$nom', '$type', NOW())");
        $succes = "Playlist créée !";
    }
}

$res = $conn->query("SELECT * FROM PLAYLIST WHERE user_id = $user_id ORDER BY date_creation DESC");
$playlists = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];


foreach ($playlists as &$playlist) {
    $res_notes = $conn->query("SELECT note FROM NOTE_PLAYLIST WHERE playlist_id = " . $playlist['id']);
    $res_notes = $res_notes ? $res_notes->fetch_all(MYSQLI_ASSOC) : [];

    $total_notes = 0;
    $nb_notes = count($res_notes);
    foreach ($res_notes as $ligne) {
        $total_notes = $total_notes + $ligne['note'];
    }

    if ($nb_notes > 0) {
        $playlist['moyenne'] = round($total_notes / $nb_notes, 1);
    } else {
        $playlist['moyenne'] = 0;
    }
    $playlist['nb_notes'] = $nb_notes;

    $ma_note_res = $conn->query("SELECT note FROM NOTE_PLAYLIST WHERE playlist_id = " . $playlist['id'] . " AND user_id = $user_id");
    $ma_note_ligne = $ma_note_res->fetch_assoc();
    if ($ma_note_ligne) {
        $playlist['ma_note'] = $ma_note_ligne['note'];
    } else {
        $playlist['ma_note'] = 0;
    }

    
    $res_morceaux = $conn->query("SELECT * FROM PLAYLIST_MORCEAU WHERE playlist_id = " . $playlist['id']);
    $playlist['morceaux'] = $res_morceaux ? $res_morceaux->fetch_all(MYSQLI_ASSOC) : [];
}
unset($playlist);

require_once "header.php";

$input_style = "width:100%; background:#2a2a2a; color:#f2ead8; border:1px solid #444; border-radius:6px; padding:0.5rem 0.75rem; outline:none; box-sizing:border-box;";
$label_style = "display:block; color:#c8a415; margin-bottom:0.35rem;";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Mes playlists</h2>

<?php if ($erreur): ?>
    <p style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>
<?php if ($succes): ?>
    <p style="color:#2ecc71; background:#1a2a1a; border:1px solid #2ecc71; border-radius:6px; padding:0.6rem 1rem;"><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>

<div style="background:#1e1e1e; border:1px solid #333; border-radius:8px; padding:1.5rem; margin-bottom:2rem;">
    <form method="POST">
        <div style="margin-bottom:1rem;">
            <label style="<?php echo $label_style; ?>">Nom de la playlist</label>
            <input type="text" name="nom" required style="<?php echo $input_style; ?>">
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
            Créer
        </button>
    </form>
</div>

<hr style="border-color:#333; margin-bottom:1.5rem;">

<?php if (empty($playlists)): ?>
    <p style="color:#666;">Aucune playlist.</p>
<?php else: ?>
    <?php foreach ($playlists as $pl): ?>
    <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; padding:1rem; margin-bottom:0.75rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <strong style="color:#f2ead8;"><?php echo htmlspecialchars($pl['nom']); ?></strong>
                <span style="color:#c8a415; font-size:0.85rem; margin-left:0.5rem;">(<?php echo htmlspecialchars($pl['type']); ?>)</span>
                <div style="color:#666; font-size:0.85rem; margin-top:0.2rem;"><?php echo date('d/m/Y', strtotime($pl['date_creation'])); ?></div>
            </div>
            <a href="playlists.php?supprimer=<?php echo $pl['id']; ?>"
               onclick="return confirm('Supprimer ?')"
               style="color:#e74c3c; text-decoration:none; font-size:0.85rem;">Supprimer</a>
        </div>

        <div style="margin-top:0.75rem; border-top:1px solid #333; padding-top:0.75rem;">
            <span style="color:#f2ead8; font-size:0.85rem;">
                Note moyenne : <strong style="color:#c8a415;"><?php echo $pl['moyenne']; ?>/5</strong> (<?php echo $pl['nb_notes']; ?> avis)
            </span>
            <form method="POST" action="playlists.php?noter=<?php echo $pl['id']; ?>" style="margin-top:0.5rem;">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <button type="submit" name="note" value="<?php echo $i; ?>"
                            style="background:none; border:none; cursor:pointer; font-size:1.2rem; color:<?php echo $i <= $pl['ma_note'] ? '#c8a415' : '#555'; ?>;">★</button>
                <?php endfor; ?>
            </form>
        </div>

        <div style="margin-top:0.75rem; border-top:1px solid #333; padding-top:0.75rem;">
            <span style="color:#c8a415; font-size:0.85rem; font-weight:600;">Morceaux :</span>
            <?php if (empty($pl['morceaux'])): ?>
                <p style="color:#666; font-size:0.85rem; margin-top:0.3rem;">Aucun morceau pour l'instant. Va sur la page d'un artiste pour en ajouter !</p>
            <?php else: ?>
                <?php foreach ($pl['morceaux'] as $morceau): ?>
                <div style="display:flex; align-items:center; gap:0.6rem; margin-top:0.5rem;">
                    <img src="<?php echo htmlspecialchars($morceau['cover']); ?>" alt="" style="width:32px; height:32px; border-radius:4px; object-fit:cover;">
                    <div>
                        <div style="color:#f2ead8; font-size:0.85rem;"><?php echo htmlspecialchars($morceau['titre']); ?></div>
                        <div style="color:#666; font-size:0.75rem;"><?php echo htmlspecialchars($morceau['artiste']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once "footer.php"; ?>