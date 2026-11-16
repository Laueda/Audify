<?php
require_once "config.php";
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }

$genres = ['Pop', 'Rock', 'Jazz', 'Rap', 'Classique', 'Electro', 'R&B', 'Metal', 'Folk', 'Soul'];

require_once "header.php";
?>

<h2 style="color:#f2ead8; margin-bottom:1.5rem;">Découverte musicale</h2>

<div style="display:flex; gap:0.5rem; margin-bottom:1rem;">
    <input type="text" id="champ-recherche" placeholder="Rechercher un artiste..."
           style="flex:1; background:#2a2a2a; color:#f2ead8; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 0.75rem; outline:none;">
    <button id="btn-recherche"
            style="background:#2a2a2a; color:#c8a415; border:1px solid #c8a415; border-radius:6px; padding:0.5rem 1.1rem; cursor:pointer;">
        Rechercher
    </button>
</div>

<div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:1rem;">
    <?php foreach ($genres as $g): ?>
        <button class="btn-genre" data-genre="<?php echo htmlspecialchars($g); ?>"
                style="background:#2a2a2a; color:#c8a415; border:1px solid #333; border-radius:4px; padding:0.3rem 0.75rem; cursor:pointer; font-size:0.85rem;">
            <?php echo htmlspecialchars($g); ?>
        </button>
    <?php endforeach; ?>
</div>

<p id="zone-statut" style="color:#666; font-size:0.9rem; margin-bottom:0.5rem;"></p>
<p id="zone-erreur" style="color:#e74c3c; background:#2a1a1a; border:1px solid #e74c3c; border-radius:6px; padding:0.6rem 1rem; display:none;"></p>

<div id="liste-artistes" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:1rem;"></div>

<style>
  .carte-artiste {
    background: #2a2a2a;
    border: 1px solid #333;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color .2s;
    display: flex;
    flex-direction: column;
  }
  .carte-artiste:hover { border-color: #c8a415; }
  .visuel-artiste img { width:100%; aspect-ratio:1; object-fit:cover; }
  .meta-artiste { padding:.6rem .8rem; }
  .meta-artiste h2 { font-size:1rem; margin:0 0 .2rem; color:#f2ead8; }
  .fans-artiste { font-size:.8rem; color:#c8a415; margin:0; }
</style>

<script src="js/app.js"></script>

<?php require_once "footer.php"; ?>
