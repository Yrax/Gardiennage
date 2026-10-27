<?php
class AuthController {

  public function connexion(): void {
    if (utilisateur()) redirect('espace');
    $erreur = null;
    $mail = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      verifier_csrf();
      $mail = post('mail');
      $u = Utilisateur::parMail($mail);
      if ($u && password_verify($_POST['password'] ?? '', $u['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = Utilisateur::session((int) $u['id_utilisateur']);
        flash('Bonjour ' . $u['prenom_utilisateur'] . ' !');
        redirect('espace');
      }
      $erreur = 'Adresse e-mail ou mot de passe incorrect.';
    }

    vue('auth/connexion', ['erreur' => $erreur, 'mail' => $mail], 'auth');
  }

  public function inscription(): void {
    if (utilisateur()) redirect('espace');
    $erreurs = [];
    $d = ['prenom' => '', 'nom' => '', 'mail' => '', 'tel' => '', 'adresse' => '', 'description' => ''];
    $roles = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      verifier_csrf();
      foreach (array_keys($d) as $cle) $d[$cle] = post($cle);
      $d['password'] = $_POST['password'] ?? '';
      $roles = array_values(array_intersect((array) ($_POST['roles'] ?? []), ['proprietaire', 'gardien']));

      if ($d['prenom'] === '' || $d['nom'] === '') $erreurs[] = 'Le prénom et le nom sont obligatoires.';
      if (mb_strlen($d['prenom']) > 25 || mb_strlen($d['nom']) > 25) $erreurs[] = 'Prénom et nom : 25 caractères maximum.';
      if (!filter_var($d['mail'], FILTER_VALIDATE_EMAIL)) $erreurs[] = 'Adresse e-mail invalide.';
      elseif (Utilisateur::parMail($d['mail'])) $erreurs[] = 'Un compte existe déjà avec cette adresse.';
      if (mb_strlen($d['password']) < 8) $erreurs[] = 'Le mot de passe doit faire au moins 8 caractères.';
      if ($d['adresse'] === '') $erreurs[] = 'La ville est obligatoire.';
      if (!$roles) $erreurs[] = 'Choisissez au moins un profil (propriétaire ou gardien).';

      if (!$erreurs) {
        $id = Utilisateur::creer($d, $roles);
        session_regenerate_id(true);
        $_SESSION['user'] = Utilisateur::session($id);
        flash('Bienvenue sur PetSitter Local !');
        redirect('espace');
      }
    }

    vue('auth/inscription', ['erreurs' => $erreurs, 'd' => $d, 'roles' => $roles], 'auth');
  }

  public function deconnexion(): void {
    $_SESSION = [];
    session_destroy();
    session_start();
    flash('Vous êtes déconnecté.');
    redirect('accueil');
  }
}
