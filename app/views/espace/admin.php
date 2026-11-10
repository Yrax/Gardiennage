<?php
$titre = 'Administration';
$lien = fn($t) => url('admin', ['tab' => $t]);
$sidebar = ['titre' => 'Administration', 'liens' => [
  ["Vue d'ensemble", $lien('accueil'),      $tab === 'accueil'],
  ['Utilisateurs',   $lien('utilisateurs'), $tab === 'utilisateurs'],
  ['Annonces',       $lien('annonces'),     $tab === 'annonces'],
  ['Avis',           $lien('avis'),         $tab === 'avis'],
]];

/** Ligne d'avis avec boutons de modération. */
$ligneAvis = function (array $a) use ($tab) {
  $surGardien = $a['auteur'] === 'proprietaire';
  $nom = $surGardien
    ? $a['prenom_gardien'] . ' ' . mb_substr($a['nom_gardien'], 0, 1) . '.'
    : $a['prenom_util_prop'] . ' ' . mb_substr($a['nom_utilisateur_proprietaire'], 0, 1) . '.';
  ?>
  <div class="carte ligne">
    <div>
      <p class="fort"><?= e($nom) ?></p>
      <p class="petit muted">Avis sur un <?= $surGardien ? 'gardien' : 'propriétaire' ?> · <?= e(date_fr($a['date_poste'])) ?> · <?= etoiles((int) $a['nombre_etoiles']) ?></p>
    </div>
    <p class="citation">« <?= e($a['commentaire']) ?> »</p>
    <?= badge($a['statut']) ?>
    <form method="post" action="<?= url('moderer') ?>" class="actions">
      <?= csrf_champ() ?>
      <input type="hidden" name="id" value="<?= (int) $a['id_avis'] ?>">
      <input type="hidden" name="tab" value="<?= e($tab) ?>">
      <?php if ($a['statut'] !== 'publie'): ?>
        <button name="decision" value="publier" class="btn btn-sm btn-contour">Publier</button>
      <?php endif; ?>
      <?php if ($a['statut'] !== 'masque'): ?>
        <button name="decision" value="masquer" class="btn btn-sm btn-danger">Masquer</button>
      <?php endif; ?>
    </form>
  </div>
<?php };
?>

<?php if ($tab === 'accueil'): ?>
  <h1>Vue d'ensemble</h1>
  <p class="muted">Suivez l'activité et traitez les avis signalés.</p>

  <div class="stats">
    <div class="carte stat"><p>Utilisateurs</p><strong><?= $stats['utilisateurs'] ?></strong><small>Propriétaires et gardiens</small></div>
    <div class="carte stat"><p>Annonces actives</p><strong><?= $stats['actives'] ?></strong><small>En ligne actuellement</small></div>
    <div class="carte stat"><p>Avis à modérer</p><strong><?= $stats['a_moderer'] ?></strong><small>Nécessitent une vérification</small></div>
    <div class="carte stat"><p>Gardes terminées</p><strong><?= $stats['terminees'] ?></strong><small>Au total</small></div>
  </div>

  <div class="titre-ligne">
    <h2>Avis en attente de modération</h2>
    <span class="badge badge-orange"><?= $stats['a_moderer'] ?> à traiter</span>
  </div>
  <?php if (!$avis): ?><div class="vide">Aucun avis à modérer. 🎉</div><?php endif; ?>
  <?php foreach ($avis as $a) $ligneAvis($a); ?>

  <h2>Gestion rapide</h2>
  <div class="grille-2">
    <div class="carte ligne">
      <div><p class="fort">Utilisateurs</p><p class="petit muted">Comptes et rôles</p></div>
      <a href="<?= $lien('utilisateurs') ?>" class="btn btn-sm btn-contour">Gérer</a>
    </div>
    <div class="carte ligne">
      <div><p class="fort">Annonces</p><p class="petit muted">Annonces ouvertes ou clôturées</p></div>
      <a href="<?= $lien('annonces') ?>" class="btn btn-sm btn-contour">Gérer</a>
    </div>
  </div>

<?php elseif ($tab === 'utilisateurs'): ?>
  <h1>Utilisateurs</h1>
  <div class="carte tableau-conteneur">
    <table class="tableau">
      <thead><tr><th>Nom</th><th>E-mail</th><th>Ville</th><th>Rôles</th></tr></thead>
      <tbody>
        <?php foreach ($utilisateurs as $x): ?>
          <tr>
            <td><?= e($x['prenom_utilisateur'] . ' ' . $x['nom_utilisateur']) ?></td>
            <td><?= e($x['mail']) ?></td>
            <td><?= e($x['adresse']) ?></td>
            <td>
              <?= $x['id_proprietaire'] ? '<span class="badge badge-vert">Propriétaire</span>' : '' ?>
              <?= $x['id_gardien'] ? '<span class="badge badge-vert">Gardien</span>' : '' ?>
              <?= $x['est_administrateur'] ? '<span class="badge badge-orange">Admin</span>' : '' ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php elseif ($tab === 'annonces'): ?>
  <h1>Annonces</h1>
  <div class="carte tableau-conteneur">
    <table class="tableau">
      <thead><tr><th>Annonce</th><th>Propriétaire</th><th>Dates</th><th>Candidatures</th><th>Statut</th></tr></thead>
      <tbody>
        <?php foreach ($annonces as $a): ?>
          <tr>
            <td><a href="<?= url('annonce', ['id' => $a['id_annonce']]) ?>" class="lien-fort"><?= e($a['nom_annonce']) ?></a></td>
            <td><?= e($a['prenom_util_prop'] . ' ' . $a['nom_utilisateur_proprietaire']) ?></td>
            <td><?= e(periode_courte($a['date_debut'], $a['date_fin'])) ?></td>
            <td><?= (int) $a['nb_candidatures'] ?></td>
            <td><?= badge($a['statut']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php elseif ($tab === 'avis'): ?>
  <h1>Tous les avis</h1>
  <?php if (!$avis): ?><div class="vide">Aucun avis.</div><?php endif; ?>
  <?php foreach ($avis as $a) $ligneAvis($a); ?>
<?php endif; ?>
