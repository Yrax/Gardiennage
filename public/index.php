<?php
session_start();

$racine = dirname(__DIR__);
require $racine . '/config/database.php';
require $racine . '/app/helpers.php';

foreach (glob($racine . '/app/models/*.php') as $f) require_once $f;
foreach (glob($racine . '/app/controllers/*.php') as $f) require_once $f;

$action = $_GET['action'] ?? 'accueil';

match ($action) {
  // Pages publiques
  'accueil'          => (new AnnonceController())->accueil(),
  'annonces', 'liste'=> (new AnnonceController())->liste(),
  'annonce'          => (new AnnonceController())->detail(),
  'gardien'          => (new AnnonceController())->profilGardien(),
  'comment'          => (new AnnonceController())->comment(),

  // Authentification
  'connexion'        => (new AuthController())->connexion(),
  'inscription'      => (new AuthController())->inscription(),
  'deconnexion'      => (new AuthController())->deconnexion(),

  // Propriétaire
  'creer'            => (new AnnonceController())->creer(),
  'terminer'         => (new AnnonceController())->terminer(),
  'decider'          => (new CandidatureController())->decider(),

  // Gardien
  'postuler'         => (new CandidatureController())->postuler(),
  'profil'           => (new EspaceController())->majProfil(),

  // Avis
  'noter'            => (new AvisController())->noter(),
  'moderer'          => (new AvisController())->moderer(),

  // Espaces
  'espace'           => (new EspaceController())->espace(),
  'espace_proprietaire' => (new EspaceController())->proprietaire(),
  'espace_gardien'   => (new EspaceController())->gardien(),
  'admin'            => (new EspaceController())->admin(),

  default            => (new AnnonceController())->accueil(),
};
