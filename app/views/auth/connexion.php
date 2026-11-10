<?php $titre = 'Connexion'; ?>
<div class="auth-form">
  <h1>Heureux de vous retrouver</h1>
  <p class="muted">Connectez-vous pour accéder à votre espace.</p>

  <?php if ($erreur): ?><div class="flash flash-erreur"><?= e($erreur) ?></div><?php endif; ?>

  <form method="post" action="<?= url('connexion') ?>">
    <?= csrf_champ() ?>
    <label class="champ"><span>Adresse e-mail</span>
      <input type="email" name="mail" required value="<?= e($mail) ?>" placeholder="vous@exemple.fr" autofocus>
    </label>
    <label class="champ"><span>Mot de passe</span>
      <input type="password" name="password" required placeholder="••••••••••••">
    </label>
    <button class="btn btn-bloc" type="submit">Se connecter</button>
  </form>
  <p class="petit muted centre">Pas encore de compte ? <a href="<?= url('inscription') ?>" class="lien-fort">Créer un compte</a></p>
</div>
