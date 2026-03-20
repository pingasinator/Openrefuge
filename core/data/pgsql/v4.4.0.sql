-------------------------------------------------------------------------------
-- Script de mise à jour vers la version v4.4.0 depuis la version v4.3.0
--
-- @package framework_openmairie
-- @version SVN : $Id: v4.4.0.sql 4310 2018-06-07 11:18:57Z fmichon $
-------------------------------------------------------------------------------




-- bugs visibility dans om_sig_map

ALTER TABLE om_sig_map_wms ALTER visibility DROP NOT NULL;

--------------------------------------------------------------------------------
-- GESTION DES REQUETES POUR LES ETATS ET LETTRES TYPE
--------------------------------------------------------------------------------
-- Création de la table
CREATE TABLE om_requete(
    id integer NOT NULL,
    code character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    description character varying(200),
    requete text,
    merge_fields text
);
-- Création de la séquence
CREATE SEQUENCE om_requete_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
-- Clé primaire
ALTER TABLE ONLY om_requete
    ADD CONSTRAINT om_requete_pkey PRIMARY KEY (id);
-- Récupération de toutes les requêtes existantes dans les tables om_etat et
-- om_lettretype
insert into om_requete (id, code, libelle, requete)
(
select nextval('om_requete_seq'), '-', 'Requête SQL', lib1
from (select om_sql as lib1, om_sql
      from om_lettretype group by om_sql
      union
      select om_sql as lib1, om_sql
      from om_etat group by om_sql
      order by lib1) as sql1
group by lib1
);
update om_etat set om_sql=(select om_requete.id from om_requete where om_requete.requete=om_etat.om_sql);
update om_lettretype set om_sql=(select om_requete.id from om_requete where om_requete.requete=om_lettretype.om_sql);
alter table om_etat alter column om_sql TYPE integer USING om_sql::integer;
alter table om_lettretype alter column om_sql TYPE integer USING om_sql::integer;
-- Clés étrangères
ALTER TABLE ONLY om_etat
    ADD CONSTRAINT om_etat_om_requete_fkey FOREIGN KEY (om_sql) REFERENCES om_requete(id);
ALTER TABLE ONLY om_lettretype
    ADD CONSTRAINT om_lettretype_om_requete_fkey FOREIGN KEY (om_sql) REFERENCES om_requete(id);
-- Permission
INSERT INTO om_droit VALUES (nextval('om_droit_seq'), 'om_requete',
(select om_profil from om_profil where libelle='ADMINISTRATEUR'));
--------------------------------------------------------------------------------

--------------------------------------------------------------------------------
-- GESTION DES LOGOS POUR LES ETATS ET LETTRES TYPE
--------------------------------------------------------------------------------
-- Création de la table
CREATE TABLE om_logo (
    om_logo integer,
    id character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    description character varying(200),
    fichier character varying(100) NOT NULL,
    resolution integer,
    actif boolean,
    om_collectivite integer NOT NULL
);
-- Création de la séquence
CREATE SEQUENCE om_logo_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
-- Clé primaire
ALTER TABLE ONLY om_logo
    ADD CONSTRAINT om_logo_pkey PRIMARY KEY (om_logo);
-- Clé étrangère
ALTER TABLE ONLY om_logo
    ADD CONSTRAINT om_logo_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);
-- Récupération de toutes les requêtes existantes dans les tables om_etat et
-- om_lettretype
insert into om_logo (om_logo, id, libelle, fichier, actif, om_collectivite)
(
select nextval('om_logo_seq'), lib1, lib1, lib1, true, 1
from (select logo as lib1, logo
      from om_lettretype group by logo
      union
      select logo as lib1, logo
      from om_etat group by logo
      order by lib1) as sql1
group by lib1
);
--update om_etat set logo=(select om_logo.id from om_logo where om_logo.fichier=om_etat.logo);
--update om_lettretype set logo=(select om_logo.id from om_logo where om_logo.fichier=om_lettretype.logo);
--alter table om_etat alter column logo TYPE integer USING logo::integer;
--alter table om_lettretype alter column logo TYPE integer USING logo::integer;
-- Clés étrangères
--ALTER TABLE ONLY om_etat
--    ADD CONSTRAINT om_etat_om_logo_fkey FOREIGN KEY (logo) REFERENCES om_logo(id);
--ALTER TABLE ONLY om_lettretype
--    ADD CONSTRAINT om_lettretype_om_logo_fkey FOREIGN KEY (logo) REFERENCES om_logo(id);
-- Permission
INSERT INTO om_droit VALUES (nextval('om_droit_seq'), 'om_logo',
(select om_profil from om_profil where libelle='SUPER UTILISATEUR'));
--------------------------------------------------------------------------------
alter table om_etat drop column footerfont;
alter table om_etat drop column footerattribut;
alter table om_etat drop column footertaille;
alter table om_etat alter column sousetat drop not null;
alter table om_etat alter column logo drop not null;
alter table om_lettretype alter column logo drop not null;
ALTER TABLE om_etat ALTER COLUMN libelle TYPE character varying(100);
ALTER TABLE om_sousetat ALTER COLUMN libelle TYPE character varying(100);
ALTER TABLE om_lettretype ALTER COLUMN libelle TYPE character varying(100);

-- nouvelles fonctionnalites om_sig
ALTER TABLE om_sig_map_wms ADD COLUMN baselayer character varying(3);
ALTER TABLE om_sig_map_wms ADD COLUMN singletile character varying(3);
ALTER TABLE om_sig_map_wms ADD COLUMN sql_filter text;
ALTER TABLE om_sig_map_wms ADD COLUMN maxzoomlevel integer;
ALTER TABLE om_sig_wms ADD COLUMN cache_type character varying(3);
ALTER TABLE om_sig_wms ADD COLUMN cache_gfi_chemin character varying(255);
ALTER TABLE om_sig_wms ADD COLUMN cache_gfi_couches character varying(255);


--
-- Modification des séquences pour qu'elles soient rattachées à la colonne
-- qui la concerne
--
ALTER SEQUENCE om_collectivite_seq OWNED BY om_collectivite.om_collectivite;
ALTER SEQUENCE om_etat_seq OWNED BY om_etat.om_etat;
ALTER SEQUENCE om_lettretype_seq OWNED BY om_lettretype.om_lettretype;
ALTER SEQUENCE om_logo_seq OWNED BY om_logo.om_logo;
ALTER SEQUENCE om_requete_seq OWNED BY om_requete.id;
ALTER SEQUENCE om_sig_map_comp_seq OWNED BY om_sig_map_comp.om_sig_map_comp;
ALTER SEQUENCE om_sig_map_seq OWNED BY om_sig_map.om_sig_map;
ALTER SEQUENCE om_sig_map_wms_seq OWNED BY om_sig_map_wms.om_sig_map_wms;
ALTER SEQUENCE om_sig_wms_seq OWNED BY om_sig_wms.om_sig_wms;
ALTER SEQUENCE om_sousetat_seq OWNED BY om_sousetat.om_sousetat;
ALTER SEQUENCE om_tdb_seq OWNED BY om_tdb.om_tdb;
ALTER SEQUENCE om_widget_seq OWNED BY om_widget.om_widget;

--
-- Modification de la taille des champs dans om_parametre
--
ALTER TABLE om_parametre ALTER COLUMN libelle TYPE character varying(100);
ALTER TABLE om_parametre ALTER COLUMN valeur TYPE character varying(250);

--
-- Renommage de la clé primaire selon la convention openmairie
--
ALTER TABLE om_requete RENAME COLUMN id TO om_requete;



---
--- Nouvelle gestion des tableaux de bord
---

-- Suppression de la table om_tdb désormais inutile
DROP TABLE om_tdb CASCADE;

-- Création de la table om_dashboard
CREATE TABLE om_dashboard (
om_dashboard integer NOT NULL,
om_profil integer NOT NULL,
bloc character varying(10) NOT NULL,
position integer,
om_widget integer NOT NULL
);
ALTER TABLE ONLY om_dashboard
    ADD CONSTRAINT om_dashboard_pkey PRIMARY KEY (om_dashboard);
ALTER TABLE ONLY om_dashboard
    ADD CONSTRAINT om_dashboard_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);
ALTER TABLE ONLY om_dashboard
    ADD CONSTRAINT om_dashboard_om_widget_fkey FOREIGN KEY (om_widget) REFERENCES om_widget(om_widget);
CREATE SEQUENCE om_dashboard_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
SELECT pg_catalog.setval('om_dashboard_seq', 1, false);
ALTER SEQUENCE om_dashboard_seq OWNED BY om_dashboard.om_dashboard;
INSERT INTO om_droit VALUES (nextval('om_droit_seq'), 'om_dashboard',
(select om_profil from om_profil where libelle='ADMINISTRATEUR'));

-- Modification de la table om_widget
ALTER TABLE ONLY om_widget
    ADD CONSTRAINT om_widget_libelle_om_collectivite_key UNIQUE (libelle, om_collectivite);
ALTER TABLE om_widget
    DROP CONSTRAINT om_widget_om_profil_fkey;
ALTER TABLE om_widget
    DROP CONSTRAINT om_widget_om_collectivite_fkey;
ALTER TABLE om_widget DROP COLUMN om_profil;
ALTER TABLE om_widget DROP COLUMN om_collectivite;
ALTER TABLE om_widget ALTER COLUMN "libelle" TYPE character varying(100);
ALTER TABLE om_widget ADD COLUMN "type" character varying(40) NOT NULL;
ALTER TABLE om_widget ALTER COLUMN "lien" SET DEFAULT ''::character varying;
ALTER TABLE om_widget ALTER COLUMN "texte" SET DEFAULT ''::text;

-- Mise à jour des om_widget pour mettre le web par défaut
UPDATE om_widget SET type='web' where type='' or type=null;

-- Modifications de type de champ pour permettre plus de possibilités
ALTER TABLE om_utilisateur ALTER COLUMN email TYPE character varying(100);
ALTER TABLE om_parametre ALTER COLUMN valeur TYPE text;

