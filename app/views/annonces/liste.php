<?php $titre = 'Annonces'; $u = utilisateur(); ?>
<div class="conteneur section">
  <p class="fil">Accueil / Annonces</p>
  <div class="titre-ligne">
    <div>
      <h1>Annonces de garde</h1>
      <p class="muted">Des propriétaires près de chez vous cherchent un gardien.</p>
    </div>
    <?php if ($u && $u['id_proprietaire']): ?>
      <a href="<?= url('creer') ?>" class="btn">+ Publier une annonce</a>
    <?php endif; ?>
  </div>

  <form class="recherche recherche-compacte" method="get" action="index.php">
    <input type="hidden" name="action" value="annonces">
    <label><span>Où ?</span><input type="text" name="ville" value="<?= e($ville) ?>" placeholder="Ville ou code postal"></label>
    <label><span>Animal</span><input type="text" name="espece" value="<?= e($espece) ?>" placeholder="Chien, chat, autre..."></label>
    <button class="btn" type="submit">Filtrer</button>
  </form>

  <?php if (!$annonces): ?>
    <div class="vide">Aucune annonce ouverte pour le moment.</div>
  <?php endif; ?>

  <div class="grille-3">
    <?php foreach ($annonces as $a): ?>
      <?php require __DIR__ . '/_carte.php'; ?>
    <?php endforeach; ?>
  </div>
</div>
