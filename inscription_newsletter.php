<?php
require_once "config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$succes = "";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = trim($_POST['prenom']);
    $email  = trim($_POST['email']);

    if ($prenom === "" || $email === "") {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        
        $check = $conn->query("SELECT id FROM NEWSLETTER_ABONNES WHERE email = '$email'");
        $existe = $check->fetch_assoc();

        if ($existe) {
            $erreur = "Cet email est déjà inscrit à la newsletter.";
        } else {
            
            $conn->query("INSERT INTO NEWSLETTER_ABONNES (prenom, email) VALUES ('$prenom', '$email')");

            
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'audify2026@gmail.com';
                $mail->Password   = 'vsfm whca npxr fndn';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('audify2026@gmail.com', 'Audify');
                $mail->addAddress($email, $prenom);

                $mail->isHTML(true);
                $mail->Subject = 'Bienvenue dans la newsletter Audify !';
                $mail->Body    = "<h1>Bonjour $prenom !</h1>
                                  <p>Vous êtes maintenant inscrit à la newsletter Audify.</p>
                                  <p>Vous recevrez nos actualités musicales directement dans votre boîte mail.</p>
                                  <p>À bientôt !</p>
                                  <p><strong>L'équipe Audify</strong></p>";

                $mail->send();
                $succes = "Inscription réussie ! Un mail de confirmation vous a été envoyé.";

            } catch (Exception $e) {
                $succes = "Inscription réussie !";
            }
        }
    }
}

require_once "header.php";
?>

<h2>S'inscrire à la newsletter</h2>

<?php if ($succes): ?><p class="succes"><?php echo $succes; ?></p><?php endif; ?>
<?php if ($erreur): ?><p class="erreur"><?php echo $erreur; ?></p><?php endif; ?>

<form method="POST" style="max-width:400px;">
    <label>Prénom</label>
    <input type="text" name="prenom" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <button type="submit">S'inscrire</button>
</form>

<?php require_once "footer.php"; ?>