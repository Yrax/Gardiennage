<?php
class Proprietaire {

  public static function trouver(int $id): ?array {
    return Database::run("SELECT * FROM Proprietaire WHERE id_proprietaire = ?", [$id])->fetch() ?: null;
  }

  /** Note moyenne reçue des gardiens : ['note' => 4.9, 'nb' => 12]. */
  public static function note(int $id): array {
    $r = Database::run(
      "SELECT COALESCE(AVG(nombre_etoiles), 0) AS note, COUNT(*) AS nb FROM Avis
       WHERE id_proprietaire_avis = ? AND auteur = 'gardien' AND statut = 'publie'", [$id]
    )->fetch();
    return ['note' => (float) $r['note'], 'nb' => (int) $r['nb']];
  }

  public static function recalculer(int $id): void {
    Database::run(
      "UPDATE Proprietaire p SET p.nombre_avis_proprietaire =
         (SELECT COUNT(*) FROM Avis WHERE id_proprietaire_avis = p.id_proprietaire
            AND auteur = 'gardien' AND statut = 'publie')
       WHERE p.id_proprietaire = ?", [$id]
    );
  }

  public static function animaux(int $id): array {
    return Database::run("SELECT * FROM Animal WHERE id_proprietaire_animal = ? ORDER BY nom_animal", [$id])->fetchAll();
  }

  /** Chiffres du tableau de bord propriétaire. */
  public static function stats(int $id): array {
    $annonces = Database::run(
      "SELECT SUM(statut = 'ouverte') AS ouvertes, SUM(statut = 'terminee') AS terminees
       FROM Annonce WHERE id_proprietaire_annonce = ?", [$id]
    )->fetch();
    $cand = Database::run(
      "SELECT COUNT(*) AS total, SUM(c.statut = 'en_attente') AS attente
       FROM Candidature c JOIN Annonce a ON a.id_annonce = c.id_annonce_candidature
       WHERE a.id_proprietaire_annonce = ?", [$id]
    )->fetch();
    $avis = Database::run(
      "SELECT COALESCE(AVG(nombre_etoiles), 0) AS note, COUNT(*) AS nb FROM Avis
       WHERE id_proprietaire_avis = ? AND auteur = 'gardien' AND statut = 'publie'", [$id]
    )->fetch();
    return [
      'ouvertes'     => (int) $annonces['ouvertes'],
      'terminees'    => (int) $annonces['terminees'],
      'candidatures' => (int) $cand['total'],
      'attente'      => (int) $cand['attente'],
      'note'         => (float) $avis['note'],
      'nb_avis'      => (int) $avis['nb'],
    ];
  }
}
