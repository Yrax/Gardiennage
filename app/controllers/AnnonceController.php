<?php
class AnnonceController {

  public function accueil(): void {
    $ville  = trim($_GET['ville'] ?? '');
    $espece = trim($_GET['espece'] ?? '');
    $recherche = $ville !== '' || $espece !== '';
    vue('accueil', [
      'gardiens'  => Gardien::enAvant($recherche ? 12 : 3, $ville, $espece),
      'annonces'  => array_slice(Annonce::toutesOuvertes(), 0, 3),
      'ville'     => $ville,
      'espece'    => $espece,
      'recherche' => $recherche,
      'page'      => 'accueil',
    ]);
  }

  public function liste(): void {
    $ville  = trim($_GET['ville'] ?? '');
    $espece = trim($_GET['espece'] ?? '');
    vue('annonces/liste', [
      'annonces' => Annonce::toutesOuvertes($ville, $espece),
      'ville'    => $ville,
      'espece'   => $espece,
      'page'     => 'annonces',
    ]);
  }

  public function detail(): void {
    $annonce = Annonce::trouver((int) ($_GET['id'] ?? 0));
    if (!$annonce) {
      flash("Cette annonce n'existe pas.", 'erreur');
      redirect('annonces');
    }
    $u = utilisateur();
    vue('annonces/detail', [
      'annonce'       => $annonce,
      'animaux'       => Annonce::animaux($annonce['id_annonce']),
      'note'          => Proprietaire::note($annonce['id_proprietaire_annonce']),
      'candidature'   => $u && $u['id_gardien'] ? Candidature::existe($annonce['id_annonce'], $u['id_gardien']) : null,
      'estProprio'    => $u && $u['id_proprietaire'] === (int) $annonce['id_proprietaire_annonce'],
      'page'          => 'annonces',
    ]);
  }

  public function profilGardien(): void {
    $gardien = Gardien::trouver((int) ($_GET['id'] ?? 0));
    if (!$gardien) {
      flash("Ce gardien n'existe pas.", 'erreur');
      redirect('accueil');
    }
    vue('gardien', [
      'gardien' => $gardien,
      'avis'    => Avis::surGardien($gardien['id_gardien']),
      'page'    => '',
    ]);
  }

  public function comment(): void {
    vue('comment', ['page' => 'comment']);
  }

  public function creer(): void {
    $idProprio = exiger_proprietaire();
    $erreurs = [];
    $animaux = Proprietaire::animaux($idProprio);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      verifier_csrf();
      $d = [
        'titre'       => post('titre'),
        'ville'       => post('ville'),
        'type_garde'  => post('type_garde'),
        'description' => post('description'),
        'a_prevoir'   => post('a_prevoir'),
        'date_debut'  => post('date_debut'),
        'date_fin'    => post('date_fin'),
      ];
      if ($d['titre'] === '')       $erreurs[] = 'Le titre est obligatoire.';
      if (mb_strlen($d['titre']) > 100) $erreurs[] = 'Le titre fait 100 caractères maximum.';
      if ($d['ville'] === '')       $erreurs[] = 'La ville est obligatoire.';
      if (!in_array($d['type_garde'], ['À domicile', 'Chez le propriétaire', 'Visites'], true)) $erreurs[] = 'Type de garde invalide.';
      if ($d['description'] === '') $erreurs[] = 'La description est obligatoire.';
      if (!strtotime($d['date_debut']) || !strtotime($d['date_fin'])) {
        $erreurs[] = 'Les dates sont obligatoires.';
      } elseif ($d['date_fin'] < $d['date_debut']) {
        $erreurs[] = 'La date de fin doit être après la date de début.';
      }

      // Animal existant ou nouvel animal
      $choix = post('animal');
      if ($choix !== '' && $choix !== 'nouveau') {
        $ids = array_column($animaux, 'id_animal');
        $animal = (int) $choix;
        if (!in_array($animal, array_map('intval', $ids), true)) $erreurs[] = 'Animal invalide.';
      } else {
        $animal = [
          'nom' => post('nom_animal'), 'espece' => post('espece'), 'race' => post('race'),
          'age' => post('age'), 'besoin' => post('besoin'),
        ];
        if ($animal['nom'] === '' || $animal['espece'] === '') $erreurs[] = "Le nom et l'espèce de l'animal sont obligatoires.";
      }

      if (!$erreurs) {
        $d['date_debut'] .= ' 09:00:00';
        $d['date_fin']   .= ' 18:00:00';
        $id = Annonce::creer($idProprio, $d, $animal);
        flash('Votre annonce est en ligne !');
        redirect('annonce', ['id' => $id]);
      }
    }

    vue('annonces/creer', [
      'erreurs' => $erreurs,
      'animaux' => $animaux,
      'page'    => 'annonces',
    ]);
  }

  public function terminer(): void {
    verifier_csrf();
    $idProprio = exiger_proprietaire();
    if (Annonce::terminer((int) post('id'), $idProprio)) {
      flash('Garde terminée. Vous pouvez maintenant laisser un avis au gardien.');
    }
    redirect('espace_proprietaire', ['tab' => 'historique']);
  }
}
