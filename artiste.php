<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$artiste_id = $_GET['id'] ?? '';

if ($artiste_id === '') {
    header("Location: discover.php");
    exit();
}

if (isset($_GET['noter']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $note = $_POST['note'];
    $check = $conn->query("SELECT id FROM NOTE_ARTISTE WHERE user_id = " . $_SESSION['id'] . " AND artiste_id = $artiste_id");
    $existe = $check->fetch_assoc();
    if ($existe) {
        $conn->query("UPDATE NOTE_ARTISTE SET note = $note WHERE user_id = " . $_SESSION['id'] . " AND artiste_id = $artiste_id");
    } else {
        $conn->query("INSERT INTO NOTE_ARTISTE (user_id, artiste_id, note) VALUES (" . $_SESSION['id'] . ", $artiste_id, $note)");
    }
}
if (isset($_GET['ajouter_playlist']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $playlist_id = $_POST['playlist_id'];
    $deezer_track_id = $_POST['deezer_track_id'];
    $titre = $_POST['titre'];
    $artiste_nom = $_POST['artiste_nom'];
    $cover = $_POST['cover'];

    $conn->query("INSERT INTO PLAYLIST_MORCEAU (playlist_id, deezer_track_id, titre, artiste, cover) VALUES ($playlist_id, $deezer_track_id, '$titre', '$artiste_nom', '$cover')");
}

$mes_playlists = $conn->query("SELECT id, nom FROM PLAYLIST WHERE user_id = " . $_SESSION['id']);
$mes_playlists = $mes_playlists ? $mes_playlists->fetch_all(MYSQLI_ASSOC) : [];

function deezer($endpoint) {
    $url = 'https://api.deezer.com/' . $endpoint;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function formaterFans($nb) {
    if ($nb >= 1000000) return round($nb / 1000000, 1) . 'M fans';
    if ($nb >= 1000) return floor($nb / 1000) . 'K fans';
    return $nb . ' fans';
}

function formaterDuree($secondes) {
    $m = floor($secondes / 60);
    $s = $secondes % 60;
    return $m . ':' . str_pad($s, 2, '0', STR_PAD_LEFT);
}

$artiste    = deezer("artist/$artiste_id");
$top        = deezer("artist/$artiste_id/top?limit=10");
$albums     = deezer("artist/$artiste_id/albums?limit=20");
$similaires = deezer("artist/$artiste_id/related?limit=12");

$top_tracks       = $top['data'] ?? [];
$liste_albums     = $albums['data'] ?? [];
$liste_similaires = $similaires['data'] ?? [];

$connus     = array_filter($liste_similaires, fn($a) => ($a['nb_fan'] ?? 0) >= 500000);
$a_decouvrir = array_filter($liste_similaires, fn($a) => ($a['nb_fan'] ?? 0) < 500000);


$res_notes = $conn->query("SELECT note FROM NOTE_ARTISTE WHERE artiste_id = $artiste_id");
$res_notes = $res_notes ? $res_notes->fetch_all(MYSQLI_ASSOC) : [];

$total_notes = 0;
$nb_notes = count($res_notes);
foreach ($res_notes as $ligne) {
    $total_notes = $total_notes + $ligne['note'];
}

if ($nb_notes > 0) {
    $moyenne = round($total_notes / $nb_notes, 1);
} else {
    $moyenne = 0;
}


$ma_note_res = $conn->query("SELECT note FROM NOTE_ARTISTE WHERE artiste_id = $artiste_id AND user_id = " . $_SESSION['id']);
$ma_note_ligne = $ma_note_res->fetch_assoc();
if ($ma_note_ligne) {
    $ma_note = $ma_note_ligne['note'];
} else {
    $ma_note = 0;
}

require_once "header.php";
?>

<div style="display:flex; align-items:center; gap:2rem; margin-bottom:2.5rem; flex-wrap:wrap;">
    <img src="<?php echo $artiste['picture_xl'] ?? $artiste['picture_medium'] ?? ''; ?>"
         alt="<?php echo htmlspecialchars($artiste['name'] ?? ''); ?>"
         style="width:160px; height:160px; border-radius:50%; object-fit:cover; border:3px solid #c8a415; flex-shrink:0;">
    <div>
        <h1 style="color:#f2ead8; font-size:2rem; margin:0 0 0.5rem;"><?php echo htmlspecialchars($artiste['name'] ?? ''); ?></h1>
        <p style="color:#c8a415; margin:0 0 1rem;"><?php echo formaterFans($artiste['nb_fan'] ?? 0); ?></p>

        <div style="margin-bottom:1rem;">
            <span style="color:#f2ead8; font-size:0.95rem;">
                Note moyenne : <strong style="color:#c8a415;"><?php echo $moyenne; ?>/5</strong> (<?php echo $nb_notes; ?> avis)
            </span>
        </div>

        <form method="POST" action="artiste.php?id=<?php echo $artiste_id; ?>&noter=1" style="margin-bottom:1rem;">
            <span style="color:#f2ead8; font-size:0.9rem; margin-right:0.5rem;">Ta note :</span>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <button type="submit" name="note" value="<?php echo $i; ?>"
                        style="background:none; border:none; cursor:pointer; font-size:1.3rem; color:<?php echo $i <= $ma_note ? '#c8a415' : '#555'; ?>;">★</button>
            <?php endfor; ?>
        </form>

        <a href="discover.php" style="color:#999; text-decoration:none; font-size:0.9rem; border:1px solid #333; border-radius:6px; padding:0.35rem 0.9rem;">Retour</a>
    </div>
</div>

<h3 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">Top titres</h3>
<div style="margin-bottom:2.5rem;">
    <?php if (empty($top_tracks)): ?>
        <p style="color:#666;">Aucun titre disponible.</p>
    <?php else: ?>
        <?php foreach ($top_tracks as $i => $track): ?>
        <div style="display:flex; align-items:center; gap:1rem; padding:0.6rem 0.75rem; border-radius:6px; background:#2a2a2a; margin-bottom:0.5rem;">
            <span style="color:#555; font-size:0.85rem; min-width:1.2rem;"><?php echo $i + 1; ?></span>
            <img src="<?php echo $track['album']['cover_small'] ?? ''; ?>" alt="" style="width:40px; height:40px; border-radius:4px; object-fit:cover;">
            <div style="flex:1;">
                <div style="color:#f2ead8; font-size:0.95rem;"><?php echo htmlspecialchars($track['title']); ?></div>
                <div style="color:#666; font-size:0.8rem;"><?php echo htmlspecialchars($track['album']['title'] ?? ''); ?></div>
            </div>
            <span style="color:#666; font-size:0.85rem;"><?php echo formaterDuree($track['duration']); ?></span>

            <?php if (!empty($mes_playlists)): ?>
            <form method="POST" action="artiste.php?id=<?php echo $artiste_id; ?>&ajouter_playlist=1" style="margin:0; display:flex; gap:0.3rem;">
                <input type="hidden" name="deezer_track_id" value="<?php echo $track['id']; ?>">
                <input type="hidden" name="titre" value="<?php echo htmlspecialchars($track['title']); ?>">
                <input type="hidden" name="artiste_nom" value="<?php echo htmlspecialchars($artiste['name'] ?? ''); ?>">
                <input type="hidden" name="cover" value="<?php echo $track['album']['cover_small'] ?? ''; ?>">
                <select name="playlist_id"
                        style="background:#1a1a1a; color:#c8a415; border:1px solid #333; border-radius:4px; padding:0.3rem; font-size:0.8rem;">
                    <?php foreach ($mes_playlists as $pl): ?>
                        <option value="<?php echo $pl['id']; ?>"><?php echo htmlspecialchars($pl['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"
                        style="background:#1a1a1a; color:#c8a415; border:1px solid #c8a415; border-radius:4px; padding:0.3rem 0.6rem; font-size:0.8rem; cursor:pointer;">
                    Ajouter
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h3 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">Discographie</h3>
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:1rem; margin-bottom:2.5rem;">
    <?php if (empty($liste_albums)): ?>
        <p style="color:#666;">Aucun album disponible.</p>
    <?php else: ?>
        <?php foreach ($liste_albums as $album): ?>
        <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; overflow:hidden;">
            <img src="<?php echo $album['cover_medium'] ?? ''; ?>" alt="<?php echo htmlspecialchars($album['title']); ?>" style="width:100%; aspect-ratio:1; object-fit:cover;">
            <div style="padding:0.6rem 0.75rem;">
                <div style="color:#f2ead8; font-size:0.9rem;"><?php echo htmlspecialchars($album['title']); ?></div>
                <div style="color:#666; font-size:0.8rem;"><?php echo isset($album['release_date']) ? substr($album['release_date'], 0, 4) : ''; ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($connus)): ?>
<h3 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">Artistes similaires</h3>
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:1rem; margin-bottom:2.5rem;">
    <?php foreach ($connus as $a): ?>
    <a href="artiste.php?id=<?php echo $a['id']; ?>" style="text-decoration:none;">
        <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; overflow:hidden;">
            <img src="<?php echo $a['picture_medium'] ?? ''; ?>" alt="<?php echo htmlspecialchars($a['name']); ?>" style="width:100%; aspect-ratio:1; object-fit:cover;">
            <div style="padding:0.6rem 0.75rem;">
                <div style="color:#f2ead8; font-size:0.9rem;"><?php echo htmlspecialchars($a['name']); ?></div>
                <div style="color:#c8a415; font-size:0.78rem;"><?php echo formaterFans($a['nb_fan'] ?? 0); ?></div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($a_decouvrir)): ?>
<h3 style="color:#c8a415; border-bottom:1px solid #333; padding-bottom:0.5rem; margin-bottom:1rem;">À découvrir</h3>
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:1rem; margin-bottom:2rem;">
    <?php foreach ($a_decouvrir as $a): ?>
    <a href="artiste.php?id=<?php echo $a['id']; ?>" style="text-decoration:none;">
        <div style="background:#2a2a2a; border:1px solid #333; border-radius:8px; overflow:hidden;">
            <img src="<?php echo $a['picture_medium'] ?? ''; ?>" alt="<?php echo htmlspecialchars($a['name']); ?>" style="width:100%; aspect-ratio:1; object-fit:cover;">
            <div style="padding:0.6rem 0.75rem;">
                <div style="color:#f2ead8; font-size:0.9rem;"><?php echo htmlspecialchars($a['name']); ?></div>
                <div style="color:#c8a415; font-size:0.78rem;"><?php echo formaterFans($a['nb_fan'] ?? 0); ?></div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once "footer.php"; ?>