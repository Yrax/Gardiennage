<?php require __DIR__ . '/head.php'; ?>
<body>
  <div class="auth">
    <div class="auth-gauche">
      <a href="<?= url('accueil') ?>" class="logo-texte">PetSitter Local</a>
      <div>
        <h1 class="auth-slogan">La confiance se construit<br>patte après patte.</h1>
        <p class="muted">Une communauté de propriétaires et de gardiens qui<br>prennent soin de ce qui compte le plus.</p>
      </div>
      <div class="carte-illustration">
        <span class="pattes">🐾</span>
        <p>Des gardes plus sereines,<br>pour eux comme pour vous.</p>
      </div>
      <a href="<?= url('accueil') ?>" class="lien-retour">← Retour à l'accueil</a>
    </div>
    <div class="auth-droite">
      <?php if ($f = flash()): ?>
        <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
      <?php endif; ?>
      <?= $contenu ?>
    </div>
  </div>
</body>
</html>
