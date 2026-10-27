<?php $titre = 'Accueil'; ?>
<section class="hero">
  <div class="conteneur hero-grille">
    <div>
      <span class="etiquette">Gardiennage de confiance</span>
      <h1>Partez l'esprit tranquille.<br>Votre compagnon est entre de bonnes pattes.</h1>
      <p class="muted">Un gardien près de chez vous, attentionné et disponible pour votre animal.</p>

      <form class="recherche" method="get" action="index.php">
        <input type="hidden" name="action" value="accueil">
        <label>
          <span>Où ?</span>
          <input type="text" name="ville" value="<?= e($ville) ?>" placeholder="Ville ou code postal">
        </label>
        <label>
          <span>Pour qui ?</span>
          <input type="text" name="espece" value="<?= e($espece) ?>" placeholder="Chien, chat, autre...">
        </label>
        <button class="btn" type="submit">Trouver un gardien</button>
      </form>
    </div>
    <div class="carte-illustration hero-carte">
      <span class="pattes">🐾</span>
      <p>Les bonnes rencontres<br>commencent ici.</p>
    </div>
  </div>
</section>

<section class="conteneur section" id="gardiens">
  <h2><?= $recherche ? 'Résultats de votre recherche' : 'Des gardiens près de chez vous' ?></h2>
  <p class="muted">Des profils avec avis et informations claires pour choisir en confiance.</p>

  <?php if (!$gardiens): ?>
    <div class="vide">Aucun gardien ne correspond à votre recherche. <a href="<?= url('accueil') ?>">Voir tous les gardiens</a></div>
  <?php endif; ?>

  <div class="grille-3">
    <?php foreach ($gardiens as $g): ?>
      <article class="carte carte-gardien">
        <div class="carte-gardien-tete">
          <span class="avatar"><?= e(initiales($g['prenom_gardien'])) ?></span>
          <div>
            <h3><?= e($g['prenom_gardien']) ?></h3>
            <p class="petit muted"><?= e(ville_courte($g['adresse_gardien'])) ?><?= $g['gardes'] ? ' · ' . (int) $g['gardes'] . ' garde(s)' : '' ?></p>
          </div>
        </div>
        <p class="note">
          <?php if ($g['nombre_avis_gardien'] > 0): ?>
            ★ <?= note_fr($g['note_moyenne']) ?> · <?= (int) $g['nombre_avis_gardien'] ?> avis
          <?php else: ?>
            ☆ Nouveau gardien
          <?php endif; ?>
        </p>
        <p class="petit"><?= e($g['description_gardien']) ?></p>
        <span class="badge badge-vert">Profil disponible</span>
        <a href="<?= url('gardien', ['id' => $g['id_gardien']]) ?>" class="btn btn-contour btn-bloc">Voir le profil</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($annonces): ?>
<section class="conteneur section">
  <div class="titre-ligne">
    <h2>Les dernières annonces</h2>
    <a href="<?= url('annonces') ?>" class="lien-fort">Voir toutes →</a>
  </div>
  <div class="grille-3">
    <?php foreach ($annonces as $a): ?>
      <?php require __DIR__ . '/annonces/_carte.php'; ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
