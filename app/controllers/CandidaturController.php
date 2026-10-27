<?php
class CandidatureController {

  /** Un gardien postule à une annonce. */
  public function postuler(): void {
    verifier_csrf();
    $idGardien = exiger_gardien();
    $idAnnonce = (int) post('id_annonce');
    $message   = post('message');
    $annonce   = Annonce::trouver($idAnnonce);

    if (!$annonce || $annonce['statut'] !== 'ouverte') {
      flash("Cette annonce n'accepte plus de candidatures.", 'erreur');
    } elseif ((int) $annonce['id_utilisateur_proprietaire'] === utilisateur()['id']) {
      flash('Vous ne pouvez pas postuler à votre propre annonce.', 'erreur');
    } elseif (Candidature::existe($idAnnonce, $idGardien)) {
      flash('Vous avez déjà postulé à cette annonce.', 'erreur');
    } elseif (mb_strlen($message) < 10) {
      flash('Votre message doit faire au moins 10 caractères.', 'erreur');
    } else {
      Candidature::creer($idAnnonce, $idGardien, $message);
      flash('Candidature envoyée ! Suivez la réponse dans votre espace gardien.');
    }
    redirect('annonce', ['id' => $idAnnonce]);
  }

  /** Le propriétaire accepte ou refuse une candidature. */
  public function decider(): void {
    verifier_csrf();
    $idProprio = exiger_proprietaire();
    $c = Candidature::trouver((int) post('id'));

    if (!$c || (int) $c['id_proprietaire_annonce'] !== $idProprio) {
      flash('Candidature introuvable.', 'erreur');
    } elseif ($c['statut'] !== 'en_attente' || $c['statut_annonce'] !== 'ouverte') {
      flash('Cette candidature a déjà été traitée.', 'erreur');
    } else {
      $accepter = post('decision') === 'accepter';
      Candidature::decider($c, $accepter);
      flash($accepter
        ? 'Candidature acceptée : la garde est attribuée à ' . $c['prenom_gardien'] . '.'
        : 'Candidature refusée.');
    }
    redirect('espace_proprietaire', ['tab' => 'candidatures']);
  }
}
