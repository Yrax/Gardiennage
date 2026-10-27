<?php
class Annonce {

  /** Colonnes communes : annonce + premier animal + propriétaire. */
  private const SELECT = "
    SELECT a.*,
      p.prenom_util_prop, p.nom_utilisateur_proprietaire, p.id_utilisateur_proprietaire,
      an.nom_animal, an.espece, an.race, an.age,
      (SELECT COUNT(*) FROM Candidature c WHERE c.id_annonce_candidature = a.id_annonce) AS nb_candidatures
    FROM Annonce a
    JOIN Proprietaire p ON p.id_proprietaire = a.id_proprietaire_annonce
    LEFT JOIN Animal an ON an.id_animal = (
      SELECT MIN(e.id_animal_est_inscrit) FROM Est_Inscrit e WHERE e.id_annonce_est_inscrit = a.id_annonce)";

  /** Annonces ouvertes, avec filtres facultatifs (ville, espèce). */
  public static function toutesOuvertes(string $ville = '', string $espece = ''): array {
    $sql = self::SELECT . " WHERE a.statut = 'ouverte'";
    $params = [];
    if ($ville !== '') {
      $sql .= " AND a.ville LIKE ?";
      $params[] = "%$ville%";
    }
    if ($espece !== '') {
      $sql .= " AND an.espece LIKE ?";
      $params[] = "%$espece%";
    }
    $sql .= " ORDER BY a.date_debut";
    return Database::run($sql, $params)->fetchAll();
  }

  public static function trouver(int $id): ?array {
    return Database::run(self::SELECT . " WHERE a.id_annonce = ?", [$id])->fetch() ?: null;
  }

  public static function animaux(int $id): array {
    return Database::run(
      "SELECT an.* FROM Animal an
       JOIN Est_Inscrit e ON e.id_animal_est_inscrit = an.id_animal
       WHERE e.id_annonce_est_inscrit = ?", [$id]
    )->fetchAll();
  }

  /**
   * Crée une annonce. $animal = id d'un animal existant (int)
   * ou tableau décrivant un nouvel animal.
   */
  public static function creer(int $idProprietaire, array $d, int|array $animal): int {
    $db = Database::get();
    $db->beginTransaction();
    try {
      if (is_array($animal)) {
        Database::run(
          "INSERT INTO Animal (id_proprietaire_animal, nom_animal, espece, race, age, besoin_particulier)
           VALUES (?, ?, ?, ?, ?, ?)",
          [$idProprietaire, $animal['nom'], $animal['espece'], $animal['race'] ?: null,
           $animal['age'] !== '' ? (int) $animal['age'] : null, $animal['besoin'] ?: null]
        );
        $idAnimal = (int) $db->lastInsertId();
      } else {
        $idAnimal = $animal;
      }

      Database::run(
        "INSERT INTO Annonce (id_proprietaire_annonce, nom_annonce, ville, type_garde, description, a_prevoir,
                              date_debut, date_fin, nombre_animaux, date_publication, statut)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), 'ouverte')",
        [$idProprietaire, $d['titre'], $d['ville'], $d['type_garde'], $d['description'],
         $d['a_prevoir'] ?: null, $d['date_debut'], $d['date_fin']]
      );
      $idAnnonce = (int) $db->lastInsertId();

      Database::run("INSERT INTO Est_Inscrit (id_animal_est_inscrit, id_annonce_est_inscrit) VALUES (?, ?)",
        [$idAnimal, $idAnnonce]);

      $db->commit();
      return $idAnnonce;
    } catch (Throwable $ex) {
      $db->rollBack();
      throw $ex;
    }
  }

  /** Annonces d'un propriétaire (filtre de statut facultatif). */
  public static function parProprietaire(int $id, ?array $statuts = null, int $limite = 0): array {
    $sql = self::SELECT . " WHERE a.id_proprietaire_annonce = ?";
    $params = [$id];
    if ($statuts) {
      $sql .= " AND a.statut IN (" . implode(',', array_fill(0, count($statuts), '?')) . ")";
      $params = array_merge($params, $statuts);
    }
    $sql .= " ORDER BY a.date_debut DESC";
    if ($limite) $sql .= " LIMIT " . $limite;
    return Database::run($sql, $params)->fetchAll();
  }

  /** Gardes attribuées à un gardien. */
  public static function parGardien(int $idGardien): array {
    return Database::run(self::SELECT . " WHERE a.id_gardien_annonce = ? ORDER BY a.date_debut DESC", [$idGardien])->fetchAll();
  }

  /** Le propriétaire déclare la garde terminée (permet ensuite de laisser un avis). */
  public static function terminer(int $id, int $idProprietaire): bool {
    return Database::run(
      "UPDATE Annonce SET statut = 'terminee'
       WHERE id_annonce = ? AND id_proprietaire_annonce = ? AND statut = 'pourvue'",
      [$id, $idProprietaire]
    )->rowCount() > 0;
  }

  public static function toutes(): array {
    return Database::run(self::SELECT . " ORDER BY a.date_publication DESC")->fetchAll();
  }

  public static function compter(string $statut): int {
    return (int) Database::run("SELECT COUNT(*) FROM Annonce WHERE statut = ?", [$statut])->fetchColumn();
  }
}
