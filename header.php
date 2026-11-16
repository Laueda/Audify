<?php require_once "config.php"; ?>
<?php
$prefs = ['theme' => 'dark', 'couleur_accent' => '#c8a415'];
if (isset($_SESSION['id'])) {
    $uid = $_SESSION['id']; 
    $res_p = $conn->query("SELECT * FROM user_preferences WHERE user_id = $uid");
    if ($res_p && $row_p = $res_p->fetch_assoc()) {
        $prefs = $row_p;
    }
}

$theme_clair   = $prefs['theme'] === 'light';
$bg_principal  = $theme_clair ? '#f5f5f0' : '#1a1a1a';
$bg_secondaire = $theme_clair ? '#e8e8e0' : '#2a2a2a';
$couleur_texte = $theme_clair ? '#1a1a1a' : '#f2ead8';
$bg_sidebar    = $theme_clair ? '#ededea' : '#111';
$accent        = htmlspecialchars($prefs['couleur_accent']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Audify</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: <?php echo $bg_principal; ?> !important; color: <?php echo $couleur_texte; ?> !important; }
        aside { background-color: <?php echo $bg_sidebar; ?> !important; }
        input:not([type=radio]):not([type=checkbox]):not([type=color]), textarea, select { background-color: <?php echo $bg_secondaire; ?> !important; color: <?php echo $couleur_texte; ?> !important; }
        div[style*="background:#2a2a2a"], div[style*="background:#1e1e1e"] { background-color: <?php echo $bg_secondaire; ?> !important; color: <?php echo $couleur_texte; ?> !important; }
        *[style*="color:#c8a415"] { color: <?php echo $accent; ?> !important; }
        *[style*="border:1px solid #c8a415"] { border-color: <?php echo $accent; ?> !important; }
        a { color: <?php echo $accent; ?> !important; }
        h1, h2, h3, h4, h5, h6 { color: <?php echo $couleur_texte; ?> !important; }
        *[style*="color:#f2ead8"] { color: <?php echo $couleur_texte; ?> !important; }
        *[style*="color:#999"] { color: <?php echo $couleur_texte; ?> !important; }
        *[style*="color:#666"] { color: <?php echo $couleur_texte; ?> !important; }
        *[style*="color:#555"] { color: <?php echo $couleur_texte; ?> !important; }
    </style>
</head>
<body style="background-color:<?php echo $bg_principal; ?>; color:<?php echo $couleur_texte; ?>; margin:0;">

<nav style="background-color:<?php echo $bg_principal; ?>; border-bottom:1px solid <?php echo $accent; ?>; padding:0.5rem 1rem; display:flex; align-items:center; gap:0.75rem;">
    <?php if (isset($_SESSION['loggedin'])): ?>
        <a href="index.php" style="text-decoration:none; margin-right:0.5rem;">
            <img src="images/A.png" alt="Audify" height="50">
        </a>
        <form class="d-flex mx-auto" role="search" action="discover.php" method="GET" style="width:40%; gap:0.5rem;">
            <input type="search" name="q" placeholder="Rechercher un artiste..." style="flex:1; background:<?php echo $bg_secondaire; ?>; color:<?php echo $couleur_texte; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.75rem; outline:none;">
            <button type="submit" style="background:transparent; color:<?php echo $accent; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.9rem; cursor:pointer;">Rechercher</button>
        </form>
        <a href="profile.php" style="color:<?php echo $accent; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.9rem; text-decoration:none;">Profil</a>
        <a href="preferences.php" style="color:<?php echo $accent; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.9rem; text-decoration:none;">Préférences</a>
        <a href="logout.php" style="color:<?php echo $accent; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.9rem; text-decoration:none;">Déconnexion</a>
    <?php else: ?>
        <span style="color:<?php echo $accent; ?>; font-size:1.3rem; font-weight:bold; letter-spacing:2px;">AUDIFY</span>
        <div style="margin-left:auto; display:flex; gap:0.5rem;">
            <a href="login.php" style="color:<?php echo $accent; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.9rem; text-decoration:none;">Se connecter</a>
            <a href="register.php" style="color:<?php echo $accent; ?>; border:1px solid <?php echo $accent; ?>; border-radius:6px; padding:0.4rem 0.9rem; text-decoration:none;">S'inscrire</a>
        </div>
    <?php endif; ?>
</nav>

<div style="display:flex; min-height:calc(100vh - 71px);">

  <?php if (isset($_SESSION['loggedin'])): ?>
  <aside style="width:220px; min-width:220px; background:<?php echo $bg_sidebar; ?>; border-right:1px solid #2a2a2a; padding:1.25rem 0.75rem;">
    <nav style="display:flex; flex-direction:column; gap:0.5rem;">
      <a href="feed.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Fil d'actualité</a>
      <a href="discover.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Découverte</a>
      <a href="playlists.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Mes playlists</a>
      <a href="events.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Événements</a>
      <a href="forums.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Forums</a>
      <a href="messages.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Messages</a>
      <a href="sondages.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Sondages</a>
      <a href="rechercher_users.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Rechercher des amis</a>
      <a href="commande_playlist.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Commander une playlist</a>
      <?php if (isset($_SESSION['statut']) && $_SESSION['statut'] === 'musicien'): ?>
        <a href="concours.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Concours</a>
      <?php endif; ?>
      <?php if (isset($_SESSION['statut']) && $_SESSION['statut'] === 'compositeur'): ?>
        <a href="partitions.php" style="display:block; color:<?php echo $accent; ?>; background:<?php echo $bg_principal; ?>; border:1px solid #2a2a2a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Partitions</a>
      <?php endif; ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="PA_BO/login.php" style="display:block; color:#e74c3c; background:<?php echo $bg_principal; ?>; border:1px solid #3a1a1a; border-radius:6px; padding:0.5rem 0.75rem; text-decoration:none;">Admin</a>
      <?php endif; ?>
    </nav>
  </aside>
  <?php endif; ?>

  <main style="flex:1; padding:2rem;">