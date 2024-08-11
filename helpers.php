<?php
function isUserLoggedIn() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['utilisateur']);
}
?>
<?php
function generateFooterLinks() {
    $links = [
        ['href' => '../index.php', 'text' => 'Accueil'],
        ['href' => '../html/liste_habitat.php', 'text' => 'Habitat'],
        ['href' => '../html/liste_service.php', 'text' => 'Service'],
        ['href' => '../html/connexion.php', 'text' => 'Connexion'],
        ['href' => '../html/formulaire_contact.php', 'text' => 'Contact']
    ];

    foreach ($links as $link) {
        echo '<li><a href="' . $link['href'] . '" class="text-white">' . $link['text'] . '</a></li>';
    }
}
?>