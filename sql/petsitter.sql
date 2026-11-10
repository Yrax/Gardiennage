DROP DATABASE IF EXISTS petsitter;
CREATE DATABASE petsitter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE petsitter;

-- ---------------------------------------------------------------
-- Schéma d'origine, avec quelques ajouts nécessaires au site :
--   * AUTO_INCREMENT sur les identifiants
--   * Utilisateur.mot_de_passe (connexion)
--   * colonnes texte élargies (mails, descriptions, titres)
--   * tel en VARCHAR (garde le 0 initial des numéros)
--   * Annonce : ville, type_garde, description, a_prevoir
--   * Avis : auteur (qui note qui) + statut (modération)
--   * nouvelle table Candidature
-- ---------------------------------------------------------------

CREATE TABLE Utilisateur (
    id_utilisateur INT AUTO_INCREMENT,
    nom_utilisateur VARCHAR(25),
    prenom_utilisateur VARCHAR(25),
    mail VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    tel VARCHAR(20),
    adresse VARCHAR(100),
    description VARCHAR(255),
    nombre_avis INT DEFAULT 0,
    est_administrateur BOOLEAN DEFAULT FALSE,
    CONSTRAINT pk_utilisateur PRIMARY KEY (id_utilisateur)
);

CREATE TABLE Gardien (
    id_gardien INT AUTO_INCREMENT,
    id_utilisateur_gardien INT,
    note_moyenne DECIMAL(4,2) DEFAULT 0,
    nom_gardien VARCHAR(25),
    prenom_gardien VARCHAR(25),
    mail_gardien VARCHAR(100),
    tel_gardien VARCHAR(20),
    adresse_gardien VARCHAR(100),
    description_gardien VARCHAR(255),
    nombre_avis_gardien INT DEFAULT 0,
    est_administrateur_gardien BOOLEAN DEFAULT FALSE,
    CONSTRAINT pk_gardien PRIMARY KEY (id_gardien),
    CONSTRAINT fk_id_utilisateur_gardien
        FOREIGN KEY (id_utilisateur_gardien)
        REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE Proprietaire (
    id_proprietaire INT AUTO_INCREMENT,
    id_utilisateur_proprietaire INT,
    nom_utilisateur_proprietaire VARCHAR(25),
    prenom_util_prop VARCHAR(25),
    mail_proprietaire VARCHAR(100),
    tel_proprietaire VARCHAR(20),
    adresse_proprietaire VARCHAR(100),
    description_proprietaire VARCHAR(255),
    nombre_avis_proprietaire INT DEFAULT 0,
    est_admin_prop BOOLEAN DEFAULT FALSE,
    CONSTRAINT pk_proprietaire PRIMARY KEY (id_proprietaire),
    CONSTRAINT fk_id_util_prop
        FOREIGN KEY (id_utilisateur_proprietaire)
        REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE Animal (
    id_animal INT AUTO_INCREMENT,
    id_proprietaire_animal INT,
    nom_animal VARCHAR(25),
    espece VARCHAR(25),
    race VARCHAR(25),
    age INT,
    poids DECIMAL(6,2),
    besoin_particulier VARCHAR(255),
    CONSTRAINT pk_animal PRIMARY KEY (id_animal),
    CONSTRAINT fk_id_proprietaire
        FOREIGN KEY (id_proprietaire_animal)
        REFERENCES Proprietaire(id_proprietaire)
);

-- statut : 'ouverte' | 'pourvue' | 'terminee'
CREATE TABLE Annonce (
    id_annonce INT AUTO_INCREMENT,
    id_gardien_annonce INT NULL,
    id_proprietaire_annonce INT,
    nom_annonce VARCHAR(100),
    ville VARCHAR(60),
    type_garde VARCHAR(30),
    description TEXT,
    a_prevoir TEXT,
    date_debut DATETIME,
    date_fin DATETIME,
    nombre_animaux INT,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(30) DEFAULT 'ouverte',
    CONSTRAINT pk_annonce PRIMARY KEY (id_annonce),
    CONSTRAINT fk_id_gardien_annonce
        FOREIGN KEY (id_gardien_annonce)
        REFERENCES Gardien(id_gardien),
    CONSTRAINT fk_id_proprietaire_annonce
        FOREIGN KEY (id_proprietaire_annonce)
        REFERENCES Proprietaire(id_proprietaire)
);

-- auteur : 'proprietaire' (avis sur le gardien) | 'gardien' (avis sur le propriétaire)
-- statut : 'a_verifier' | 'publie' | 'masque'
CREATE TABLE Avis (
    id_avis INT AUTO_INCREMENT,
    id_annonce_avis INT,
    id_proprietaire_avis INT,
    id_gardien_avis INT,
    auteur VARCHAR(20) NOT NULL,
    commentaire VARCHAR(255),
    nombre_etoiles INT,
    nombre_upvote INT DEFAULT 0,
    date_poste DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'a_verifier',
    CONSTRAINT pk_avis PRIMARY KEY (id_avis),
    CONSTRAINT fk_id_annonce_avis
        FOREIGN KEY (id_annonce_avis)
        REFERENCES Annonce(id_annonce),
    CONSTRAINT fk_id_proprietaire_avis
        FOREIGN KEY (id_proprietaire_avis)
        REFERENCES Proprietaire(id_proprietaire),
    CONSTRAINT fk_id_gardien_avis
        FOREIGN KEY (id_gardien_avis)
        REFERENCES Gardien(id_gardien)
);

CREATE TABLE Est_Inscrit (
    id_animal_est_inscrit INT,
    id_annonce_est_inscrit INT,
    CONSTRAINT pk_est_inscrit PRIMARY KEY (
        id_animal_est_inscrit,
        id_annonce_est_inscrit
    ),
    CONSTRAINT fk_id_animal_est_inscrit
        FOREIGN KEY (id_animal_est_inscrit)
        REFERENCES Animal(id_animal),
    CONSTRAINT fk_id_annonce_est_inscrit
        FOREIGN KEY (id_annonce_est_inscrit)
        REFERENCES Annonce(id_annonce)
);

-- statut : 'en_attente' | 'acceptee' | 'refusee'
CREATE TABLE Candidature (
    id_candidature INT AUTO_INCREMENT,
    id_annonce_candidature INT NOT NULL,
    id_gardien_candidature INT NOT NULL,
    message TEXT,
    statut VARCHAR(20) DEFAULT 'en_attente',
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_candidature PRIMARY KEY (id_candidature),
    CONSTRAINT uq_candidature UNIQUE (id_annonce_candidature, id_gardien_candidature),
    CONSTRAINT fk_id_annonce_candidature
        FOREIGN KEY (id_annonce_candidature)
        REFERENCES Annonce(id_annonce),
    CONSTRAINT fk_id_gardien_candidature
        FOREIGN KEY (id_gardien_candidature)
        REFERENCES Gardien(id_gardien)
);

-- ---------------------------------------------------------------
-- Données de démonstration (mot de passe de tous les comptes : password123)
-- ---------------------------------------------------------------
SET @mdp = '$2y$10$ZMD7lD6VYCeinF4gyYHZvussUEnVn258uGHSRrz2Ge4qkyU2nB.0S';

INSERT INTO Utilisateur (id_utilisateur, nom_utilisateur, prenom_utilisateur, mail, mot_de_passe, tel, adresse, description, nombre_avis, est_administrateur) VALUES
(1, 'Bernard',  'Marc',     'marc@petsitter.fr',      @mdp, '0612345678', 'Massy, 91300',     'Propriétaire de Nala, Minou et Rocky.', 1, FALSE),
(2, 'Lefèvre',  'Alexandre','alexandre@petsitter.fr', @mdp, '0623456789', 'Massy, 91300',     'J''ai grandi avec des chiens, je connais bien leurs besoins.', 1, FALSE),
(3, 'Dubois',   'Camille',  'camille@petsitter.fr',   @mdp, '0634567890', 'Massy, 91300',     'Douce et attentive, expérience avec les chats.', 0, FALSE),
(4, 'Roux',     'Thomas',   'thomas@petsitter.fr',    @mdp, '0645678901', 'Antony, 92160',    'Promenades et jeux pour chiens actifs.', 0, FALSE),
(5, 'Martin',   'Sarah',    'sarah@petsitter.fr',     @mdp, '0656789012', 'Palaiseau, 91120', 'Un environnement calme et plein d''attention.', 0, FALSE),
(6, 'Admin',    'Équipe',   'admin@petsitter.fr',     @mdp, NULL,         'Paris',            'Administrateur du site.', 0, TRUE),
(7, 'Petit',    'Julie',    'julie@petsitter.fr',     @mdp, '0667890123', 'Antony, 92160',    'Maman de Pistache et Filou.', 0, FALSE);

INSERT INTO Proprietaire (id_proprietaire, id_utilisateur_proprietaire, nom_utilisateur_proprietaire, prenom_util_prop, mail_proprietaire, tel_proprietaire, adresse_proprietaire, description_proprietaire, nombre_avis_proprietaire, est_admin_prop) VALUES
(1, 1, 'Bernard', 'Marc',  'marc@petsitter.fr',  '0612345678', 'Massy, 91300',  'Propriétaire de Nala, Minou et Rocky.', 1, FALSE),
(2, 7, 'Petit',   'Julie', 'julie@petsitter.fr', '0667890123', 'Antony, 92160', 'Maman de Pistache et Filou.', 0, FALSE);

INSERT INTO Gardien (id_gardien, id_utilisateur_gardien, note_moyenne, nom_gardien, prenom_gardien, mail_gardien, tel_gardien, adresse_gardien, description_gardien, nombre_avis_gardien, est_administrateur_gardien) VALUES
(1, 2, 5.00, 'Lefèvre', 'Alexandre', 'alexandre@petsitter.fr', '0623456789', 'Massy, 91300',     'J''ai grandi avec des chiens, je connais bien leurs besoins.', 1, FALSE),
(2, 3, 0,    'Dubois',  'Camille',   'camille@petsitter.fr',   '0634567890', 'Massy, 91300',     'Douce et attentive, expérience avec les chats.', 0, FALSE),
(3, 4, 0,    'Roux',    'Thomas',    'thomas@petsitter.fr',    '0645678901', 'Antony, 92160',    'Promenades et jeux pour chiens actifs.', 0, FALSE),
(4, 5, 0,    'Martin',  'Sarah',     'sarah@petsitter.fr',     '0656789012', 'Palaiseau, 91120', 'Un environnement calme et plein d''attention.', 0, FALSE);

INSERT INTO Animal (id_animal, id_proprietaire_animal, nom_animal, espece, race, age, poids, besoin_particulier) VALUES
(1, 1, 'Nala',     'Chien', 'Shiba Inu',  3, 9.50, 'Deux sorties par jour'),
(2, 1, 'Minou',    'Chat',  'Européen',   5, 4.20, NULL),
(3, 1, 'Rocky',    'Chien', 'Beagle',     6, 12.00, NULL),
(4, 2, 'Pistache', 'Chat',  'Siamois',    2, 3.80, NULL),
(5, 2, 'Filou',    'Lapin', 'Bélier',     1, 1.90, 'Foin frais tous les jours');

INSERT INTO Annonce (id_annonce, id_gardien_annonce, id_proprietaire_annonce, nom_annonce, ville, type_garde, description, a_prevoir, date_debut, date_fin, nombre_animaux, date_publication, statut) VALUES
(1, NULL, 1, 'Garde de Nala pendant un week-end', 'Massy, 91300', 'Chez le propriétaire',
 'Nala est une chienne sociable de 3 ans. Elle aime les promenades tranquilles, les jeux et la présence humaine. Elle a besoin de deux sorties par jour et d''un peu de compagnie.',
 'Deux promenades quotidiennes\nRepas matin et soir\nCarnet de santé disponible',
 '2026-11-14 09:00:00', '2026-11-16 18:00:00', 1, '2026-10-18 10:00:00', 'ouverte'),
(2, 3, 1, 'Visites pour Minou', 'Antony, 92160', 'Visites',
 'Minou est un chat indépendant. Une visite par jour suffit pour le nourrir et changer sa litière.',
 'Une visite par jour\nCroquettes dans le placard',
 '2026-11-22 09:00:00', '2026-11-25 18:00:00', 1, '2026-10-10 10:00:00', 'pourvue'),
(3, 1, 1, 'Garde de Rocky', 'Massy, 91300', 'Chez le propriétaire',
 'Rocky est un beagle joueur qui adore courir.',
 'Trois promenades par jour',
 '2026-10-02 09:00:00', '2026-10-04 18:00:00', 1, '2026-09-20 10:00:00', 'terminee'),
(4, 2, 2, 'Visites pour Pistache', 'Antony, 92160', 'Visites',
 'Pistache est une jeune chatte câline.',
 'Deux visites par jour',
 '2026-09-10 09:00:00', '2026-09-14 18:00:00', 1, '2026-09-01 10:00:00', 'terminee'),
(5, NULL, 2, 'Garde de Filou pour les vacances', 'Antony, 92160', 'À domicile',
 'Filou est un lapin calme. Il vit dans un grand enclos que je vous apporterai.',
 'Foin frais tous les jours\nLégumes le soir',
 '2026-12-20 09:00:00', '2026-12-27 18:00:00', 1, '2026-10-22 10:00:00', 'ouverte');

INSERT INTO Est_Inscrit (id_animal_est_inscrit, id_annonce_est_inscrit) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5);

INSERT INTO Candidature (id_annonce_candidature, id_gardien_candidature, message, statut, date_envoi) VALUES
(1, 1, 'J''ai l''habitude de garder des chiens, Nala sera entre de bonnes mains !', 'en_attente', '2026-10-20 14:00:00'),
(1, 2, 'Bonjour, je serais ravie de m''occuper de Nala ce week-end.', 'en_attente', '2026-10-21 09:30:00'),
(2, 3, 'Je peux passer chaque jour en fin de matinée.', 'acceptee', '2026-10-11 18:00:00'),
(2, 4, 'Disponible toute la semaine.', 'refusee', '2026-10-12 08:00:00'),
(3, 1, 'Rocky a l''air adorable, je suis disponible.', 'acceptee', '2026-09-21 11:00:00'),
(4, 2, 'J''adore les chats siamois !', 'acceptee', '2026-09-02 15:00:00');

INSERT INTO Avis (id_annonce_avis, id_proprietaire_avis, id_gardien_avis, auteur, commentaire, nombre_etoiles, date_poste, statut) VALUES
(3, 1, 1, 'proprietaire', 'Rocky est revenu ravi, gardien très fiable.', 5, '2026-10-05 10:00:00', 'publie'),
(3, 1, 1, 'gardien',      'Propriétaire très clair et accueillant.',      5, '2026-10-05 12:00:00', 'publie'),
(4, 2, 2, 'proprietaire', 'Très bonne expérience, communication facile.', 5, '2026-10-22 09:00:00', 'a_verifier'),
(4, 2, 2, 'gardien',      'Organisation compliquée, informations manquantes.', 3, '2026-10-21 18:00:00', 'a_verifier');
