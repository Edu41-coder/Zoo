<?php
require_once(__DIR__ . '/../helpers.php');
?>
<footer class="text-center text-lg-start text-white" id="footerGlobal">
  <div class="container p-4 pb-0">
    <section>
      <div class="row">
        <div class="col-lg-6 col-md-6 mb-4 mb-md-0" id="footerDiv">
          <h5 class="text-uppercase">Merci pour votre visite</h5>
          <p>
            « Une saison au zoo » témoigne aujourd’hui de notre souci permanent du bien-être de nos animaux et de notre implication sans faille dans la préservation de la biodiversité, menacée de toutes parts. A travers les projets qu’il porte, notre zoo fait maintenant un pari pour l’avenir : celui que notre créativité, nos valeurs de l'écologie et d’engagement total aux côtés des acteurs de la conservation, touchent le jeune public, cette génération future qui demain aura la lourde responsabilité de soigner, préserver, notre si belle planète.
          </p>
        </div>
        <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
          <h5 class="text-uppercase">Navigation du site</h5>
          <ul class="list-unstyled mb-0">
            <?php generateFooterLinks(); ?>
          </ul>
        </div>
      </div>
    </section>
  </div>
  <div class="text-center p-3" id="footerBas">
    ©2024 Copyright: Eduardo Hermosilla
  </div>
</footer>
<script src="https://kit.fontawesome.com/89c7ec92f3.js" crossorigin="anonymous"></script>