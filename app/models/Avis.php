<?php
class Avis {

  private const SELECT = "
    SELECT v.*, a.nom_annonce,
           g.prenom_gardien, g.nom_gardien,
           p.prenom_util_prop, p.nom_utilisateur_proprietaire
    FROM Avis v
    JOIN Annonce a ON a.id_annonce = v.id_annonce_avis
    JOIN Gardien g ON g.id_gardien = v.id_gardien_avis
    JOIN Proprietaire p ON p.id_proprietaire = v.id_proprietaire_avis";

  /** $auteur : 'proprietaire' (note le gardien) ou 'gardien' (note le propriétaire). */
  public static function creer(array $annonce, string $auteur, int $etoiles, string $commentaire): void {
    Database::run(
      "INSERT INTO Avis (id_annonce_avis, id_proprietaire_avis, id_gardien_avis, auteur,
                         commentaire, nombre_etoiles, nombre_upvote, date_poste, statut)
       VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), 'a_verifier')",
      [$annonce['id_annonce'], $annonce['id_proprietaire_annonce'], $annonce['id_gardien_annonce'],
       $auteur, $commentaire, $etoiles]
    );
  }

  public static function dejaPoste(int $idAnnonce, string $auteur): bool {
    return (bool) Database::run("SELECT 1 FROM Avis WHERE id_annonce_avis = ? AND auteur = ?",
      [$idAnnonce, $auteur])->fetchColumn();
  }

  public static function trouver(int $id): ?array {
    return Database::run("SELECT * FROM Avis WHERE id_avis = ?", [$id])->fetch() ?: null;
  }

  public static function aModerer(): array {
    return Database::run(self::SELECT . " WHERE v.statut = 'a_verifier' ORDER BY v.date_poste DESC")->fetchAll();
  }

  public static function tous(): array {
    return Database::run(self::SELECT . " ORDER BY v.date_poste DESC")->fetchAll();
  }

  public static function compterAModerer(): int {
    return (int) Database::run("SELECT COUNT(*) FROM Avis WHERE statut = 'a_verifier'")->fetchColumn();
  }

  /** Publie ou masque un avis puis met à jour les notes. */
  public static function moderer(int $id, string $statut): void {
    $avis = self::trouver($id);
    if (!$avis) return;
    Database::run("UPDATE Avis SET statut = ? WHERE id_avis = ?", [$statut, $id]);
    if ($avis['auteur'] === 'proprietaire') {
      Gardien::recalculer((int) $avis['id_gardien_avis']);
    } else {
      Proprietaire::recalculer((int) $avis['id_proprietaire_avis']);
    }
  }

  /** Avis publiés reçus par un gardien. */
  public static function surGardien(int $idGardien): array {
    return Database::run(self::SELECT . " WHERE v.id_gardien_avis = ? AND v.auteur = 'proprietaire'
                                          AND v.statut = 'publie' ORDER BY v.date_poste DESC", [$idGardien])->fetchAll();
  }

  /** Avis publiés reçus par un propriétaire. */
  public static function surProprietaire(int $idProprietaire): array {
    return Database::run(self::SELECT . " WHERE v.id_proprietaire_avis = ? AND v.auteur = 'gardien'
                                          AND v.statut = 'publie' ORDER BY v.date_poste DESC", [$idProprietaire])->fetchAll();
  }
}
