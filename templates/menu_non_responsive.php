<?php
require_once(__DIR__ . '/../helpers.php');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="../index.php"><img style="width: 70px; margin-left: 2vw;" src="../image/uploads//logo.png" alt="Logo du site"></a>
  <span class="navbar-text" style="font-size: 1.25rem; margin-left: 1vw; font-weight: bold; color: black;">Menu du site</span>
  
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link custom-nav-link <?php if ($page == 'accueil') { echo 'active'; } ?>" href="../index.php">Accueil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link custom-nav-link <?php if ($page == 'habitat') { echo 'active'; } ?>" href="../html/liste_habitat.php">Habitats</a>
      </li>
      <li class="nav-item">
        <a class="nav-link custom-nav-link <?php if ($page == 'service') { echo 'active'; } ?>" href="../html/liste_service.php">Services</a>
      </li>
      <li class="nav-item">
        <a class="nav-link custom-nav-link <?php if ($page == 'contact') { echo 'active'; } ?>" href="../html/formulaire_contact.php">Contact et avis</a>
      </li>
      <?php if (!isUserLoggedIn()) { ?>
        <li class="nav-item">
          <a class="nav-link custom-nav-link <?php if ($page == 'compte') { echo 'active'; } ?>" href="../html/connexion.php">Connexion</a>
        </li>
      <?php } else { ?>
        <li class="nav-item">
          <a class="nav-link custom-nav-link <?php if ($page == 'compte') { echo 'active'; } ?>" href="../html/monCompte.php">Mon compte</a>
        </li>
        <li class="nav-item">
          <a class="nav-link custom-nav-link" href="../Php/deconnexion.php">Déconnexion</a>
        </li>
      <?php } ?>
    </ul>
  </div>
</nav>