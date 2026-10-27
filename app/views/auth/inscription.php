<?php $titre = 'Inscription'; ?>
<div class="auth-form">
  <h1>Rejoignez la communauté</h1>
  <p class="muted">Créez votre compte en une minute.</p>

  <?php if ($erreurs): ?>
    <div class="flash flash-erreur"><?php foreach ($erreurs as $er) echo e($er) . '<br>'; ?></div>
  <?php endif; ?>

  <form method="post" action="<?= url('inscription') ?>">
    <?= csrf_champ() ?>
    <p class="champ-titre">Je suis…</p>
    <div class="choix-roles">
      <label class="choix">
        <input type="checkbox" name="roles[]" value="proprietaire" <?= in_array('proprietaire', $roles) ? 'checked' : '' ?>>
        <span><strong>Propriétaire</strong><br><small>Je cherche un gardien</small></span>
      </label>
      <label class="choix">
        <input type="checkbox" name="roles[]" value="gardien" <?= in_array('gardien', $roles) ? 'checked' : '' ?>>
        <span><strong>Gardien</strong><br><small>Je garde des animaux</small></span>
      </label>
    </div>

    <div class="deux-colonnes">
      <label class="champ"><span>Prénom</span><input type="text" name="prenom" maxlength="25" required value="<?= e($d['prenom']) ?>"></label>
      <label class="champ"><span>Nom</span><input type="text" name="nom" maxlength="25" required value="<?= e($d['nom']) ?>"></label>
    </div>
    <label class="champ"><span>Adresse e-mail</span>
      <input type="email" name="mail" maxlength="100" required value="<?= e($d['mail']) ?>" placeholder="vous@exemple.fr">
    </label>
    <label class="champ"><span>Mot de passe (8 caractères minimum)</span>
      <input type="password" name="password" minlength="8" required>
    </label>
    <div class="deux-colonnes">
      <label class="champ"><span>Ville</span><input type="text" name="adresse" maxlength="100" required value="<?= e($d['adresse']) ?>" placeholder="Massy, 91300"></label>
      <label class="champ"><span>Téléphone (facultatif)</span><input type="tel" name="tel" maxlength="20" value="<?= e($d['tel']) ?>"></label>
    </div>
    <label class="champ"><span>Présentation (facultatif)</span>
      <textarea name="description" rows="2" maxlength="255" placeholder="Quelques mots sur vous et vos animaux"><?= e($d['description']) ?></textarea>
    </label>
    <button class="btn btn-bloc" type="submit">Créer mon compte</button>
  </form>
  <p class="petit muted centre">Déjà inscrit ? <a href="<?= url('connexion') ?>" class="lien-fort">Se connecter</a></p>
</div>
