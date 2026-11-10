<?php
class Gardien {

  public static function trouver(int $id): ?array {
    return Database::run("SELECT * FROM Gardien WHERE id_gardien = ?", [$id])->fetch() ?: null;
  }

  /** Gardiens mis en avant sur l'accueil (mieux notés d'abord). */
  public static function enAvant(int $limite = 3, string $ville = '', string $espece = ''): array {
    $sql = "SELECT g.*,
              (SELECT COUNT(*) FROM Annonce a WHERE a.id_gardien_annonce = g.id_gardien AND a.statut = 'terminee') AS gardes
            FROM Gardien g WHERE 1 = 1";
    $params = [];
    if ($ville !== '') {
      $sql .= " AND g.adresse_gardien LIKE ?";
      $params[] = "%$ville%";
    }
    if ($espece !== '') {
      // On cherche l'espèce sans le « s » final dans la présentation du gardien.
      $sql .= " AND g.description_gardien LIKE ?";
      $params[] = '%' . rtrim($espece, 'sS') . '%';
    }
    $sql .= " ORDER BY g.note_moyenne DESC, g.nombre_avis_gardien DESC LIMIT " . (int) $limite;
    return Database::run($sql, $params)->fetchAll();
  }

  /** Recalcule la note moyenne à partir des avis publiés. */
  public static function recalculer(int $id): void {
    Database::run(
      "UPDATE Gardien g SET
         g.note_moyenne = (SELECT COALESCE(AVG(nombre_etoiles), 0) FROM Avis
                           WHERE id_gardien_avis = g.id_gardien AND auteur = 'proprietaire' AND statut = 'publie'),
         g.nombre_avis_gardien = (SELECT COUNT(*) FROM Avis
                           WHERE id_gardien_avis = g.id_gardien AND auteur = 'proprietaire' AND statut = 'publie')
       WHERE g.id_gardien = ?", [$id]
    );
    Database::run(
      "UPDATE Utilisateur u JOIN Gardien g ON g.id_utilisateur_gardien = u.id_utilisateur
       SET u.nombre_avis = g.nombre_avis_gardien WHERE g.id_gardien = ?", [$id]
    );
  }

  /** Chiffres du tableau de bord gardien. */
  public static function stats(int $id): array {
    $r = Database::run(
      "SELECT
         SUM(c.statut = 'en_attente') AS attente,
         SUM(c.statut = 'acceptee' AND a.statut <> 'terminee') AS acceptees,
         SUM(c.statut = 'acceptee' AND a.statut = 'terminee') AS terminees
       FROM Candidature c JOIN Annonce a ON a.id_annonce = c.id_annonce_candidature
       WHERE c.id_gardien_candidature = ?", [$id]
    )->fetch();
    return array_map('intval', $r);
  }
}
