<?php $titre = 'Comment ça marche ?'; ?>
<div class="conteneur section">
  <h1>Comment ça marche ?</h1>
  <p class="muted">Trois étapes simples, que vous soyez propriétaire ou gardien.</p>

  <div class="grille-2">
    <div class="carte">
      <h2>🏠 Vous êtes propriétaire</h2>
      <ol class="etapes">
        <li><strong>Publiez une annonce</strong> avec les dates et les besoins de votre animal.</li>
        <li><strong>Recevez des candidatures</strong> de gardiens près de chez vous et consultez leurs avis.</li>
        <li><strong>Choisissez votre gardien</strong>, puis laissez-lui un avis après la garde.</li>
      </ol>
      <a href="<?= url('inscription') ?>" class="btn">Publier une annonce</a>
    </div>
    <div class="carte">
      <h2>🐾 Vous êtes gardien</h2>
      <ol class="etapes">
        <li><strong>Complétez votre profil</strong> : présentation, ville, expérience.</li>
        <li><strong>Postulez aux annonces</strong> avec un message de motivation.</li>
        <li><strong>Gardez, puis recevez des avis</strong> qui renforcent votre réputation.</li>
      </ol>
      <a href="<?= url('annonces') ?>" class="btn">Voir les annonces</a>
    </div>
  </div>

  <div class="carte bandeau">
    <h3>La confiance avant tout</h3>
    <p class="muted">Chaque avis est vérifié par notre équipe avant publication.</p>
  </div>
</div>
