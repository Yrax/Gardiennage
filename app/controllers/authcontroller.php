// À l'inscription
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

// À la connexion
if (password_verify($_POST['password'], $utilisateur->password)) {
  $_SESSION['user_id'] = $utilisateur->id;
}