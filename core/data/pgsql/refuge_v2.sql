CREATE TABLE rue(
    rue INT NOT NULL PRIMARY KEY,
    nom VARCHAR
);

CREATE TABLE animal_sortie(
    animal_sortie INT NOT NULL PRIMARY KEY,
    date_sortie DATE,
    animal INT NOT NULL,
    cause_mort INT
);

CREATE TABLE animal_entree(
    animal_entree INT NOT NULL PRIMARY KEY,
    date_entree DATE,
    animal INT NOT NULL
);

CREATE TABLE cause_mort(
    cause_mort INT NOT NULL PRIMARY KEY,
    libelle VARCHAR
);

CREATE TABLE medicament_suivi(
    medicament_suivi INT NOT NULL PRIMARY KEY,
    medicament INT NOT NULL,
    animal INT NOT NULL,
    date DATE NOT NULL,
    heure TIME NOT NULL
);

CREATE SEQUENCE rue_seq
START WITH 613
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE medicament_suivi_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE animal_sortie_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE animal_entree_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

CREATE SEQUENCE cause_mort_seq
START WITH 5
INCREMENT BY 1
NO MINVALUE 
NO MAXVALUE 
NO CYCLE
CACHE 1;

ALTER TABLE animal
ADD COLUMN num_identification VARCHAR UNIQUE,
ADD COLUMN animal_sortie INT,
ADD CONSTRAINT fk_animal_sortie FOREIGN KEY (animal_sortie) REFERENCES animal_sortie(animal_sortie),
ADD COLUMN description TEXT;

ALTER TABLE animal_sortie
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal),
ADD CONSTRAINT fk_cause_mort FOREIGN KEY (cause_mort) REFERENCES cause_mort(cause_mort);

ALTER TABLE animal_entree
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal);

ALTER TABLE medicament_suivi
ADD CONSTRAINT fk_animal FOREIGN KEY (animal) REFERENCES animal(animal),
ADD CONSTRAINT fk_medicament FOREIGN KEY (medicament) REFERENCES medicament(medicament);

ALTER TABLE provenance
DROP COLUMN libelle,
ADD COLUMN num_rue INT,
ADD COLUMN rue INT,
ADD CONSTRAINT fk_rue FOREIGN KEY (rue) REFERENCES rue(rue);

ALTER TABLE personne
DROP COLUMN adresse,
ADD COLUMN num_rue INT,
ADD COLUMN rue INT,
ADD CONSTRAINT fk_rue FOREIGN KEY (rue) REFERENCES rue(rue);

ALTER TABLE medicament
DROP COLUMN unite_mesure,
ALTER COLUMN dose TYPE VARCHAR;

ALTER TABLE soin
DROP COLUMN tarif;

ALTER TABLE clinique
ADD COLUMN num_rue INT,
ADD COLUMN rue INT,
DROP COLUMN adresse,
ADD CONSTRAINT fk_rue FOREIGN KEY (rue) REFERENCES rue(rue);

ALTER SEQUENCE animal_race_seq
RESTART WITH 176;

ALTER SEQUENCE om_droit_seq
RESTART WITH 154;

ALTER SEQUENCE om_permission_seq
RESTART WITH 138;

DROP TABLE unite_mesure;

DROP TABLE facture_soin;
DROP TABLE facture_sejour;

DROP TABLE sejour;
DROP TABLE sejour_tarif;
DROP TABLE hebergement;
DROP TABLE hebergement_type;

DROP SEQUENCE sejour_seq;
DROP SEQUENCE sejour_tarif_seq;
DROP SEQUENCE hebergement_seq;
DROP SEQUENCE facture_soin_seq;
DROP SEQUENCE facture_sejour_seq;

DELETE FROM om_droit;
DELETE FROM om_permission;
DELETE FROM om_etat;
DELETE FROM om_requete;
DELETE FROM om_sousetat;

\copy om_droit (om_droit,libelle,om_profil) from 'refuge_data/V2/om_droit.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_permission (om_permission,libelle,type) from 'refuge_data/V2/om_permission.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy cause_mort (cause_mort,libelle) from 'refuge_data/V2/cause_mort.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_requete (om_requete,code,libelle,description,requete,merge_fields,type,classe,methode) from 'refuge_data/V2/om_requete.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_etat (om_etat,om_collectivite,id,libelle,actif,orientation,format,logo,logoleft,logotop,titre_om_htmletat,titreleft,titretop,titrelargeur,titrehauteur,titrebordure,corps_om_htmletatex,om_sql,se_font,se_couleurtexte,margeleft,margetop,margeright,margebottom,header_om_htmletat,header_offset,footer_om_htmletat,footer_offset) from 'refuge_data/V2/om_etat.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_sousetat (om_sousetat,om_collectivite,id,libelle,actif,titre,titrehauteur,titrefont,titreattribut,titretaille,titrebordure,titrealign,titrefond,titrefondcouleur,titretextecouleur,intervalle_debut,intervalle_fin,entete_flag,entete_fond,entete_orientation,entete_hauteur,entetecolone_bordure,entetecolone_align,entete_fondcouleur,entete_textecouleur,tableau_largeur,tableau_bordure,tableau_fontaille,bordure_couleur,se_fond1,se_fond2,cellule_fond,cellule_hauteur,cellule_largeur,cellule_bordure_un,cellule_bordure,cellule_align,cellule_fond_total,cellule_fontaille_total,cellule_hauteur_total,cellule_fondcouleur_total,cellule_bordure_total,cellule_align_total,cellule_fond_moyenne,cellule_fontaille_moyenne,cellule_hauteur_moyenne,cellule_fondcouleur_moyenne,cellule_bordure_moyenne,cellule_align_moyenne,cellule_fond_nbr,cellule_fontaille_nbr,cellule_hauteur_nbr,cellule_fondcouleur_nbr,cellule_bordure_nbr,cellule_align_nbr,cellule_numerique,cellule_total,cellule_moyenne,cellule_compteur,om_sql) from 'refuge_data/V2/om_sousetat.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy rue (rue,nom) from 'refuge_data/V2/rue.csv' with (FORMAT csv, HEADER true, DELIMITER ',');