<?php
class Utilisateur {

  public static function parMail(string $mail): ?array {
    return Database::run("SELECT * FROM Utilisateur WHERE mail = ?", [$mail])->fetch() ?: null;
  }

  public static function trouver(int $id): ?array {
    return Database::run("SELECT * FROM Utilisateur WHERE id_utilisateur = ?", [$id])->fetch() ?: null;
  }

  /**
   * Crée l'utilisateur et sa fiche Gardien et/ou Propriétaire.
   * $roles : tableau contenant 'gardien' et/ou 'proprietaire'.
   */
  public static function creer(array $d, array $roles): int {
    $db = Database::get();
    $db->beginTransaction();
    try {
      Database::run(
        "INSERT INTO Utilisateur (nom_utilisateur, prenom_utilisateur, mail, mot_de_passe, tel, adresse, description)
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$d['nom'], $d['prenom'], $d['mail'], password_hash($d['password'], PASSWORD_DEFAULT),
         $d['tel'] ?: null, $d['adresse'], $d['description'] ?: null]
      );
      $id = (int) $db->lastInsertId();

      if (in_array('gardien', $roles, true)) {
        Database::run(
          "INSERT INTO Gardien (id_utilisateur_gardien, nom_gardien, prenom_gardien, mail_gardien,
                                tel_gardien, adresse_gardien, description_gardien)
           VALUES (?, ?, ?, ?, ?, ?, ?)",
          [$id, $d['nom'], $d['prenom'], $d['mail'], $d['tel'] ?: null, $d['adresse'], $d['description'] ?: null]
        );
      }
      if (in_array('proprietaire', $roles, true)) {
        Database::run(
          "INSERT INTO Proprietaire (id_utilisateur_proprietaire, nom_utilisateur_proprietaire, prenom_util_prop,
                                     mail_proprietaire, tel_proprietaire, adresse_proprietaire, description_proprietaire)
           VALUES (?, ?, ?, ?, ?, ?, ?)",
          [$id, $d['nom'], $d['prenom'], $d['mail'], $d['tel'] ?: null, $d['adresse'], $d['description'] ?: null]
        );
      }
      $db->commit();
      return $id;
    } catch (Throwable $ex) {
      $db->rollBack();
      throw $ex;
    }
  }

  /** Données gardées en session après connexion. */
  public static function session(int $id): array {
    $u = self::trouver($id);
    $g = Database::run("SELECT id_gardien FROM Gardien WHERE id_utilisateur_gardien = ?", [$id])->fetchColumn();
    $p = Database::run("SELECT id_proprietaire FROM Proprietaire WHERE id_utilisateur_proprietaire = ?", [$id])->fetchColumn();
    return [
      'id'              => $id,
      'prenom'          => $u['prenom_utilisateur'],
      'nom'             => $u['nom_utilisateur'],
      'admin'           => (bool) $u['est_administrateur'],
      'id_gardien'      => $g ? (int) $g : null,
      'id_proprietaire' => $p ? (int) $p : null,
    ];
  }

  /** Met à jour le profil et recopie les infos dans les tables Gardien / Proprietaire. */
  public static function majProfil(int $id, string $tel, string $adresse, string $description): void {
    $tel = $tel ?: null;
    Database::run("UPDATE Utilisateur SET tel = ?, adresse = ?, description = ? WHERE id_utilisateur = ?",
      [$tel, $adresse, $description, $id]);
    Database::run("UPDATE Gardien SET tel_gardien = ?, adresse_gardien = ?, description_gardien = ?
                   WHERE id_utilisateur_gardien = ?", [$tel, $adresse, $description, $id]);
    Database::run("UPDATE Proprietaire SET tel_proprietaire = ?, adresse_proprietaire = ?, description_proprietaire = ?
                   WHERE id_utilisateur_proprietaire = ?", [$tel, $adresse, $description, $id]);
  }

  /** Liste pour l'administration, avec les rôles. */
  public static function tous(): array {
    return Database::run(
      "SELECT u.*, g.id_gardien, p.id_proprietaire
       FROM Utilisateur u
       LEFT JOIN Gardien g ON g.id_utilisateur_gardien = u.id_utilisateur
       LEFT JOIN Proprietaire p ON p.id_utilisateur_proprietaire = u.id_utilisateur
       ORDER BY u.id_utilisateur DESC"
    )->fetchAll();
  }

  public static function compter(): int {
    return (int) Database::run("SELECT COUNT(*) FROM Utilisateur")->fetchColumn();
  }
}
