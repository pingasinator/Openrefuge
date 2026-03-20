-------------------------------------------------------------------------------
-- Script de mise à jour vers la version v4.2.0 depuis la version v4.1.0
--
-- @package framework_openmairie
-- @version SVN : $Id: v4.2.0.sql 4310 2018-06-07 11:18:57Z fmichon $
-------------------------------------------------------------------------------


-- modification dans une application existante

-- modification wms et geometry

-- renommer la table om_sig_point
ALTER TABLE om_sig_point RENAME TO om_sig_map;
-- supprimer la contrainte de cle primaire et secondaire
ALTER TABLE om_sig_map DROP constraint om_sig_point_pkey;
ALTER TABLE om_sig_map DROP constraint om_sig_point_om_collectivite_fkey;
-- renommer la cle primaire om_sig_point -> om_sig_map
ALTER TABLE ONLY om_sig_map RENAME COLUMN om_sig_point TO om_sig_map;
-- supprimer la sequence
DROP SEQUENCE om_sig_point_seq;
-- ajouter les champs nouveaux
ALTER TABLE ONLY om_sig_map ADD COLUMN type_geometrie character varying(30);
ALTER TABLE ONLY om_sig_map ADD COLUMN lib_geometrie character varying(50);
-- integrite referentielle
ALTER TABLE ONLY om_sig_map
    ADD CONSTRAINT om_sig_map_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);
-- cle primaire
ALTER TABLE ONLY om_sig_map
    ADD CONSTRAINT om_sig_map_pkey PRIMARY KEY (om_sig_map);




CREATE TABLE om_sig_wms
(
  om_sig_wms integer NOT NULL,
  libelle character varying(50) NOT NULL,
  om_collectivite integer NOT NULL,
  id character varying(50) NOT NULL,
  chemin character varying(255) NOT NULL,
  couches character varying(255) NOT NULL,
  PRIMARY KEY  (om_sig_wms)
);

CREATE TABLE om_sig_map_wms
(
  om_sig_map_wms integer NOT NULL,
  om_sig_wms integer NOT NULL,
  om_sig_map integer NOT NULL,
  ol_map character varying(50) NOT NULL,
  ordre integer NOT NULL,
  visibility character varying(3) NOT NULL,
  panier character varying(3),
  pa_nom character varying(50),
  pa_layer character varying(50),
  pa_attribut character varying(50),
  pa_encaps character varying(3),
  pa_sql text,
  pa_type_geometrie character varying(30),
  PRIMARY KEY  (om_sig_map_wms)
);

CREATE TABLE om_sig_map_comp
(
  om_sig_map_comp integer NOT NULL,
  om_sig_map integer NOT NULL,
  libelle character varying(50) NOT NULL,
  ordre integer NOT NULL,
  actif character varying(3),
  comp_maj character varying(3),
  type_geometrie character varying(30),
  comp_table_update character varying(30),
  comp_champ character varying(30),
  PRIMARY KEY  (om_sig_map_comp)
);

-- integrite referentielle

ALTER TABLE ONLY om_sig_map_wms
    ADD CONSTRAINT om_sig_map_wms_om_sig_map_fkey FOREIGN KEY (om_sig_map) REFERENCES om_sig_map(om_sig_map);
ALTER TABLE ONLY om_sig_map_wms
    ADD CONSTRAINT om_sig_map_wms_om_sig_wms_fkey FOREIGN KEY (om_sig_wms) REFERENCES om_sig_wms(om_sig_wms);
ALTER TABLE ONLY om_sig_map_comp
    ADD CONSTRAINT om_sig_map_comp_om_sig_map_fkey FOREIGN KEY (om_sig_map) REFERENCES om_sig_map(om_sig_map);
ALTER TABLE ONLY om_sig_wms
    ADD CONSTRAINT om_sig_wms_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


-- sequence

CREATE SEQUENCE om_sig_wms_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

  
CREATE SEQUENCE om_sig_map_wms_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
  
CREATE SEQUENCE om_sig_map_comp_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE SEQUENCE om_sig_map_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
