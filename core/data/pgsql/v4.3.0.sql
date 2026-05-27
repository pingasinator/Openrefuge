-------------------------------------------------------------------------------
-- Script de mise à jour vers la version v4.3.0 depuis la version v4.2.0
--
-- @package framework_openmairie
-- @version SVN : $Id: v4.3.0.sql 4310 2018-06-07 11:18:57Z fmichon $
-------------------------------------------------------------------------------


ALTER TABLE om_droit ALTER COLUMN om_profil DROP DEFAULT;

ALTER TABLE om_utilisateur ALTER COLUMN om_utilisateur DROP DEFAULT;
ALTER TABLE om_utilisateur ALTER COLUMN nom DROP DEFAULT;
ALTER TABLE om_utilisateur ALTER COLUMN email DROP DEFAULT;
ALTER TABLE om_utilisateur ALTER COLUMN login DROP DEFAULT;
ALTER TABLE om_utilisateur ALTER COLUMN pwd DROP DEFAULT;
ALTER TABLE om_utilisateur ALTER COLUMN om_profil DROP DEFAULT;
ALTER TABLE om_utilisateur ALTER COLUMN om_type SET DEFAULT 'DB';

ALTER TABLE om_utilisateur ADD CONSTRAINT om_utilisateur_login_key UNIQUE (login);

ALTER TABLE om_etat ALTER COLUMN titreattribut SET DEFAULT '';
ALTER TABLE om_etat ALTER COLUMN corpsattribut SET DEFAULT '';
ALTER TABLE om_etat ALTER COLUMN footerattribut SET DEFAULT '';
ALTER TABLE om_etat ALTER COLUMN sousetat SET DEFAULT '';
ALTER TABLE om_lettretype ALTER COLUMN titreattribut SET DEFAULT '';
ALTER TABLE om_lettretype ALTER COLUMN corpsattribut SET DEFAULT '';
ALTER TABLE om_sousetat ALTER COLUMN titreattribut SET DEFAULT '';

ALTER TABLE om_etat ALTER COLUMN actif TYPE boolean USING CASE WHEN actif='Oui' THEN true ELSE false END;
ALTER TABLE om_lettretype ALTER COLUMN actif TYPE boolean USING CASE WHEN actif='Oui' THEN true ELSE false END;
ALTER TABLE om_sousetat ALTER COLUMN actif TYPE boolean USING CASE WHEN actif='Oui' THEN true ELSE false END;
ALTER TABLE om_etat ALTER actif DROP NOT NULL;
ALTER TABLE om_lettretype ALTER actif DROP NOT NULL;
ALTER TABLE om_sousetat ALTER actif DROP NOT NULL;

ALTER TABLE ONLY om_widget
    ADD CONSTRAINT om_widget_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);

ALTER TABLE om_profil ALTER COLUMN om_profil DROP DEFAULT;

ALTER TABLE om_widget ALTER COLUMN om_profil SET NOT NULL;

ALTER TABLE om_sig_map ALTER COLUMN actif TYPE boolean USING CASE WHEN actif='Oui' THEN true ELSE false END;


---
--- Gestion des profils non hiérarchiques / Mise à jour OBLIGATOIRE
---

---
ALTER TABLE om_droit DROP CONSTRAINT om_droit_pkey;
ALTER TABLE om_droit DROP CONSTRAINT om_droit_om_profil_fkey;
ALTER TABLE om_utilisateur DROP CONSTRAINT om_utilisateur_om_profil_fkey;
ALTER TABLE om_widget DROP CONSTRAINT om_widget_om_profil_fkey;
ALTER TABLE om_profil DROP CONSTRAINT om_profil_pkey;

ALTER TABLE om_droit RENAME COLUMN om_droit TO om_droit_old;
ALTER TABLE om_droit RENAME COLUMN om_profil TO om_profil_old;

CREATE SEQUENCE om_droit_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER TABLE om_droit ADD COLUMN om_droit integer NOT NULL DEFAULT nextval('om_droit_seq'::regclass);
ALTER TABLE om_droit ADD COLUMN libelle character varying(100);
ALTER TABLE om_droit ADD COLUMN om_profil integer;

UPDATE om_droit SET libelle=om_droit_old;
UPDATE om_droit SET om_profil=om_profil_old::integer;
---
ALTER TABLE om_profil RENAME COLUMN om_profil TO om_profil_old;
ALTER TABLE om_profil RENAME COLUMN libelle TO libelle_old;

CREATE SEQUENCE om_profil_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER TABLE om_profil ADD COLUMN om_profil integer NOT NULL DEFAULT nextval('om_profil_seq'::regclass);
ALTER TABLE om_profil ADD COLUMN libelle character varying(100);
ALTER TABLE om_profil ADD COLUMN hierarchie integer NOT NULL DEFAULT 0 ;

UPDATE om_profil SET libelle=libelle_old;
UPDATE om_profil SET hierarchie=om_profil_old::integer;
UPDATE om_droit SET om_profil=(select om_profil.om_profil from om_profil where hierarchie=om_droit.om_profil_old::integer);
---
ALTER TABLE om_utilisateur RENAME COLUMN om_profil TO om_profil_old;
ALTER TABLE om_utilisateur ADD COLUMN om_profil integer;

UPDATE om_utilisateur SET om_profil=(select om_profil.om_profil from om_profil where hierarchie=om_utilisateur.om_profil_old::integer);
---
ALTER TABLE om_widget RENAME COLUMN om_profil TO om_profil_old;
ALTER TABLE om_widget ADD COLUMN om_profil integer;

UPDATE om_widget SET om_profil=(select om_profil.om_profil from om_profil where hierarchie=om_widget.om_profil_old::integer);
---
ALTER TABLE om_droit ALTER COLUMN libelle SET NOT NULL;
ALTER TABLE om_droit ALTER COLUMN om_profil SET NOT NULL;
ALTER TABLE om_profil ALTER COLUMN libelle SET NOT NULL;
ALTER TABLE om_utilisateur ALTER COLUMN om_profil SET NOT NULL;
ALTER TABLE om_widget ALTER COLUMN om_profil SET NOT NULL;




ALTER TABLE om_droit DROP COLUMN om_droit_old;
ALTER TABLE om_droit DROP COLUMN om_profil_old;
ALTER TABLE om_profil DROP COLUMN libelle_old;
ALTER TABLE om_profil DROP COLUMN om_profil_old;
ALTER TABLE om_utilisateur DROP COLUMN om_profil_old;
ALTER TABLE om_widget DROP COLUMN om_profil_old;

---

ALTER TABLE ONLY om_droit
    ADD CONSTRAINT om_droit_pkey PRIMARY KEY (om_droit);

ALTER TABLE ONLY om_profil
    ADD CONSTRAINT om_profil_pkey PRIMARY KEY (om_profil);

ALTER TABLE ONLY om_utilisateur
    ADD CONSTRAINT om_utilisateur_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);

ALTER TABLE ONLY om_droit
    ADD CONSTRAINT om_droit_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);

ALTER TABLE ONLY om_widget
    ADD CONSTRAINT om_widget_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);

ALTER TABLE ONLY om_droit
    ADD CONSTRAINT om_droit_libelle_om_profil_key UNIQUE (libelle, om_profil);

ALTER SEQUENCE om_profil_seq OWNED BY om_profil.om_profil;
ALTER SEQUENCE om_droit_seq OWNED BY om_droit.om_droit;

ALTER TABLE om_droit ALTER COLUMN om_droit DROP DEFAULT;
ALTER TABLE om_profil ALTER COLUMN om_profil DROP DEFAULT;
ALTER SEQUENCE om_utilisateur_seq OWNED BY om_utilisateur.om_utilisateur;
ALTER SEQUENCE om_parametre_seq OWNED BY om_parametre.om_parametre;

---
