<?php
class AvisController {

  /**
   * Laisser un avis sur une garde terminée.
   * Le propriétaire note le gardien, le gardien note le propriétaire.
   */
  public function noter(): void {
    $u = exiger_connexion();
    $annonce = Annonce::trouver((int) ($_GET['id'] ?? $_POST['id'] ?? 0));

    $auteur = null;
    if ($annonce && $annonce['statut'] === 'terminee') {
      if ($u['id_proprietaire'] === (int) $annonce['id_proprietaire_annonce']) $auteur = 'proprietaire';
      elseif ($u['id_gardien'] && $u['id_gardien'] === (int) $annonce['id_gardien_annonce']) $auteur = 'gardien';
    }
    if (!$auteur) {
      flash('Vous ne pouvez pas noter cette garde.', 'erreur');
      redirect('espace');
    }
    $retour = $auteur === 'proprietaire' ? ['espace_proprietaire', ['tab' => 'historique']]
                                         : ['espace_gardien', ['tab' => 'avis']];
    if (Avis::dejaPoste($annonce['id_annonce'], $auteur)) {
      flash('Vous avez déjà laissé un avis pour cette garde.', 'erreur');
      redirect(...$retour);
    }

    $erreurs = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      verifier_csrf();
      $etoiles = (int) post('etoiles');
      $commentaire = post('commentaire');
      if ($etoiles < 1 || $etoiles > 5) $erreurs[] = 'Choisissez une note entre 1 et 5 étoiles.';
      if ($commentaire === '' || mb_strlen($commentaire) > 255) $erreurs[] = 'Le commentaire est obligatoire (255 caractères maximum).';
      if (!$erreurs) {
        Avis::creer($annonce, $auteur, $etoiles, $commentaire);
        flash('Merci ! Votre avis sera publié après vérification.');
        redirect(...$retour);
      }
    }

    vue('avis/noter', [
      'annonce' => $annonce,
      'auteur'  => $auteur,
      'erreurs' => $erreurs,
      'page'    => '',
    ]);
  }

  /** Administration : publier ou masquer un avis. */
  public function moderer(): void {
    verifier_csrf();
    exiger_admin();
    $statut = post('decision') === 'publier' ? 'publie' : 'masque';
    Avis::moderer((int) post('id'), $statut);
    flash($statut === 'publie' ? 'Avis publié.' : 'Avis masqué.');
    redirect('admin', ['tab' => post('tab') ?: 'accueil']);
  }
}
