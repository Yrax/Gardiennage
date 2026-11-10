<?php
$titre = 'Espace gardien';
$u = utilisateur();
$lien = fn($t) => url('espace_gardien', ['tab' => $t]);
$sidebar = ['titre' => 'Gardien', 'liens' => [
  ["Vue d'ensemble",   $lien('accueil'),      $tab === 'accueil'],
  ['Trouver une garde', url('annonces'),      false],
  ['Mes candidatures', $lien('candidatures'), $tab === 'candidatures'],
  ['Mes gardes',       $lien('gardes'),       $tab === 'gardes'],
  ['Mon profil',       $lien('profil'),       $tab === 'profil'],
  ['Mes avis',         $lien('avis'),         $tab === 'avis'],
]];

/** Ligne de suivi d'une candidature. */
$ligneCandidature = function (array $c) {
  $termine = $c['statut'] === 'acceptee' && $c['statut_annonce'] === 'terminee';
  $info = match (true) {
    $termine                     => 'Laissez un avis au propriétaire',
    $c['statut'] === 'acceptee'  => 'Préparez la rencontre',
    $c['statut'] === 'refusee'   => 'Ce sera pour une prochaine fois',
    default                      => 'Envoyée le ' . date_fr($c['date_envoi']),
  };
  ?>
  <div class="carte ligne">
    <div class="ligne-personne">
      <span class="icone-carre">🐾</span>
      <div>
        <a href="<?= url('annonce', ['id' => $c['id_annonce_candidature']]) ?>" class="fort"><?= e($c['nom_annonce']) ?></a>
        <p class="petit muted"><?= e(ville_courte($c['ville'])) ?> · <?= e(periode_courte($c['date_debut'], $c['date_fin'])) ?></p>
      </div>
    </div>
    <span class="petit muted"><?= e($info) ?></span>
    <?= badge($termine ? 'terminee' : $c['statut']) ?>
    <a href="<?= $termine ? url('noter', ['id' => $c['id_annonce_candidature']]) : url('annonce', ['id' => $c['id_annonce_candidature']]) ?>" class="lien-fort">Voir →</a>
  </div>
<?php };
?>

<?php if ($tab === 'accueil'): ?>
  <div class="titre-ligne">
    <div>
      <h1>Bonjour, <?= e($u['prenom']) ?></h1>
      <p class="muted">Prêt à trouver votre prochaine garde ?</p>
    </div>
    <a href="<?= url('annonces') ?>" class="btn">Trouver une garde</a>
  </div>

  <div class="stats">
    <div class="carte stat"><p>Candidatures en attente</p><strong><?= $stats['attente'] ?></strong><small>En attente d'une réponse</small></div>
    <div class="carte stat"><p>Candidatures acceptées</p><strong><?= $stats['acceptees'] ?></strong><small>À préparer</small></div>
    <div class="carte stat"><p>Gardes terminées</p><strong><?= $stats['terminees'] ?></strong><small>Votre expérience</small></div>
    <div class="carte stat"><p>Votre note</p><strong><?= $gardien['nombre_avis_gardien'] ? note_fr($gardien['note_moyenne']) . ' / 5' : '—' ?></strong><small>Sur <?= (int) $gardien['nombre_avis_gardien'] ?> avis</small></div>
  </div>

  <h2>Suivi de mes candidatures</h2>
  <?php if (!$candidatures): ?>
    <div class="vide">Vous n'avez pas encore postulé. <a href="<?= url('annonces') ?>" class="lien-fort">Voir les annonces</a></div>
  <?php endif; ?>
  <?php foreach ($candidatures as $c) $ligneCandidature($c); ?>

  <?php if (!$gardien['description_gardien']): ?>
    <h2 class="vert">Un profil complet inspire confiance</h2>
    <p class="muted">Ajoutez une présentation claire pour aider les propriétaires à vous choisir. <a href="<?= $lien('profil') ?>" class="lien-fort">Compléter mon profil</a></p>
  <?php else: ?>
    <h2 class="vert">Un profil complet inspire confiance</h2>
    <p class="muted">Gardez votre présentation et votre ville à jour pour aider les propriétaires à vous choisir.</p>
  <?php endif; ?>

<?php elseif ($tab === 'candidatures'): ?>
  <h1>Mes candidatures</h1>
  <?php if (!$candidatures): ?><div class="vide">Aucune candidature envoyée.</div><?php endif; ?>
  <?php foreach ($candidatures as $c) $ligneCandidature($c); ?>

<?php elseif ($tab === 'gardes'): ?>
  <h1>Mes gardes</h1>
  <?php if (!$gardes): ?><div class="vide">Aucune garde attribuée pour le moment.</div><?php endif; ?>
  <?php foreach ($gardes as $a): ?>
    <div class="carte ligne">
      <div class="ligne-personne">
        <span class="icone-carre"><?= emoji_espece($a['espece']) ?></span>
        <div>
          <a href="<?= url('annonce', ['id' => $a['id_annonce']]) ?>" class="fort"><?= e($a['nom_annonce']) ?></a>
          <p class="petit muted"><?= e($a['ville']) ?> · <?= e(periode_courte($a['date_debut'], $a['date_fin'])) ?></p>
        </div>
      </div>
      <span class="petit muted">Chez <?= e($a['prenom_util_prop']) ?> · <?= e($a['type_garde']) ?></span>
      <?= badge($a['statut']) ?>
    </div>
  <?php endforeach; ?>

<?php elseif ($tab === 'profil'): ?>
  <h1>Mon profil</h1>
  <p class="muted">Ces informations sont visibles par les propriétaires.</p>
  <form method="post" action="<?= url('profil') ?>" class="carte formulaire etroit-gauche">
    <?= csrf_champ() ?>
    <div class="deux-colonnes">
      <label class="champ"><span>Prénom</span><input type="text" value="<?= e($profil['prenom_utilisateur']) ?>" disabled></label>
      <label class="champ"><span>Nom</span><input type="text" value="<?= e($profil['nom_utilisateur']) ?>" disabled></label>
    </div>
    <label class="champ"><span>E-mail</span><input type="email" value="<?= e($profil['mail']) ?>" disabled></label>
    <div class="deux-colonnes">
      <label class="champ"><span>Ville</span><input type="text" name="adresse" maxlength="100" required value="<?= e($profil['adresse']) ?>"></label>
      <label class="champ"><span>Téléphone</span><input type="tel" name="tel" maxlength="20" value="<?= e($profil['tel']) ?>"></label>
    </div>
    <label class="champ"><span>Présentation</span>
      <textarea name="description" rows="3" maxlength="255"><?= e($profil['description']) ?></textarea>
    </label>
    <button class="btn" type="submit">Enregistrer</button>
  </form>

<?php elseif ($tab === 'avis'): ?>
  <h1>Mes avis</h1>
  <?php if ($aNoter): ?>
    <h2>À noter</h2>
    <?php foreach ($aNoter as $a): ?>
      <div class="carte ligne">
        <div>
          <p class="fort"><?= e($a['nom_annonce']) ?></p>
          <p class="petit muted">Chez <?= e($a['prenom_util_prop']) ?> · <?= e(periode_courte($a['date_debut'], $a['date_fin'])) ?></p>
        </div>
        <a href="<?= url('noter', ['id' => $a['id_annonce']]) ?>" class="btn btn-sm">Laisser un avis</a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <h2>Avis reçus</h2>
  <?php if (!$avis): ?><div class="vide">Pas encore d'avis publié.</div><?php endif; ?>
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
<?php endif; ?>
