<?php
require_once "config.php";

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['captcha_puzzle'] != '1') {
        $erreur = "Veuillez compléter le puzzle.";
    } else {
        $pseudo   = trim($_POST["pseudo"]);
        $nom      = trim($_POST["nom"]);
        $prenom   = trim($_POST["prenom"]);
        $email    = trim($_POST["email"]);
        $password = $_POST["password"];
        $statut   = $_POST["statut"];

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $erreur = "Cet email est deja utilise.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (pseudo, nom, prenom, email, password, statut, role, date_inscription) VALUES (?, ?, ?, ?, ?, ?, 'user', NOW())");
            $stmt->bind_param("ssssss", $pseudo, $nom, $prenom, $email, $hash, $statut);
            if ($stmt->execute()) {
                if (isset($_POST['newsletter'])) {
                    $conn->query("INSERT INTO NEWSLETTER_ABONNES (prenom, email) VALUES ('$prenom', '$email')");
                }
                header("Location: login.php");
                exit();
            } else {
                $erreur = "Une erreur est survenue.";
            }
        }
    }
}

require_once "header.php";
?>

<h2>Créer un compte</h2>

<?php if ($erreur): ?>
    <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>

<form method="POST" style="max-width:400px;">
    <label>Pseudo</label>
    <input type="text" name="pseudo" required>

    <label>Nom</label>
    <input type="text" name="nom" required>

    <label>Prénom</label>
    <input type="text" name="prenom" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Mot de passe</label>
    <input type="password" name="password" required minlength="8">

    <label>Statut</label><br>
    <input type="radio" name="statut" value="particulier" checked> Particulier
    <input type="radio" name="statut" value="musicien"> Musicien
    <input type="radio" name="statut" value="compositeur"> Compositeur
    <br><br>

    <hr>
    <p>Remets les pièces dans le bon ordre</p>

    <div id="captcha-container" style="display:grid; grid-template-columns:repeat(3, 60px); gap:2px; width:182px; margin-bottom:1rem;"></div>

    <p id="message-captcha" style="color:#e74c3c;">Complète le puzzle pour continuer</p>

    <input type="hidden" name="captcha_puzzle" id="captcha_puzzle" value="0">

    <label>
        <input type="checkbox" name="newsletter" value="1" style="width:auto;">
        Recevoir la newsletter Audify
    </label>

    <button type="submit">S'inscrire</button>
</form>

<p>Déjà un compte ? <a href="login.php">Se connecter</a></p>

<script src="js/captcha.js"></script>

<?php require_once "footer.php"; ?>