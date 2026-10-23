

class Annonce {
  public function __construct(
    public int $id,
    public string $titre,
    public string $statut,
  ) {}

  public static function toutesOuvertes(): array {
    $stmt = Database::get()->query(
      "SELECT * FROM annonces
       WHERE statut='ouverte'"
    );
    return $stmt->fetchAll();
  }
}