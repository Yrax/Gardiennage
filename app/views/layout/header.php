<?php $u = utilisateur(); $page ??= ''; ?>
<header class="entete">
  <div class="entete-inner">
    <a href="<?= url('accueil') ?>" class="logo">PetSitter<span>Local</span></a>
    <nav class="nav">
      <a href="<?= url('accueil') ?>" class="<?= $page === 'accueil' ? 'actif' : '' ?>">Accueil</a>
      <a href="<?= url('annonces') ?>" class="<?= $page === 'annonces' ? 'actif' : '' ?>">Annonces</a>
      <a href="<?= url('comment') ?>" class="<?= $page === 'comment' ? 'actif' : '' ?>">Comment ça marche ?</a>
    </nav>
    <div class="entete-actions">
      <?php if ($u): ?>
        <a href="<?= url('espace') ?>" class="lien-espace">Mon espace</a>
        <a href="<?= url('espace') ?>" class="avatar avatar-sm" title="<?= e($u['prenom'] . ' ' . $u['nom']) ?>"><?= e(initiales($u['prenom'], $u['nom'])) ?></a>
      <?php else: ?>
        <a href="<?= url('connexion') ?>" class="btn btn-contour btn-sm">Connexion</a>
        <a href="<?= url('inscription') ?>" class="btn btn-sm">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<?php if ($f = flash()): ?>
  <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endif; ?>
