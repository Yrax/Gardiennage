<?php $titre = 'Publier une annonce'; $v = fn($k) => e($_POST[$k] ?? ''); ?>
<div class="conteneur section etroit">
  <p class="fil"><a href="<?= url('espace_proprietaire') ?>">Mon espace</a> / Publier une annonce</p>
  <h1>Publier une annonce</h1>
  <p class="muted">Décrivez votre besoin, les gardiens près de chez vous pourront postuler.</p>

  <?php if ($erreurs): ?>
    <div class="flash flash-erreur"><?php foreach ($erreurs as $er) echo e($er) . '<br>'; ?></div>
  <?php endif; ?>

  <form method="post" class="carte formulaire">
    <?= csrf_champ() ?>
    <h3>La garde</h3>
    <label class="champ"><span>Titre de l'annonce</span>
      <input type="text" name="titre" maxlength="100" required value="<?= $v('titre') ?>" placeholder="Garde de Nala pendant un week-end">
    </label>
    <div class="deux-colonnes">
      <label class="champ"><span>Ville</span>
        <input type="text" name="ville" required value="<?= $v('ville') ?>" placeholder="Massy, 91300">
      </label>
      <label class="champ"><span>Type de garde</span>
        <select name="type_garde">
          <?php foreach (['Chez le propriétaire', 'À domicile', 'Visites'] as $t): ?>
            <option <?= ($_POST['type_garde'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="deux-colonnes">
      <label class="champ"><span>Date de début</span>
        <input type="date" name="date_debut" required value="<?= $v('date_debut') ?>" min="<?= date('Y-m-d') ?>">
      </label>
      <label class="champ"><span>Date de fin</span>
        <input type="date" name="date_fin" required value="<?= $v('date_fin') ?>" min="<?= date('Y-m-d') ?>">
      </label>
    </div>
    <label class="champ"><span>À propos de la garde</span>
      <textarea name="description" rows="4" required placeholder="Caractère de votre animal, habitudes..."><?= $v('description') ?></textarea>
    </label>
    <label class="champ"><span>À prévoir (une ligne par élément)</span>
      <textarea name="a_prevoir" rows="3" placeholder="Deux promenades quotidiennes&#10;Repas matin et soir"><?= $v('a_prevoir') ?></textarea>
    </label>

    <h3>L'animal</h3>
    <?php if ($animaux): ?>
      <label class="champ"><span>Choisir un de vos animaux</span>
        <select name="animal" id="choix-animal">
          <?php foreach ($animaux as $an): ?>
            <option value="<?= (int) $an['id_animal'] ?>" <?= ($_POST['animal'] ?? '') == $an['id_animal'] ? 'selected' : '' ?>>
              <?= e($an['nom_animal']) ?> (<?= e($an['espece']) ?>)
            </option>
          <?php endforeach; ?>
          <option value="nouveau" <?= ($_POST['animal'] ?? '') === 'nouveau' ? 'selected' : '' ?>>+ Ajouter un nouvel animal</option>
        </select>
      </label>
    <?php else: ?>
      <input type="hidden" name="animal" value="nouveau">
    <?php endif; ?>

    <div id="nouvel-animal">
      <div class="deux-colonnes">
        <label class="champ"><span>Nom</span><input type="text" name="nom_animal" maxlength="25" value="<?= $v('nom_animal') ?>"></label>
        <label class="champ"><span>Espèce</span>
          <select name="espece">
            <?php foreach (['Chien', 'Chat', 'Lapin', 'Oiseau', 'Rongeur', 'Poisson', 'Autre'] as $es): ?>
              <option <?= ($_POST['espece'] ?? '') === $es ? 'selected' : '' ?>><?= $es ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
      <div class="deux-colonnes">
        <label class="champ"><span>Race (facultatif)</span><input type="text" name="race" maxlength="25" value="<?= $v('race') ?>"></label>
        <label class="champ"><span>Âge (facultatif)</span><input type="number" name="age" min="0" max="40" value="<?= $v('age') ?>"></label>
      </div>
      <label class="champ"><span>Besoin particulier (facultatif)</span><input type="text" name="besoin" maxlength="255" value="<?= $v('besoin') ?>"></label>
    </div>

    <button class="btn btn-bloc" type="submit">Publier l'annonce</button>
  </form>
</div>
<script>
  // Affiche le formulaire « nouvel animal » seulement si besoin
  const choix = document.getElementById('choix-animal');
  const bloc = document.getElementById('nouvel-animal');
  if (choix) {
    const maj = () => bloc.hidden = choix.value !== 'nouveau';
    choix.addEventListener('change', maj);
    maj();
  }
</script>
