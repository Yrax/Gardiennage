<?php
class Candidature {

  private const SELECT = "
    SELECT c.*, a.nom_annonce, a.ville, a.date_debut, a.date_fin, a.statut AS statut_annonce,
           a.id_proprietaire_annonce, a.id_gardien_annonce,
           g.prenom_gardien, g.nom_gardien, g.note_moyenne, g.nombre_avis_gardien,
           (SELECT COUNT(*) FROM Annonce t WHERE t.id_gardien_annonce = g.id_gardien AND t.statut = 'terminee') AS gardes
    FROM Candidature c
    JOIN Annonce a ON a.id_annonce = c.id_annonce_candidature
    JOIN Gardien g ON g.id_gardien = c.id_gardien_candidature";

  public static function creer(int $idAnnonce, int $idGardien, string $message): void {
    Database::run(
      "INSERT INTO Candidature (id_annonce_candidature, id_gardien_candidature, message, statut, date_envoi)
       VALUES (?, ?, ?, 'en_attente', NOW())",
      [$idAnnonce, $idGardien, $message]
    );
  }

  public static function existe(int $idAnnonce, int $idGardien): ?array {
    return Database::run(
      "SELECT * FROM Candidature WHERE id_annonce_candidature = ? AND id_gardien_candidature = ?",
      [$idAnnonce, $idGardien]
    )->fetch() ?: null;
  }

  public static function trouver(int $id): ?array {
    return Database::run(self::SELECT . " WHERE c.id_candidature = ?", [$id])->fetch() ?: null;
  }

  public static function parGardien(int $idGardien, int $limite = 0): array {
    $sql = self::SELECT . " WHERE c.id_gardien_candidature = ? ORDER BY c.date_envoi DESC";
    if ($limite) $sql .= " LIMIT " . $limite;
    return Database::run($sql, [$idGardien])->fetchAll();
  }

  /** Candidatures reçues sur les annonces d'un propriétaire. */
  public static function recues(int $idProprietaire, ?string $statut = null, ?int $idAnnonce = null): array {
    $sql = self::SELECT . " WHERE a.id_proprietaire_annonce = ?";
    $params = [$idProprietaire];
    if ($statut) { $sql .= " AND c.statut = ?"; $params[] = $statut; }
    if ($idAnnonce) { $sql .= " AND a.id_annonce = ?"; $params[] = $idAnnonce; }
    $sql .= " ORDER BY c.statut = 'en_attente' DESC, c.date_envoi DESC";
    return Database::run($sql, $params)->fetchAll();
  }

  /**
   * Accepte ou refuse une candidature.
   * Accepter attribue la garde au gardien et refuse les autres candidatures.
   */
  public static function decider(array $c, bool $accepter): void {
    $db = Database::get();
    $db->beginTransaction();
    try {
      if ($accepter) {
        Database::run("UPDATE Candidature SET statut = 'acceptee' WHERE id_candidature = ?", [$c['id_candidature']]);
        Database::run(
          "UPDATE Candidature SET statut = 'refusee'
           WHERE id_annonce_candidature = ? AND id_candidature <> ? AND statut = 'en_attente'",
          [$c['id_annonce_candidature'], $c['id_candidature']]
        );
        Database::run("UPDATE Annonce SET statut = 'pourvue', id_gardien_annonce = ? WHERE id_annonce = ?",
          [$c['id_gardien_candidature'], $c['id_annonce_candidature']]);
      } else {
        Database::run("UPDATE Candidature SET statut = 'refusee' WHERE id_candidature = ?", [$c['id_candidature']]);
      }
      $db->commit();
    } catch (Throwable $ex) {
      $db->rollBack();
      throw $ex;
    }
  }
}
