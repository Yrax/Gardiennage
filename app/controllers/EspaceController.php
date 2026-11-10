<?php
class EspaceController {

  /** « Mon espace » : envoie vers le bon tableau de bord. */
  public function espace(): void {
    $u = exiger_connexion();
    if ($u['admin'])           redirect('admin');
    if ($u['id_proprietaire']) redirect('espace_proprietaire');
    if ($u['id_gardien'])      redirect('espace_gardien');
    redirect('accueil');
  }

  public function proprietaire(): void {
    $id  = exiger_proprietaire();
    $tab = $_GET['tab'] ?? 'accueil';
    $d   = ['tab' => $tab, 'page' => 'espace'];

    switch ($tab) {
      case 'annonces':
        $d['annonces'] = Annonce::parProprietaire($id);
        break;
      case 'candidatures':
        $idAnnonce = (int) ($_GET['annonce'] ?? 0) ?: null;
        $d['candidatures'] = Candidature::recues($id, null, $idAnnonce);
        $d['annonceFiltre'] = $idAnnonce ? Annonce::trouver($idAnnonce) : null;
        break;
      case 'historique':
        $d['annonces'] = Annonce::parProprietaire($id, ['pourvue', 'terminee']);
        $d['dejaNote'] = fn($idA) => Avis::dejaPoste($idA, 'proprietaire');
        break;
      case 'avis':
        $d['avis'] = Avis::surProprietaire($id);
        break;
      default:
        $d['tab'] = 'accueil';
        $d['stats'] = Proprietaire::stats($id);
        $d['annonces'] = Annonce::parProprietaire($id, null, 3);
        $d['candidatures'] = Candidature::recues($id, 'en_attente');
    }
    vue('espace/proprietaire', $d, 'espace');
  }

  public function gardien(): void {
    $id  = exiger_gardien();
    $tab = $_GET['tab'] ?? 'accueil';
    $d   = ['tab' => $tab, 'page' => 'espace'];

    switch ($tab) {
      case 'candidatures':
        $d['candidatures'] = Candidature::parGardien($id);
        break;
      case 'gardes':
        $d['gardes'] = Annonce::parGardien($id);
        break;
      case 'profil':
        $d['profil'] = Utilisateur::trouver(utilisateur()['id']);
        break;
      case 'avis':
        $d['avis'] = Avis::surGardien($id);
        $d['aNoter'] = array_filter(Annonce::parGardien($id),
          fn($a) => $a['statut'] === 'terminee' && !Avis::dejaPoste($a['id_annonce'], 'gardien'));
        break;
      default:
        $d['tab'] = 'accueil';
        $d['stats'] = Gardien::stats($id);
        $d['gardien'] = Gardien::trouver($id);
        $d['candidatures'] = Candidature::parGardien($id, 5);
    }
    vue('espace/gardien', $d, 'espace');
  }

  public function majProfil(): void {
    verifier_csrf();
    $u = exiger_connexion();
    $tel = post('tel'); $adresse = post('adresse'); $description = post('description');
    if ($adresse === '' || mb_strlen($description) > 255 || mb_strlen($adresse) > 100) {
      flash('Vérifiez les champs : ville obligatoire, présentation 255 caractères maximum.', 'erreur');
    } else {
      Utilisateur::majProfil($u['id'], $tel, $adresse, $description);
      flash('Profil mis à jour.');
    }
    redirect('espace_gardien', ['tab' => 'profil']);
  }

  public function admin(): void {
    exiger_admin();
    $tab = $_GET['tab'] ?? 'accueil';
    $d   = ['tab' => $tab, 'page' => 'espace'];

    switch ($tab) {
      case 'utilisateurs':
        $d['utilisateurs'] = Utilisateur::tous();
        break;
      case 'annonces':
        $d['annonces'] = Annonce::toutes();
        break;
      case 'avis':
        $d['avis'] = Avis::tous();
        break;
      default:
        $d['tab'] = 'accueil';
        $d['stats'] = [
          'utilisateurs' => Utilisateur::compter(),
          'actives'      => Annonce::compter('ouverte'),
          'a_moderer'    => Avis::compterAModerer(),
          'terminees'    => Annonce::compter('terminee'),
        ];
        $d['avis'] = Avis::aModerer();
    }
    vue('espace/admin', $d, 'espace');
  }
}
