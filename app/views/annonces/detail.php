<?php $titre = $annonce['nom_annonce']; $u = utilisateur(); ?>
<div class="conteneur section">
  <p class="fil">
    <a href="<?= url('accueil') ?>">Accueil</a> / <a href="<?= url('annonces') ?>">Annonces</a> / Détail
  </p>

  <div class="detail-grille">
    <div>
      <div class="detail-image">
        <span><?= emoji_espece($annonce['espece']) ?></span>
        <span class="etiquette"><?= e(mb_strtoupper($annonce['type_garde'])) ?></span>
      </div>

      <h2>À propos de la garde</h2>
      <p class="muted texte"><?= nl2br(e($annonce['description'])) ?></p>

      <?php if ($animaux): ?>
        <h3>Animal</h3>
        <ul class="liste-puces muted">
          <?php foreach ($animaux as $an): ?>
            <li>
              <?= e($an['nom_animal']) ?> — <?= e($an['espece']) ?><?= $an['race'] ? ', ' . e($an['race']) : '' ?><?= $an['age'] ? ', ' . (int) $an['age'] . ' an(s)' : '' ?>
              <?= $an['besoin_particulier'] ? '<br><em>' . e($an['besoin_particulier']) . '</em>' : '' ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php if ($annonce['a_prevoir']): ?>
        <h3>À prévoir</h3>
        <ul class="liste-puces muted">
          <?php foreach (preg_split('/\R/', $annonce['a_prevoir']) as $ligne): ?>
            <?php if (trim($ligne) !== ''): ?><li><?= e($ligne) ?></li><?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <div>
      <h1><?= e($annonce['nom_annonce']) ?></h1>
      <p class="muted petit"><?= e($annonce['ville']) ?> · <?= e($annonce['type_garde']) ?></p>
      <p class="note">
        <?php if ($note['nb']): ?>
          ★ <?= note_fr($note['note']) ?> · <?= $note['nb'] ?> avis sur <?= e($annonce['prenom_util_prop']) ?>
        <?php else: ?>
          Publiée par <?= e($annonce['prenom_util_prop']) ?>
        <?php endif; ?>
      </p>

      <div class="carte carte-info">
        <p class="sur-titre">Dates de la garde</p>
        <p class="fort"><?= e(periode_longue($annonce['date_debut'], $annonce['date_fin'])) ?></p>
      </div>

      <div class="carte carte-candidature">
        <?php if ($annonce['statut'] !== 'ouverte'): ?>
          <h2>Garde <?= $annonce['statut'] === 'pourvue' ? 'pourvue' : 'terminée' ?></h2>
          <p class="muted">Cette annonce n'accepte plus de candidatures.</p>

        <?php elseif ($estProprio): ?>
          <h2>C'est votre annonce</h2>
          <p class="muted"><?= (int) $annonce['nb_candidatures'] ?> candidature(s) reçue(s).</p>
          <a href="<?= url('espace_proprietaire', ['tab' => 'candidatures', 'annonce' => $annonce['id_annonce']]) ?>" class="btn btn-bloc">Voir les candidatures</a>

        <?php elseif ($candidature): ?>
          <h2>Candidature envoyée</h2>
          <p class="muted">Envoyée le <?= e(date_fr($candidature['date_envoi'])) ?>. <?= badge($candidature['statut']) ?></p>
          <a href="<?= url('espace_gardien', ['tab' => 'candidatures']) ?>" class="btn btn-bloc">Suivre ma candidature</a>

        <?php elseif ($u && $u['id_gardien']): ?>
          <h2>Cette garde vous intéresse ?</h2>
          <p class="muted petit">Présentez-vous au propriétaire et expliquez pourquoi vous êtes la bonne personne.</p>
          <form method="post" action="<?= url('postuler') ?>">
            <?= csrf_champ() ?>
            <input type="hidden" name="id_annonce" value="<?= (int) $annonce['id_annonce'] ?>">
            <label class="champ">
              <span>Votre message de motivation</span>
              <textarea name="message" rows="4" required minlength="10"
                placeholder="Bonjour, je serais ravi(e) de m'occuper de <?= e($annonce['nom_animal'] ?? 'votre animal') ?>..."></textarea>
            </label>
            <button class="btn btn-bloc" type="submit">Envoyer ma candidature</button>
          </form>
          <p class="petit muted">Vous pourrez suivre la réponse dans votre espace gardien.</p>

        <?php else: ?>
          <h2>Cette garde vous intéresse ?</h2>
          <p class="muted petit">
            <?= $u ? 'Seuls les comptes gardien peuvent postuler.' : 'Connectez-vous avec un compte gardien pour envoyer votre candidature.' ?>
          </p>
          <?php if (!$u): ?>
            <a href="<?= url('connexion') ?>" class="btn btn-bloc">Se connecter</a>
            <p class="petit muted centre">Pas encore de compte ? <a href="<?= url('inscription') ?>" class="lien-fort">Créer un compte</a></p>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
