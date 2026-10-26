DROP DATABASE IF EXISTS petsitter;
CREATE DATABASE petsitter;
use petsitter;

CREATE TABLE Utilisateur (
    id_utilisateur INT,
    nom_utilisateur VARCHAR(25),
    prenom_utilisateur VARCHAR(25),
    mail VARCHAR(30),
    tel INT(10),
    adresse VARCHAR(30),
    description VARCHAR(30),
    nombre_avis INT,
    est_administrateur BOOLEAN,
    CONSTRAINT pk_utilisateur PRIMARY KEY (id_utilisateur)
);

CREATE TABLE Gardien (
    id_gardien INT,
    id_utilisateur_gardien INT,
    note_moyenne DECIMAL(4,2),
    nom_gardien VARCHAR(25),
    prenom_gardien VARCHAR(25),
    mail_gardien VARCHAR(25),
    tel_gardien INT(10),
    adresse_gardien VARCHAR(25),
    description_gardien VARCHAR(25),
    nombre_avis_gardien INT,
    est_administrateur_gardien BOOLEAN,
    CONSTRAINT pk_gardien PRIMARY KEY (id_gardien),
    CONSTRAINT fk_id_utilisateur_gardien
        FOREIGN KEY (id_utilisateur_gardien)
        REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE Proprietaire (
    id_proprietaire INT,
    id_utilisateur_proprietaire INT,
    nom_utilisateur_proprietaire VARCHAR(25),
    prenom_util_prop VARCHAR(25),
    mail_proprietaire VARCHAR(30),
    tel_proprietaire VARCHAR(20),
    adresse_proprietaire VARCHAR(30),
    description_proprietaire VARCHAR(30),
    nombre_avis_proprietaire INT,
    est_admin_prop BOOLEAN,
    CONSTRAINT pk_proprietaire PRIMARY KEY (id_proprietaire),
    CONSTRAINT fk_id_util_prop
        FOREIGN KEY (id_utilisateur_proprietaire)
        REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE Animal (
    id_animal INT,
    id_proprietaire_animal INT,
    nom_animal VARCHAR(25),
    espece VARCHAR(25),
    race VARCHAR(25),
    age INT,
    poids DECIMAL(6,2),
    besoin_particulier VARCHAR(30),
    CONSTRAINT pk_animal PRIMARY KEY (id_animal),
    CONSTRAINT fk_id_proprietaire
        FOREIGN KEY (id_proprietaire_animal)
        REFERENCES Proprietaire(id_proprietaire)
);

CREATE TABLE Annonce (
    id_annonce INT,
    id_gardien_annonce INT,
    id_proprietaire_annonce INT,
    nom_annonce VARCHAR(25),
    date_debut DATETIME,
    date_fin DATETIME,
    nombre_animaux INT,
    date_publication DATETIME,
    statut VARCHAR(30),
    CONSTRAINT pk_annonce PRIMARY KEY (id_annonce),
    CONSTRAINT fk_id_gardien_annonce
        FOREIGN KEY (id_gardien_annonce)
        REFERENCES Gardien(id_gardien),
    CONSTRAINT fk_id_proprietaire_annonce
        FOREIGN KEY (id_proprietaire_annonce)
        REFERENCES Proprietaire(id_proprietaire)
);

CREATE TABLE Avis (
    id_avis INT,
    id_annonce_avis INT,
    id_proprietaire_avis INT,
    id_gardien_avis INT,
    commentaire VARCHAR(30),
    nombre_etoiles INT,
    nombre_upvote INT,
    date_poste DATETIME,
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
