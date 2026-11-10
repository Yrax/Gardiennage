<?php /* Carte d'annonce réutilisable. Attend $a. */ ?>
<a class="carte carte-annonce" href="<?= url('annonce', ['id' => $a['id_annonce']]) ?>">
  <div class="carte-annonce-image">
    <span><?= emoji_espece($a['espece']) ?></span>
    <span class="etiquette"><?= e($a['type_garde']) ?></span>
  </div>
  <div class="carte-annonce-corps">
    <h3><?= e($a['nom_annonce']) ?></h3>
    <p class="petit muted"><?= e($a['ville']) ?> · <?= e(periode_courte($a['date_debut'], $a['date_fin'])) ?></p>
    <p class="petit muted"><?= (int) $a['nb_candidatures'] ?> candidature(s)</p>
  </div>
</a>
