<?php
$titre = 'Laisser un avis';
if ($auteur === 'proprietaire') {
  $cible = Gardien::trouver((int) $annonce['id_gardien_annonce'])['prenom_gardien'] ?? 'votre gardien';
} else {
  $cible = $annonce['prenom_util_prop'];
}
?>
<div class="conteneur section etroit">
  <h1>Laisser un avis à <?= e($cible) ?></h1>
  <p class="muted"><?= e($annonce['nom_annonce']) ?> · <?= e(periode_courte($annonce['date_debut'], $annonce['date_fin'])) ?></p>

  <?php if ($erreurs): ?>
    <div class="flash flash-erreur"><?php foreach ($erreurs as $er) echo e($er) . '<br>'; ?></div>
  <?php endif; ?>

  <form method="post" action="<?= url('noter', ['id' => $annonce['id_annonce']]) ?>" class="carte formulaire">
    <?= csrf_champ() ?>
    <p class="champ-titre">Votre note</p>
    <div class="etoiles-choix">
      <?php for ($i = 5; $i >= 1; $i--): ?>
        <input type="radio" name="etoiles" id="e<?= $i ?>" value="<?= $i ?>" <?= (int) ($_POST['etoiles'] ?? 0) === $i ? 'checked' : '' ?>>
        <label for="e<?= $i ?>" title="<?= $i ?> étoile(s)">★</label>
      <?php endfor; ?>
    </div>
    <label class="champ"><span>Votre commentaire</span>
      <textarea name="commentaire" rows="3" maxlength="255" required><?= e($_POST['commentaire'] ?? '') ?></textarea>
    </label>
    <button class="btn btn-bloc" type="submit">Envoyer mon avis</button>
    <p class="petit muted">Votre avis sera publié après vérification par notre équipe.</p>
  </form>
</div>
