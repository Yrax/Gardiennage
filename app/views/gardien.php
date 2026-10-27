<?php $titre = $gardien['prenom_gardien']; ?>
<div class="conteneur section etroit">
  <p class="fil"><a href="<?= url('accueil') ?>">Accueil</a> / Gardiens / <?= e($gardien['prenom_gardien']) ?></p>

  <div class="carte profil-tete">
    <span class="avatar avatar-lg"><?= e(initiales($gardien['prenom_gardien'], $gardien['nom_gardien'])) ?></span>
    <div>
      <h1><?= e($gardien['prenom_gardien']) ?> <?= e(mb_substr($gardien['nom_gardien'], 0, 1)) ?>.</h1>
      <p class="muted"><?= e($gardien['adresse_gardien']) ?></p>
      <p class="note">
        <?= $gardien['nombre_avis_gardien'] > 0
          ? '★ ' . note_fr($gardien['note_moyenne']) . ' · ' . (int) $gardien['nombre_avis_gardien'] . ' avis'
          : '☆ Nouveau gardien' ?>
      </p>
    </div>
  </div>

  <h2>Présentation</h2>
  <p class="muted texte"><?= e($gardien['description_gardien'] ?: 'Ce gardien n\'a pas encore rédigé sa présentation.') ?></p>

  <h2>Avis des propriétaires</h2>
  <?php if (!$avis): ?>
    <div class="vide">Pas encore d'avis publié.</div>
  <?php endif; ?>
  <?php foreach ($avis as $a): ?>
    <div class="carte ligne">
      <div>
        <p class="fort"><?= e($a['prenom_util_prop']) ?></p>
        <p class="petit muted"><?= e($a['nom_annonce']) ?> · <?= e(date_fr($a['date_poste'])) ?></p>
      </div>
      <p class="citation">« <?= e($a['commentaire']) ?> »</p>
      <span class="note"><?= etoiles((int) $a['nombre_etoiles']) ?></span>
    </div>
  <?php endforeach; ?>
</div>
