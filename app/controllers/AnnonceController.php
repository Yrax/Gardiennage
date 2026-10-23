

class AnnonceController {

  public function liste(): void {
    $annonces = Annonce::toutesOuvertes();
    require __DIR__.'/../views/annonces/liste.php';
  }
}