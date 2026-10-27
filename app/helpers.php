<?php
// ---------- Affichage ----------

/** Échappe une valeur pour l'afficher dans du HTML. */
function e(mixed $valeur): string {
  return htmlspecialchars((string) ($valeur ?? ''), ENT_QUOTES, 'UTF-8');
}

function url(string $action, array $params = []): string {
  return 'index.php?' . http_build_query(['action' => $action] + $params);
}

function redirect(string $action, array $params = []): never {
  header('Location: ' . url($action, $params));
  exit;
}

/** Affiche une vue dans un gabarit (main, espace ou auth). */
function vue(string $nom, array $donnees = [], string $gabarit = 'main'): void {
  extract($donnees);
  ob_start();
  require __DIR__ . "/views/$nom.php";
  $contenu = ob_get_clean();
  require __DIR__ . "/views/layout/$gabarit.php";
}

function flash(?string $message = null, string $type = 'succes'): ?array {
  if ($message !== null) {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    return null;
  }
  $f = $_SESSION['flash'] ?? null;
  unset($_SESSION['flash']);
  return $f;
}

function initiales(?string $prenom, ?string $nom = ''): string {
  return mb_strtoupper(mb_substr($prenom ?? '', 0, 1) . mb_substr($nom ?? '', 0, 1));
}

function note_fr(float|string|null $note): string {
  return number_format((float) $note, 1, ',', '');
}

const MOIS = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet',
              'août', 'septembre', 'octobre', 'novembre', 'décembre'];
const MOIS_COURT = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.',
                    'août', 'sept.', 'oct.', 'nov.', 'déc.'];

/** « 20 octobre » */
function date_fr(?string $date): string {
  if (!$date) return '';
  $t = strtotime($date);
  return date('j', $t) . ' ' . MOIS[date('n', $t) - 1];
}

/** « Du 14 au 16 novembre » ou « Du 30 octobre au 2 novembre » */
function periode_longue(string $debut, string $fin): string {
  $d = strtotime($debut); $f = strtotime($fin);
  if (date('n Y', $d) === date('n Y', $f)) {
    return 'Du ' . date('j', $d) . ' au ' . date('j', $f) . ' ' . MOIS[date('n', $f) - 1];
  }
  return 'Du ' . date_fr($debut) . ' au ' . date_fr($fin);
}

/** « 14–16 nov. » */
function periode_courte(string $debut, string $fin): string {
  $d = strtotime($debut); $f = strtotime($fin);
  if (date('n Y', $d) === date('n Y', $f)) {
    return date('d', $d) . '–' . date('d', $f) . ' ' . MOIS_COURT[date('n', $f) - 1];
  }
  return date('d', $d) . ' ' . MOIS_COURT[date('n', $d) - 1] . ' – ' . date('d', $f) . ' ' . MOIS_COURT[date('n', $f) - 1];
}

/** Ville sans le code postal : « Massy, 91300 » -> « Massy » */
function ville_courte(?string $adresse): string {
  return trim(explode(',', $adresse ?? '')[0]);
}

function emoji_espece(?string $espece): string {
  return match (mb_strtolower($espece ?? '')) {
    'chien' => '🐕', 'chat' => '🐈', 'lapin' => '🐇', 'oiseau' => '🐦',
    'poisson' => '🐟', 'rongeur', 'hamster' => '🐹', default => '🐾',
  };
}

/** Badge coloré pour un statut d'annonce, de candidature ou d'avis. */
function badge(string $statut): string {
  [$texte, $classe] = match ($statut) {
    'ouverte'    => ['Ouverte', 'vert'],
    'pourvue'    => ['Pourvue', 'vert'],
    'terminee'   => ['Terminée', 'gris'],
    'en_attente' => ['En attente', 'vert'],
    'acceptee'   => ['Acceptée', 'vert'],
    'refusee'    => ['Refusée', 'rouge'],
    'a_verifier' => ['À vérifier', 'orange'],
    'publie'     => ['Publié', 'vert'],
    'masque'     => ['Masqué', 'rouge'],
    default      => [$statut, 'gris'],
  };
  return '<span class="badge badge-' . $classe . '">' . e($texte) . '</span>';
}

function etoiles(int $n): string {
  return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

// ---------- Session / droits ----------

function utilisateur(): ?array {
  return $_SESSION['user'] ?? null;
}

function exiger_connexion(): array {
  $u = utilisateur();
  if (!$u) {
    flash('Connectez-vous pour accéder à cette page.', 'erreur');
    redirect('connexion');
  }
  return $u;
}

/** Renvoie l'id_proprietaire de l'utilisateur connecté. */
function exiger_proprietaire(): int {
  $u = exiger_connexion();
  if (!$u['id_proprietaire']) {
    flash('Cette page est réservée aux propriétaires.', 'erreur');
    redirect('espace');
  }
  return $u['id_proprietaire'];
}

/** Renvoie l'id_gardien de l'utilisateur connecté. */
function exiger_gardien(): int {
  $u = exiger_connexion();
  if (!$u['id_gardien']) {
    flash('Cette page est réservée aux gardiens.', 'erreur');
    redirect('espace');
  }
  return $u['id_gardien'];
}

function exiger_admin(): void {
  $u = exiger_connexion();
  if (!$u['admin']) {
    flash('Accès réservé aux administrateurs.', 'erreur');
    redirect('accueil');
  }
}

// ---------- Formulaires ----------

function csrf_champ(): string {
  $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
  return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

function verifier_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST'
      || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Requête invalide.');
  }
}

/** Valeur POST nettoyée. */
function post(string $cle): string {
  return trim((string) ($_POST[$cle] ?? ''));
}
