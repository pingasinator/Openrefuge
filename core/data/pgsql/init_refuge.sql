/*
*Création des tables 
*/
CREATE TABLE animal (
    animal INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    date_naissance DATE,
    animal_espece INT,
    animal_race INT,
    animal_sexe INT,
    personne INT
);

CREATE TABLE animal_race (
    animal_race INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    animal_espece INT
);

CREATE TABLE animal_sexe (
    animal_sexe INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE animal_espece(
    animal_espece INT NOT NULL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE personne (
    personne INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    prenom VARCHAR,
    adresse VARCHAR,
    ville INT,
    telephone VARCHAR,
    telephone_sec VARCHAR,
    mail VARCHAR,
    civilite INT
);

CREATE TABLE ville (
    ville INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    code_postal VARCHAR
);

CREATE TABLE civilite (
    civilite INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE soin (
    soin INT NOT NULL PRIMARY KEY,
    date_soin DATE,
    description TEXT,
    posologie VARCHAR,
    tarif FLOAT,
    veterinaire INT,
    animal INT,
    clinique INT,
    soin_type INT
);

CREATE TABLE soin_type (
    soin_type INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE veterinaire(
    veterinaire INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    prenom VARCHAR,
    telephone VARCHAR,
    clinique INT,
    civilite INT
);

CREATE TABLE clinique(
    clinique INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    adresse VARCHAR,
    ville INT,
    telephone VARCHAR
);

CREATE TABLE medicament (
    medicament INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    date_debut DATE,
    date_fin DATE,
    dose INT,
    frequence VARCHAR,
    unite_mesure INT,
    soin INT,
    animal INT
);

CREATE TABLE unite_mesure (
    unite_mesure INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE sejour (
    sejour INT NOT NULL PRIMARY KEY,
    date_entree DATE,
    date_sortie DATE,
    paye BOOLEAN,
    animal INT,
    provenance INT,
    hebergement INT,
    sejour_tarif INT
);

CREATE TABLE provenance (
    provenance INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE sejour_tarif (
   sejour_tarif INT NOT NULL PRIMARY KEY,
   libelle VARCHAR,
   prix FLOAT
);

CREATE TABLE hebergement (
    hebergement INT NOT NULL PRIMARY KEY,
    nom VARCHAR,
    adresse VARCHAR,
    ville INT,
    telephone VARCHAR,
    hebergement_type INT
);

CREATE TABLE hebergement_type (
    hebergement_type INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE facture (
    facture INT NOT NULL PRIMARY KEY,
    personne INT NOT NULL,
    date_creation DATE,
    numero_facture VARCHAR,
    etat VARCHAR
);

CREATE TABLE facture_soin (
    facture_soin INT NOT NULL PRIMARY KEY,
    facture INT,
    soin INT,
    date_soin DATE,
    tarif FLOAT,
    veterinaire INT,
    animal INT,
    clinique INT,
    soin_type INT
);

CREATE TABLE facture_sejour (
    facture_sejour INT NOT NULL PRIMARY KEY,
    facture INT,
    sejour INT,
    sejour_tarif INT,
    date_entree DATE,
    date_sortie DATE,
    paye BOOLEAN,
    animal INT,
    provenance INT,
    hebergement INT
);


/*
* Création des séquences 
*/

CREATE SEQUENCE animal_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE animal_race_seq
START WITH 176
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE animal_espece_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE personne_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE soin_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE veterinaire_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE clinique_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE medicament_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE sejour_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE sejour_tarif_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE hebergement_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE facture_seq 
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE facture_sejour_seq 
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE facture_soin_seq 
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE ville_seq 
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

/*
* Assignation des clés secondaires
*/

ALTER TABLE animal 
ADD CONSTRAINT fk_personne FOREIGN KEY (personne) REFERENCES personne(personne),
ADD CONSTRAINT fk_animal_race FOREIGN KEY (animal_race) REFERENCES animal_race(animal_race),
ADD CONSTRAINT fk_animal_espece FOREIGN KEY (animal_espece) REFERENCES animal_espece(animal_espece),
ADD CONSTRAINT fk_animal_sexe FOREIGN KEY (animal_sexe) REFERENCES animal_sexe(animal_sexe);

ALTER TABLE animal_race
ADD CONSTRAINT fk_animal_espece FOREIGN KEY (animal_espece) REFERENCES animal_espece(animal_espece);

ALTER TABLE personne
ADD CONSTRAINT fk_ville FOREIGN KEY (ville) REFERENCES ville(ville),
ADD CONSTRAINT fk_civilite FOREIGN KEY (civilite) REFERENCES civilite(civilite);

ALTER TABLE soin
ADD CONSTRAINT fk_veterinaire FOREIGN KEY (veterinaire) REFERENCES veterinaire(veterinaire),
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal),
ADD CONSTRAINT fk_clinique FOREIGN KEY (clinique) REFERENCES clinique(clinique),
ADD CONSTRAINT fk_soin_type FOREIGN KEY (soin_type) REFERENCES soin_type(soin_type);

ALTER TABLE veterinaire
ADD CONSTRAINT fk_clinique FOREIGN KEY (clinique) REFERENCES clinique(clinique),
ADD CONSTRAINT fk_civilite FOREIGN KEY (civilite) REFERENCES civilite(civilite);

ALTER TABLE clinique
ADD CONSTRAINT fk_ville FOREIGN KEY (ville) REFERENCES ville(ville);

ALTER TABLE medicament 
ADD CONSTRAINT fk_unite_mesure FOREIGN KEY (unite_mesure) REFERENCES unite_mesure(unite_mesure),
ADD CONSTRAINT fk_soin FOREIGN KEY (soin) REFERENCES soin(soin),
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal);

ALTER TABLE sejour
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal),
ADD CONSTRAINT fk_provenance FOREIGN KEY (provenance) REFERENCES provenance(provenance),
ADD CONSTRAINT fk_hebergement FOREIGN KEY (hebergement) REFERENCES hebergement(hebergement),
ADD CONSTRAINT fk_sejour_tarif FOREIGN KEY (sejour_tarif) REFERENCES sejour_tarif(sejour_tarif);

ALTER TABLE hebergement
ADD CONSTRAINT fk_ville FOREIGN KEY (ville) REFERENCES ville(ville),
ADD CONSTRAINT fk_hebergement_type FOREIGN KEY (hebergement_type) REFERENCES hebergement_type(hebergement_type);

ALTER TABLE facture
ADD CONSTRAINT fk_personne FOREIGN KEY (personne) REFERENCES personne(personne);

ALTER TABLE facture_soin
ADD CONSTRAINT fk_facture FOREIGN KEY (facture) REFERENCES facture(facture),
ADD CONSTRAINT fk_soin FOREIGN KEY (soin) REFERENCES soin(soin),
ADD CONSTRAINT fk_veterinaire FOREIGN KEY (veterinaire) REFERENCES veterinaire(veterinaire),
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal),
ADD CONSTRAINT fk_clinique FOREIGN KEY (clinique) REFERENCES clinique(clinique),
ADD CONSTRAINT fk_soin_type FOREIGN KEY (soin_type) REFERENCES soin_type(soin_type);

ALTER TABLE facture_sejour
ADD CONSTRAINT fk_facture FOREIGN KEY (facture) REFERENCES facture(facture),
ADD CONSTRAINT fk_sejour FOREIGN KEY (sejour) REFERENCES sejour(sejour),
ADD CONSTRAINT fk_sejour_tarif FOREIGN KEY (sejour_tarif) REFERENCES sejour_tarif(sejour_tarif),
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal),
ADD CONSTRAINT fk_provenance FOREIGN KEY (provenance) REFERENCES provenance(provenance),
ADD CONSTRAINT fk_hebergement FOREIGN KEY (hebergement) REFERENCES hebergement(hebergement);

ALTER SEQUENCE om_etat_seq
RESTART WITH 10;