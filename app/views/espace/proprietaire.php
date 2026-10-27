<?php
$titre = 'Espace propriétaire';
$u = utilisateur();
$lien = fn($t) => url('espace_proprietaire', ['tab' => $t]);
$sidebar = ['titre' => 'Propriétaire', 'liens' => [
  ["Vue d'ensemble",       $lien('accueil'),      $tab === 'accueil'],
  ['Mes annonces',         $lien('annonces'),     $tab === 'annonces'],
  ['Candidatures reçues',  $lien('candidatures'), $tab === 'candidatures'],
  ['Historique',           $lien('historique'),   $tab === 'historique'],
  ['Avis',                 $lien('avis'),         $tab === 'avis'],
]];

/** Ligne d'annonce (utilisée dans plusieurs onglets). */
$ligneAnnonce = function (array $a) { ?>
  <div class="carte ligne">
    <div>
      <a href="<?= url('annonce', ['id' => $a['id_annonce']]) ?>" class="fort"><?= e($a['nom_annonce']) ?></a>
      <p class="petit muted"><?= e(periode_courte($a['date_debut'], $a['date_fin'])) ?> · <?= e(ville_courte($a['ville'])) ?></p>
    </div>
    <?= badge($a['statut']) ?>
    <span class="petit muted"><?= (int) $a['nb_candidatures'] ?> candidature(s)</span>
    <a href="<?= url('espace_proprietaire', ['tab' => 'candidatures', 'annonce' => $a['id_annonce']]) ?>" class="lien-fort">Gérer →</a>
  </div>
<?php };

/** Ligne de candidature avec boutons Accepter / Refuser. */
$ligneCandidature = function (array $c, bool $detail = true) { ?>
  <div class="carte ligne">
    <div class="ligne-personne">
      <span class="avatar"><?= e(initiales($c['prenom_gardien'], $c['nom_gardien'])) ?></span>
      <div>
        <a href="<?= url('gardien', ['id' => $c['id_gardien_candidature']]) ?>" class="fort">
          <?= e($c['prenom_gardien']) ?> <?= e(mb_substr($c['nom_gardien'], 0, 1)) ?>.
        </a>
        <p class="note petit">
          <?= $c['nombre_avis_gardien'] > 0 ? '★ ' . note_fr($c['note_moyenne']) . ' · ' : '' ?><?= (int) $c['gardes'] ?> garde(s) terminée(s)
        </p>
      </div>
    </div>
    <div class="ligne-texte">
      <?php if ($detail): ?><p class="petit muted"><?= e($c['nom_annonce']) ?></p><?php endif; ?>
      <p class="citation">« <?= e($c['message']) ?> »</p>
    </div>
    <?php if ($c['statut'] === 'en_attente' && $c['statut_annonce'] === 'ouverte'): ?>
      <form method="post" action="<?= url('decider') ?>" class="actions">
        <?= csrf_champ() ?>
        <input type="hidden" name="id" value="<?= (int) $c['id_candidature'] ?>">
        <button name="decision" value="accepter" class="btn btn-sm">Accepter</button>
        <button name="decision" value="refuser" class="btn btn-sm btn-danger">Refuser</button>
      </form>
    <?php else: ?>
      <?= badge($c['statut']) ?>
    <?php endif; ?>
  </div>
<?php };
?>

<?php if ($tab === 'accueil'): ?>
  <div class="titre-ligne">
    <div>
      <h1>Bonjour, <?= e($u['prenom']) ?></h1>
      <p class="muted">Retrouvez vos annonces et les candidatures reçues.</p>
    </div>
    <a href="<?= url('creer') ?>" class="btn">+ Publier une annonce</a>
  </div>

  <div class="stats">
    <div class="carte stat"><p>Annonces ouvertes</p><strong><?= $stats['ouvertes'] ?></strong><small>Visibles par les gardiens</small></div>
    <div class="carte stat"><p>Candidatures reçues</p><strong><?= $stats['candidatures'] ?></strong><small><?= $stats['attente'] ?> à examiner</small></div>
    <div class="carte stat"><p>Gardes terminées</p><strong><?= $stats['terminees'] ?></strong><small>Depuis votre inscription</small></div>
    <div class="carte stat"><p>Note moyenne</p><strong><?= $stats['nb_avis'] ? note_fr($stats['note']) . ' / 5' : '—' ?></strong><small>Sur <?= $stats['nb_avis'] ?> avis reçu(s)</small></div>
  </div>

  <div class="titre-ligne">
    <h2>Mes annonces récentes</h2>
    <a href="<?= $lien('annonces') ?>" class="lien-fort">Voir toutes →</a>
  </div>
  <?php if (!$annonces): ?><div class="vide">Vous n'avez pas encore publié d'annonce.</div><?php endif; ?>
  <?php foreach ($annonces as $a) $ligneAnnonce($a); ?>

  <h2>Candidatures à examiner</h2>
  <?php if (!$candidatures): ?><div class="vide">Aucune candidature en attente.</div><?php endif; ?>
  <?php foreach ($candidatures as $c) $ligneCandidature($c); ?>

<?php elseif ($tab === 'annonces'): ?>
  <div class="titre-ligne">
    <h1>Mes annonces</h1>
    <a href="<?= url('creer') ?>" class="btn">+ Publier une annonce</a>
  </div>
  <?php if (!$annonces): ?><div class="vide">Vous n'avez pas encore publié d'annonce.</div><?php endif; ?>
  <?php foreach ($annonces as $a) $ligneAnnonce($a); ?>

<?php elseif ($tab === 'candidatures'): ?>
  <h1>Candidatures reçues</h1>
  <?php if ($annonceFiltre): ?>
    <p class="muted">Pour « <?= e($annonceFiltre['nom_annonce']) ?> » · <a href="<?= $lien('candidatures') ?>" class="lien-fort">Voir toutes</a></p>
  <?php endif; ?>
  <?php if (!$candidatures): ?><div class="vide">Aucune candidature pour le moment.</div><?php endif; ?>
  <?php foreach ($candidatures as $c) $ligneCandidature($c, !$annonceFiltre); ?>

<?php elseif ($tab === 'historique'): ?>
  <h1>Historique des gardes</h1>
  <p class="muted">Indiquez quand une garde est terminée pour pouvoir noter le gardien.</p>
  <?php if (!$annonces): ?><div class="vide">Aucune garde attribuée pour le moment.</div><?php endif; ?>
  <?php foreach ($annonces as $a): ?>
    <div class="carte ligne">
      <div>
        <a href="<?= url('annonce', ['id' => $a['id_annonce']]) ?>" class="fort"><?= e($a['nom_annonce']) ?></a>
        <p class="petit muted"><?= e(periode_courte($a['date_debut'], $a['date_fin'])) ?> · <?= e(ville_courte($a['ville'])) ?></p>
      </div>
      <?= badge($a['statut']) ?>
      <?php if ($a['statut'] === 'pourvue'): ?>
        <form method="post" action="<?= url('terminer') ?>">
          <?= csrf_champ() ?>
          <input type="hidden" name="id" value="<?= (int) $a['id_annonce'] ?>">
          <button class="btn btn-sm btn-contour">Marquer terminée</button>
        </form>
      <?php elseif ($dejaNote($a['id_annonce'])): ?>
        <span class="petit muted">Avis envoyé ✓</span>
      <?php else: ?>
        <a href="<?= url('noter', ['id' => $a['id_annonce']]) ?>" class="btn btn-sm">Laisser un avis</a>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

<?php elseif ($tab === 'avis'): ?>
  <h1>Avis reçus</h1>
  <p class="muted">Ce que les gardiens disent de vous.</p>
  <?php if (!$avis): ?><div class="vide">Pas encore d'avis publié.</div><?php endif; ?>
  <?php foreach ($avis as $a): ?>
    <div class="carte ligne">
      <div>
        <p class="fort"><?= e($a['prenom_gardien']) ?></p>
        <p class="petit muted"><?= e($a['nom_annonce']) ?> · <?= e(date_fr($a['date_poste'])) ?></p>
      </div>
      <p class="citation">« <?= e($a['commentaire']) ?> »</p>
      <span class="note"><?= etoiles((int) $a['nombre_etoiles']) ?></span>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
