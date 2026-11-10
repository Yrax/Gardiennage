<?php
/*
 * Gabarit des espaces connectés. La vue définit $sidebar :
 *   ['titre' => 'Propriétaire', 'liens' => [[libellé, url, actif?], ...]]
 */
require __DIR__ . '/head.php';
$u = utilisateur();
?>
<body class="corps-espace">
  <?php require __DIR__ . '/header.php'; ?>
  <div class="espace">
    <aside class="sidebar">
      <p class="sidebar-sur">Espace</p>
      <p class="sidebar-titre"><?= e($sidebar['titre']) ?></p>
      <nav>
        <?php foreach ($sidebar['liens'] as [$libelle, $lien, $actif]): ?>
          <a href="<?= $lien ?>" class="<?= $actif ? 'actif' : '' ?>"><?= e($libelle) ?></a>
        <?php endforeach; ?>
      </nav>
      <?php
        // Lien vers l'autre espace si l'utilisateur a plusieurs profils
        $autres = [];
        if ($u['id_proprietaire'] && $sidebar['titre'] !== 'Propriétaire') $autres[] = ['Espace propriétaire', url('espace_proprietaire')];
        if ($u['id_gardien'] && $sidebar['titre'] !== 'Gardien') $autres[] = ['Espace gardien', url('espace_gardien')];
        if ($u['admin'] && $sidebar['titre'] !== 'Administration') $autres[] = ['Administration', url('admin')];
      ?>
      <?php if ($autres): ?>
        <div class="sidebar-autres">
          <?php foreach ($autres as [$libelle, $lien]): ?>
            <a href="<?= $lien ?>">↔ <?= e($libelle) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <a href="<?= url('deconnexion') ?>" class="btn-deconnexion">Se déconnecter</a>
    </aside>
    <section class="espace-contenu"><?= $contenu ?></section>
  </div>
</body>
</html>
