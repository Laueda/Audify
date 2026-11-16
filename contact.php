<?php
require_once "config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$succes = "";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom     = trim($_POST['nom']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($nom === "" || $email === "" || $message === "") {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom($email, $nom);
            $mail->addAddress('audify2026@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message de contact - Audify';
            $mail->Body    = "<p><strong>Nom :</strong> $nom</p>
                              <p><strong>Email :</strong> $email</p>
                              <p><strong>Message :</strong> $message</p>";

            $mail->send();
            $succes = "Votre message a bien été envoyé !";

        } catch (Exception $e) {
            $erreur = "Erreur lors de l'envoi : " . $mail->ErrorInfo;
        }
    }
}

require_once "header.php";
?>

<h2>Nous contacter</h2>

<?php if ($succes): ?><p class="succes"><?php echo $succes; ?></p><?php endif; ?>
<?php if ($erreur): ?><p class="erreur"><?php echo $erreur; ?></p><?php endif; ?>

<form method="POST" style="max-width:400px;">
    <label>Nom</label>
    <input type="text" name="nom" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Message</label>
    <textarea name="message" rows="6" required></textarea>

    <button type="submit">Envoyer</button>
</form>

<?php require_once "footer.php"; ?>