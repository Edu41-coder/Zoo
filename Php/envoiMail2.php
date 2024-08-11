<?php
$mail = $_POST['mail'];
$nom = $_POST['nom'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$destinataire = 'Eduardo Hermosilla <hehermosilla@gmail.com>';

$headers = "From: $nom <$mail>" . "\r\n";
$headers .= "Reply-To: $mail" . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// Ajout de logs pour déboguer
error_log("Destinataire: $destinataire");
error_log("Sujet: $subject");
error_log("Message: $message");
error_log("En-têtes: $headers");

if (mail($destinataire, $subject, $message, $headers)) {
    header('Location: ../html/formulaire_contact.php?message=mailOk');
    exit();
} else {
    error_log("Erreur lors de l'envoi de l'email");
    header('Location: ../html/formulaire_contact.php?message=mailKo');
    exit();
}
?>