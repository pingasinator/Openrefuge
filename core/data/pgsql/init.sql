--
-- PostgreSQL database dump
--

-- SET statement_timeout = 0;
-- SET client_encoding = 'UTF8';
-- SET standard_conforming_strings = on;
-- SET check_function_bodies = false;
-- SET client_min_messages = warning;

-- SET search_path = openexemple, pg_catalog;

-- SET default_tablespace = '';

-- SET default_with_oids = false;

--
-- Name: om_collectivite; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_collectivite (
    om_collectivite integer NOT NULL,
    libelle character varying(100) NOT NULL,
    niveau character varying(1) NOT NULL
);


--
-- Name: TABLE om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_collectivite IS 'Ville utilisant openADS';


--
-- Name: COLUMN om_collectivite.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_collectivite.om_collectivite IS 'Identifiant unique';


--
-- Name: COLUMN om_collectivite.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_collectivite.libelle IS 'Libellé de la ville';


--
-- Name: COLUMN om_collectivite.niveau; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_collectivite.niveau IS 'Niveau de la collectivité (1 = mono collectivité, 2 = gère plusieurs autres collectivité)';


--
-- Name: om_collectivite_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_collectivite_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_collectivite_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_collectivite_seq OWNED BY om_collectivite.om_collectivite;


--
-- Name: om_dashboard; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_dashboard (
    om_dashboard integer NOT NULL,
    om_profil integer NOT NULL,
    bloc character varying(10) NOT NULL,
    "position" integer,
    om_widget integer NOT NULL
);


--
-- Name: TABLE om_dashboard; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_dashboard IS 'Paramétrage du tableau de bord par profil';


--
-- Name: COLUMN om_dashboard.om_dashboard; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_dashboard.om_dashboard IS 'Identifiant unique';


--
-- Name: COLUMN om_dashboard.om_profil; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_dashboard.om_profil IS 'Profil auquel on affecte le tableau de ville';


--
-- Name: COLUMN om_dashboard.bloc; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_dashboard.bloc IS 'Bloc de positionnement du widget';


--
-- Name: COLUMN om_dashboard."position"; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_dashboard."position" IS 'Position du widget dans le bloc';


--
-- Name: COLUMN om_dashboard.om_widget; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_dashboard.om_widget IS 'Identifiant du widget';


--
-- Name: om_dashboard_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_dashboard_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_dashboard_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_dashboard_seq OWNED BY om_dashboard.om_dashboard;


--
-- Name: om_droit; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_droit (
    om_droit integer NOT NULL,
    libelle character varying(100) NOT NULL,
    om_profil integer NOT NULL
);


--
-- Name: TABLE om_droit; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_droit IS 'Paramétrage des droits';


--
-- Name: COLUMN om_droit.om_droit; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_droit.om_droit IS 'Identifiant unique';


--
-- Name: COLUMN om_droit.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_droit.libelle IS 'Libellé du droit';


--
-- Name: COLUMN om_droit.om_profil; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_droit.om_profil IS 'Type de profil auquel est lié le droit';


--
-- Name: om_droit_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_droit_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_droit_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_droit_seq OWNED BY om_droit.om_droit;


--
-- Name: om_etat; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_etat (
    om_etat integer NOT NULL,
    om_collectivite integer NOT NULL,
    id character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    actif boolean,
    orientation character varying(2) NOT NULL,
    format character varying(5) NOT NULL,
    logo character varying(30),
    logoleft integer NOT NULL,
    logotop integer NOT NULL,
    titre_om_htmletat text NOT NULL,
    titreleft integer NOT NULL,
    titretop integer NOT NULL,
    titrelargeur integer NOT NULL,
    titrehauteur integer NOT NULL,
    titrebordure character varying(20) NOT NULL,
    corps_om_htmletatex text NOT NULL,
    om_sql integer NOT NULL,
    se_font character varying(20) NOT NULL,
    se_couleurtexte character varying(11) NOT NULL,
    margeleft integer DEFAULT 10 NOT NULL,
    margetop integer DEFAULT 10 NOT NULL,
    margeright integer DEFAULT 10 NOT NULL,
    margebottom integer DEFAULT 10 NOT NULL,
    header_om_htmletat text,
    header_offset integer DEFAULT 12 NOT NULL,
    footer_om_htmletat text,
    footer_offset integer DEFAULT 12 NOT NULL
);


--
-- Name: TABLE om_etat; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_etat IS 'Paramétrage des états';


--
-- Name: COLUMN om_etat.om_etat; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.om_etat IS 'Identifiant unique';


--
-- Name: COLUMN om_etat.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.om_collectivite IS 'Identifiant de la collectivité liée à l''état';


--
-- Name: COLUMN om_etat.id; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.id IS 'Identifiant de l''état';


--
-- Name: COLUMN om_etat.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.libelle IS 'Libellé de l''état';


--
-- Name: COLUMN om_etat.actif; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.actif IS 'Défini si l''état est actif';


--
-- Name: COLUMN om_etat.orientation; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.orientation IS 'Défini l''orientation de la page';


--
-- Name: COLUMN om_etat.format; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.format IS 'Défini le format de la page';


--
-- Name: COLUMN om_etat.logo; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.logo IS 'Défini le logo d''entête';


--
-- Name: COLUMN om_etat.logoleft; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.logoleft IS 'Position du logo à gauche';


--
-- Name: COLUMN om_etat.logotop; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.logotop IS 'Position du logo en haut';


--
-- Name: COLUMN om_etat.titre_om_htmletat; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.titre_om_htmletat IS 'Bloc de titre contenant un éditeur de texte riche';


--
-- Name: COLUMN om_etat.titreleft; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.titreleft IS 'Position du titre à gauche';


--
-- Name: COLUMN om_etat.titretop; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.titretop IS 'Position du titre en haut';


--
-- Name: COLUMN om_etat.titrelargeur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.titrelargeur IS 'Largeur du titre';


--
-- Name: COLUMN om_etat.titrehauteur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.titrehauteur IS 'Hauteur du titre';


--
-- Name: COLUMN om_etat.titrebordure; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.titrebordure IS 'Défini si les bordures du titre sont affichées';


--
-- Name: COLUMN om_etat.corps_om_htmletatex; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.corps_om_htmletatex IS 'Bloc de corps contenant un éditeur de texte riche';


--
-- Name: COLUMN om_etat.om_sql; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.om_sql IS 'Identifiant de la requête permettant de récupérer les champs de fusion de l''état';


--
-- Name: COLUMN om_etat.se_font; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.se_font IS 'Police du texte des sous-états';


--
-- Name: COLUMN om_etat.se_couleurtexte; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.se_couleurtexte IS 'Couleur du texte des sous-états';


--
-- Name: COLUMN om_etat.margeleft; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.margeleft IS 'Marge gauche de l''édition';


--
-- Name: COLUMN om_etat.margetop; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.margetop IS 'Marge haute de l''édition';


--
-- Name: COLUMN om_etat.margeright; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.margeright IS 'Marge droite de l''édition';


--
-- Name: COLUMN om_etat.margebottom; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_etat.margebottom IS 'Marge basse de l''édition';


--
-- Name: om_etat_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_etat_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_etat_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_etat_seq OWNED BY om_etat.om_etat;


--
-- Name: om_lettretype; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_lettretype (
    om_lettretype integer NOT NULL,
    om_collectivite integer NOT NULL,
    id character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    actif boolean,
    orientation character varying(2) NOT NULL,
    format character varying(5) NOT NULL,
    logo character varying(30),
    logoleft integer NOT NULL,
    logotop integer NOT NULL,
    titre_om_htmletat text NOT NULL,
    titreleft integer NOT NULL,
    titretop integer NOT NULL,
    titrelargeur integer NOT NULL,
    titrehauteur integer NOT NULL,
    titrebordure character varying(20) NOT NULL,
    corps_om_htmletatex text NOT NULL,
    om_sql integer NOT NULL,
    margeleft integer DEFAULT 10 NOT NULL,
    margetop integer DEFAULT 10 NOT NULL,
    margeright integer DEFAULT 10 NOT NULL,
    margebottom integer DEFAULT 10 NOT NULL,
    se_font character varying(20),
    se_couleurtexte character varying(11),
    header_om_htmletat text,
    header_offset integer DEFAULT 0 NOT NULL,
    footer_om_htmletat text,
    footer_offset integer DEFAULT 0 NOT NULL
);


--
-- Name: TABLE om_lettretype; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_lettretype IS 'Paramétrage des lettre-types';


--
-- Name: COLUMN om_lettretype.om_lettretype; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.om_lettretype IS 'Identifiant unique';


--
-- Name: COLUMN om_lettretype.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.om_collectivite IS 'Identifiant de la collectivité liée à la lettre-type';


--
-- Name: COLUMN om_lettretype.id; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.id IS 'Identifiant de la lettre-type';


--
-- Name: COLUMN om_lettretype.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.libelle IS 'Libellé de la lettre-type';


--
-- Name: COLUMN om_lettretype.actif; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.actif IS 'Défini si la lettre-type est active';


--
-- Name: COLUMN om_lettretype.orientation; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.orientation IS 'Défini l''orientation de la page';


--
-- Name: COLUMN om_lettretype.format; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.format IS 'Défini le format de la page';


--
-- Name: COLUMN om_lettretype.logo; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.logo IS 'Défini le logo d''entête';


--
-- Name: COLUMN om_lettretype.logoleft; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.logoleft IS 'Position du logo à gauche';


--
-- Name: COLUMN om_lettretype.logotop; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.logotop IS 'Position du logo en haut';


--
-- Name: COLUMN om_lettretype.titre_om_htmletat; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.titre_om_htmletat IS 'Bloc de titre contenant un éditeur de texte riche';


--
-- Name: COLUMN om_lettretype.titreleft; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.titreleft IS 'Position du titre à gauche';


--
-- Name: COLUMN om_lettretype.titretop; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.titretop IS 'Position du titre en haut';


--
-- Name: COLUMN om_lettretype.titrelargeur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.titrelargeur IS 'Largeur du titre';


--
-- Name: COLUMN om_lettretype.titrehauteur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.titrehauteur IS 'Hauteur du titre';


--
-- Name: COLUMN om_lettretype.titrebordure; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.titrebordure IS 'Défini si les bordures du titre sont affichées';


--
-- Name: COLUMN om_lettretype.corps_om_htmletatex; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.corps_om_htmletatex IS 'Bloc de corps contenant un éditeur de texte riche';


--
-- Name: COLUMN om_lettretype.om_sql; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.om_sql IS 'Identifiant de la requête permettant de récupérer les champs de fusion de la lettre-type';


--
-- Name: COLUMN om_lettretype.margeleft; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.margeleft IS 'Marge gauche de l''édition';


--
-- Name: COLUMN om_lettretype.margetop; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.margetop IS 'Marge haute de l''édition';


--
-- Name: COLUMN om_lettretype.margeright; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.margeright IS 'Marge droite de l''édition';


--
-- Name: COLUMN om_lettretype.margebottom; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.margebottom IS 'Marge basse de l''édition';


--
-- Name: COLUMN om_lettretype.se_font; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.se_font IS 'Police du texte des sous-états';


--
-- Name: COLUMN om_lettretype.se_couleurtexte; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_lettretype.se_couleurtexte IS 'Couleur du texte des sous-états';


--
-- Name: om_lettretype_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_lettretype_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_lettretype_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_lettretype_seq OWNED BY om_lettretype.om_lettretype;


--
-- Name: om_logo; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_logo (
    om_logo integer NOT NULL,
    id character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    description character varying(200),
    fichier character varying(100) NOT NULL,
    resolution integer,
    actif boolean,
    om_collectivite integer NOT NULL
);


--
-- Name: TABLE om_logo; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_logo IS 'Paramétrage des logos de lettre-types et états';


--
-- Name: COLUMN om_logo.om_logo; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.om_logo IS 'Identifiant unique';


--
-- Name: COLUMN om_logo.id; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.id IS 'Identifiant du logo';


--
-- Name: COLUMN om_logo.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.libelle IS 'Libellé du logo';


--
-- Name: COLUMN om_logo.description; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.description IS 'Description du logo';


--
-- Name: COLUMN om_logo.fichier; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.fichier IS 'Fichier de l''image';


--
-- Name: COLUMN om_logo.resolution; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.resolution IS 'Résolution de l''image';


--
-- Name: COLUMN om_logo.actif; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.actif IS 'Défini si le logo est utilisable dans les éditions';


--
-- Name: COLUMN om_logo.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_logo.om_collectivite IS 'Identifiant de la collectivité liée au logo';


--
-- Name: om_logo_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_logo_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_logo_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_logo_seq OWNED BY om_logo.om_logo;


--
-- Name: om_parametre; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_parametre (
    om_parametre integer NOT NULL,
    libelle character varying(100) NOT NULL,
    valeur text NOT NULL,
    om_collectivite integer NOT NULL
);


--
-- Name: TABLE om_parametre; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_parametre IS 'Paramétrage de l''application';


--
-- Name: COLUMN om_parametre.om_parametre; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_parametre.om_parametre IS 'Identifiant unique';


--
-- Name: COLUMN om_parametre.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_parametre.libelle IS 'Libellé du paramètre';


--
-- Name: COLUMN om_parametre.valeur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_parametre.valeur IS 'Valeur du paramètre';


--
-- Name: COLUMN om_parametre.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_parametre.om_collectivite IS 'Collectivité utilisant le paramètre';


--
-- Name: om_parametre_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_parametre_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_parametre_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_parametre_seq OWNED BY om_parametre.om_parametre;


--
-- Name: om_permission; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_permission (
    om_permission integer NOT NULL,
    libelle character varying(100) NOT NULL,
    type character varying(100) NOT NULL
);


--
-- Name: om_permission_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_permission_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_permission_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_permission_seq OWNED BY om_permission.om_permission;


--
-- Name: om_profil; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_profil (
    om_profil integer NOT NULL,
    libelle character varying(100) NOT NULL,
    hierarchie integer DEFAULT 0 NOT NULL
);


--
-- Name: TABLE om_profil; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_profil IS 'Type de profil des utilisateurs';


--
-- Name: COLUMN om_profil.om_profil; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_profil.om_profil IS 'Identifiant unique';


--
-- Name: COLUMN om_profil.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_profil.libelle IS 'Libellé du profil';


--
-- Name: COLUMN om_profil.hierarchie; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_profil.hierarchie IS 'Permet de rendre hiérarchique certains profils';


--
-- Name: om_profil_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_profil_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_profil_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_profil_seq OWNED BY om_profil.om_profil;


--
-- Name: om_requete; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_requete (
    om_requete integer NOT NULL,
    code character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    description character varying(200),
    requete text,
    merge_fields text,
    type character varying(200) NOT NULL,
    classe character varying(200),
    methode character varying(200)
);


--
-- Name: TABLE om_requete; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_requete IS 'Paramétrage des requêtes utilisées par les lettre-types et les états';


--
-- Name: COLUMN om_requete.om_requete; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.om_requete IS 'Identifiant unique';


--
-- Name: COLUMN om_requete.code; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.code IS 'Code de la requête';


--
-- Name: COLUMN om_requete.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.libelle IS 'Libellé de la requête';


--
-- Name: COLUMN om_requete.description; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.description IS 'Description de la requête';


--
-- Name: COLUMN om_requete.requete; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.requete IS 'Requête SQL';


--
-- Name: COLUMN om_requete.merge_fields; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.merge_fields IS 'Champs de fusion';


--
-- Name: COLUMN om_requete.type; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.type IS 'Requête SQL ou objet ?';


--
-- Name: COLUMN om_requete.classe; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.classe IS 'Nom de(s) la classe(s) contenant la méthode';


--
-- Name: COLUMN om_requete.methode; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_requete.methode IS 'Méthode (de la première classe si plusieurs définies) fournissant les champs de fusion. Si non spécifiée appel à une méthode générique';


--
-- Name: om_requete_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_requete_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_requete_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_requete_seq OWNED BY om_requete.om_requete;


--
-- Name: om_sig_extent; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_sig_extent (
    om_sig_extent integer NOT NULL,
    nom character varying(150),
    extent character varying(150),
    valide boolean
);


--
-- Name: om_sig_extent_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_sig_extent_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_sig_extent_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_sig_extent_seq OWNED BY om_sig_extent.om_sig_extent;


--
-- Name: om_sig_flux; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_sig_flux (
    om_sig_flux integer NOT NULL,
    libelle character varying(50) NOT NULL,
    om_collectivite integer NOT NULL,
    id character varying(50) NOT NULL,
    attribution character varying(150),
    chemin character varying(255) NOT NULL,
    couches character varying(255) NOT NULL,
    cache_type character varying(3),
    cache_gfi_chemin character varying(255),
    cache_gfi_couches character varying(255)
);


--
-- Name: om_sig_flux_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_sig_flux_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_sig_flux_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_sig_flux_seq OWNED BY om_sig_flux.om_sig_flux;


--
-- Name: om_sig_map; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_sig_map (
    om_sig_map integer NOT NULL,
    om_collectivite integer NOT NULL,
    id character varying(50) NOT NULL,
    libelle character varying(50) NOT NULL,
    actif boolean,
    zoom character varying(3) NOT NULL,
    fond_osm boolean,
    fond_bing boolean,
    fond_sat boolean,
    layer_info boolean,
    projection_externe character varying(60) NOT NULL,
    url text NOT NULL,
    om_sql text NOT NULL,
    retour character varying(50) NOT NULL,
    util_idx boolean,
    util_reqmo boolean,
    util_recherche boolean,
    source_flux integer,
    fond_default character varying(10) NOT NULL,
    om_sig_extent integer NOT NULL,
    restrict_extent boolean,
    sld_marqueur character varying(254),
    sld_data character varying(254),
    point_centrage public.geometry(Point,2154)
);


--
-- Name: TABLE om_sig_map; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_sig_map IS 'Table utile au SIG interne';


--
-- Name: om_sig_map_comp; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_sig_map_comp (
    om_sig_map_comp integer NOT NULL,
    om_sig_map integer NOT NULL,
    libelle character varying(50) NOT NULL,
    ordre integer NOT NULL,
    actif boolean,
    comp_maj boolean,
    type_geometrie character varying(30),
    comp_table_update character varying(30),
    comp_champ character varying(30),
    comp_champ_idx character varying(30),
    obj_class character varying(100) NOT NULL
);


--
-- Name: TABLE om_sig_map_comp; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_sig_map_comp IS 'Table utile au SIG interne';


--
-- Name: om_sig_map_comp_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_sig_map_comp_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_sig_map_comp_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_sig_map_comp_seq OWNED BY om_sig_map_comp.om_sig_map_comp;


--
-- Name: om_sig_map_flux; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_sig_map_flux (
    om_sig_map_flux integer NOT NULL,
    om_sig_flux integer NOT NULL,
    om_sig_map integer NOT NULL,
    ol_map character varying(50) NOT NULL,
    ordre integer NOT NULL,
    visibility boolean,
    panier boolean,
    pa_nom character varying(50),
    pa_layer character varying(50),
    pa_attribut character varying(50),
    pa_encaps character varying(3),
    pa_sql text,
    pa_type_geometrie character varying(30),
    sql_filter text,
    baselayer boolean,
    singletile boolean,
    maxzoomlevel integer
);


--
-- Name: om_sig_map_flux_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_sig_map_flux_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_sig_map_flux_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_sig_map_flux_seq OWNED BY om_sig_map_flux.om_sig_map_flux;


--
-- Name: om_sig_map_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_sig_map_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_sig_map_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_sig_map_seq OWNED BY om_sig_map.om_sig_map;


--
-- Name: om_sousetat; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_sousetat (
    om_sousetat integer NOT NULL,
    om_collectivite integer NOT NULL,
    id character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    actif boolean,
    titre text NOT NULL,
    titrehauteur integer NOT NULL,
    titrefont character varying(20) NOT NULL,
    titreattribut character varying(20) DEFAULT ''::character varying NOT NULL,
    titretaille integer NOT NULL,
    titrebordure character varying(20) NOT NULL,
    titrealign character varying(20) NOT NULL,
    titrefond character varying(20) NOT NULL,
    titrefondcouleur character varying(11) NOT NULL,
    titretextecouleur character varying(11) NOT NULL,
    intervalle_debut integer NOT NULL,
    intervalle_fin integer NOT NULL,
    entete_flag character varying(20) NOT NULL,
    entete_fond character varying(20) NOT NULL,
    entete_orientation character varying(100) NOT NULL,
    entete_hauteur integer NOT NULL,
    entetecolone_bordure character varying(200) NOT NULL,
    entetecolone_align character varying(100) NOT NULL,
    entete_fondcouleur character varying(11) NOT NULL,
    entete_textecouleur character varying(11) NOT NULL,
    tableau_largeur integer NOT NULL,
    tableau_bordure character varying(20) NOT NULL,
    tableau_fontaille integer NOT NULL,
    bordure_couleur character varying(11) NOT NULL,
    se_fond1 character varying(11) NOT NULL,
    se_fond2 character varying(11) NOT NULL,
    cellule_fond character varying(20) NOT NULL,
    cellule_hauteur integer NOT NULL,
    cellule_largeur character varying(200) NOT NULL,
    cellule_bordure_un character varying(200) NOT NULL,
    cellule_bordure character varying(200) NOT NULL,
    cellule_align character varying(100) NOT NULL,
    cellule_fond_total character varying(20) NOT NULL,
    cellule_fontaille_total integer NOT NULL,
    cellule_hauteur_total integer NOT NULL,
    cellule_fondcouleur_total character varying(11) NOT NULL,
    cellule_bordure_total character varying(200) NOT NULL,
    cellule_align_total character varying(100) NOT NULL,
    cellule_fond_moyenne character varying(20) NOT NULL,
    cellule_fontaille_moyenne integer NOT NULL,
    cellule_hauteur_moyenne integer NOT NULL,
    cellule_fondcouleur_moyenne character varying(11) NOT NULL,
    cellule_bordure_moyenne character varying(200) NOT NULL,
    cellule_align_moyenne character varying(100) NOT NULL,
    cellule_fond_nbr character varying(20) NOT NULL,
    cellule_fontaille_nbr integer NOT NULL,
    cellule_hauteur_nbr integer NOT NULL,
    cellule_fondcouleur_nbr character varying(11) NOT NULL,
    cellule_bordure_nbr character varying(200) NOT NULL,
    cellule_align_nbr character varying(100) NOT NULL,
    cellule_numerique character varying(200) NOT NULL,
    cellule_total character varying(100) NOT NULL,
    cellule_moyenne character varying(100) NOT NULL,
    cellule_compteur character varying(100) NOT NULL,
    om_sql text NOT NULL
);


--
-- Name: TABLE om_sousetat; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_sousetat IS 'Types de profil des utilisateurs';


--
-- Name: COLUMN om_sousetat.om_sousetat; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.om_sousetat IS 'Identifiant unique';


--
-- Name: COLUMN om_sousetat.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.om_collectivite IS 'Identifiant de la collectivité liée à la lettre-type';


--
-- Name: COLUMN om_sousetat.id; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.id IS 'Identifiant du sous-état';


--
-- Name: COLUMN om_sousetat.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.libelle IS 'Libellé du sous-état';


--
-- Name: COLUMN om_sousetat.actif; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.actif IS 'Défini si le sous-état est utilisable';


--
-- Name: COLUMN om_sousetat.titre; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titre IS 'Titre affiché dans le sous-état';


--
-- Name: COLUMN om_sousetat.titrehauteur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titrehauteur IS 'Hauteur du titre en cm';


--
-- Name: COLUMN om_sousetat.titrefont; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titrefont IS 'Font du texte du titre';


--
-- Name: COLUMN om_sousetat.titreattribut; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titreattribut IS 'Attribut du texte du titre (italique, souligné, gras)';


--
-- Name: COLUMN om_sousetat.titretaille; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titretaille IS 'Taille du texte du titre';


--
-- Name: COLUMN om_sousetat.titrebordure; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titrebordure IS 'Affiche ou non les bordures sur le titre';


--
-- Name: COLUMN om_sousetat.titrealign; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titrealign IS 'Alignement du texte du titre';


--
-- Name: COLUMN om_sousetat.titrefond; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titrefond IS 'Affiche ou non une couleur de fond au titre';


--
-- Name: COLUMN om_sousetat.titrefondcouleur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titrefondcouleur IS 'Couleur de fond du titre';


--
-- Name: COLUMN om_sousetat.titretextecouleur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.titretextecouleur IS 'Couleur du texte du titre';


--
-- Name: COLUMN om_sousetat.intervalle_debut; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.intervalle_debut IS 'Début du titre';


--
-- Name: COLUMN om_sousetat.intervalle_fin; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.intervalle_fin IS 'Fin du titre';


--
-- Name: COLUMN om_sousetat.entete_flag; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entete_flag IS 'Défini si le tableau contient une ligne d''entête';


--
-- Name: COLUMN om_sousetat.entete_fond; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entete_fond IS 'Défini si l''entête du tableau à une couleur de fond';


--
-- Name: COLUMN om_sousetat.entete_orientation; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entete_orientation IS 'Orientation du texte dans les entêtes';


--
-- Name: COLUMN om_sousetat.entete_hauteur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entete_hauteur IS 'Hauteur de la ligne d''entête';


--
-- Name: COLUMN om_sousetat.entetecolone_bordure; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entetecolone_bordure IS 'Affichage ou non de chaque bordure des cellules d''entête';


--
-- Name: COLUMN om_sousetat.entetecolone_align; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entetecolone_align IS 'Alignement du texte dans chaque cellule d''entête';


--
-- Name: COLUMN om_sousetat.entete_fondcouleur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entete_fondcouleur IS 'Couleur de fond de l''entête';


--
-- Name: COLUMN om_sousetat.entete_textecouleur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.entete_textecouleur IS 'Couleur du texte de l''entête';


--
-- Name: COLUMN om_sousetat.tableau_largeur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.tableau_largeur IS 'Largeur du tableau';


--
-- Name: COLUMN om_sousetat.tableau_bordure; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.tableau_bordure IS 'Défini si on affiche les bordures du tableau';


--
-- Name: COLUMN om_sousetat.tableau_fontaille; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.tableau_fontaille IS 'Taille du texte du tableau';


--
-- Name: COLUMN om_sousetat.bordure_couleur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.bordure_couleur IS 'Couleur des bordures du tableau';


--
-- Name: COLUMN om_sousetat.se_fond1; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.se_fond1 IS 'Couleur de fond du tableau';


--
-- Name: COLUMN om_sousetat.se_fond2; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.se_fond2 IS 'Seconde couleur de fond du tableau';


--
-- Name: COLUMN om_sousetat.cellule_fond; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fond IS 'Défini si les cellules du tableau ont une couleur de fond';


--
-- Name: COLUMN om_sousetat.cellule_hauteur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_hauteur IS 'Hauteur des cellules';


--
-- Name: COLUMN om_sousetat.cellule_largeur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_largeur IS 'Largeur des cellules';


--
-- Name: COLUMN om_sousetat.cellule_bordure_un; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_bordure_un IS 'Bordure des cellules';


--
-- Name: COLUMN om_sousetat.cellule_bordure; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_bordure IS 'Bordure des cellules';


--
-- Name: COLUMN om_sousetat.cellule_align; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_align IS 'Alignement du texte dans chaque cellule';


--
-- Name: COLUMN om_sousetat.cellule_fond_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fond_total IS 'Défini si la ligne des totaux a une couleur de fond';


--
-- Name: COLUMN om_sousetat.cellule_fontaille_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fontaille_total IS 'Taille du texte de la ligne des totaux';


--
-- Name: COLUMN om_sousetat.cellule_hauteur_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_hauteur_total IS 'Hauteur de la ligne des totaux';


--
-- Name: COLUMN om_sousetat.cellule_fondcouleur_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fondcouleur_total IS 'Couleur de fond de la ligne des totaux';


--
-- Name: COLUMN om_sousetat.cellule_bordure_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_bordure_total IS 'Défini les bordures de la ligne des totaux';


--
-- Name: COLUMN om_sousetat.cellule_align_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_align_total IS 'Alignement du texte de la ligne des totaux';


--
-- Name: COLUMN om_sousetat.cellule_fond_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fond_moyenne IS 'Défini si la ligne des moyennes contient une couleur de fond';


--
-- Name: COLUMN om_sousetat.cellule_fontaille_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fontaille_moyenne IS 'Taille du texte de la ligne des moyennes';


--
-- Name: COLUMN om_sousetat.cellule_hauteur_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_hauteur_moyenne IS 'Hauteur de la ligne des moyennes';


--
-- Name: COLUMN om_sousetat.cellule_fondcouleur_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fondcouleur_moyenne IS 'Couleur de fond de la ligne des moyennes';


--
-- Name: COLUMN om_sousetat.cellule_bordure_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_bordure_moyenne IS 'Défini les bordures de la ligne des moyennes';


--
-- Name: COLUMN om_sousetat.cellule_align_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_align_moyenne IS 'Alignement du texte de la ligne des moyennes';


--
-- Name: COLUMN om_sousetat.cellule_fond_nbr; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fond_nbr IS 'Defini si une couleur de fond du compte de ligne est affichée';


--
-- Name: COLUMN om_sousetat.cellule_fontaille_nbr; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fontaille_nbr IS 'Taille du texte du compte de lignes';


--
-- Name: COLUMN om_sousetat.cellule_hauteur_nbr; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_hauteur_nbr IS 'Hauteur du compte de nombre de lignes';


--
-- Name: COLUMN om_sousetat.cellule_fondcouleur_nbr; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_fondcouleur_nbr IS 'Couleur de fond du compte de nombre de lignes';


--
-- Name: COLUMN om_sousetat.cellule_bordure_nbr; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_bordure_nbr IS 'Défini les bordures du compte de lignes';


--
-- Name: COLUMN om_sousetat.cellule_align_nbr; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_align_nbr IS 'Alignement du texte du compte de lignes';


--
-- Name: COLUMN om_sousetat.cellule_numerique; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_numerique IS 'Formatage du texte de chaque cellule du tableau';


--
-- Name: COLUMN om_sousetat.cellule_total; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_total IS 'Formatage du texte de chaque cellule des totaux';


--
-- Name: COLUMN om_sousetat.cellule_moyenne; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_moyenne IS 'Formatage du texte de chaque cellule des moyennes';


--
-- Name: COLUMN om_sousetat.cellule_compteur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.cellule_compteur IS 'Formatage du texte de chaque cellule du compteur';


--
-- Name: COLUMN om_sousetat.om_sql; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_sousetat.om_sql IS 'Requête SQL permettant de récupérer les données à afficher';


--
-- Name: om_sousetat_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_sousetat_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_sousetat_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_sousetat_seq OWNED BY om_sousetat.om_sousetat;


--
-- Name: om_utilisateur; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_utilisateur (
    om_utilisateur integer NOT NULL,
    nom character varying(30) NOT NULL,
    email character varying(100) NOT NULL,
    login character varying(30) NOT NULL,
    pwd character varying(100) NOT NULL,
    om_collectivite integer NOT NULL,
    om_type character varying(20) DEFAULT 'DB'::character varying NOT NULL,
    om_profil integer NOT NULL
);


--
-- Name: TABLE om_utilisateur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_utilisateur IS 'Utilisateurs';


--
-- Name: COLUMN om_utilisateur.om_utilisateur; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.om_utilisateur IS 'Identifiant unique';


--
-- Name: COLUMN om_utilisateur.nom; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.nom IS 'Nom de l''utilisateur';


--
-- Name: COLUMN om_utilisateur.email; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.email IS 'Mail de l''utilisateur';


--
-- Name: COLUMN om_utilisateur.login; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.login IS 'Identifiant de l''utilisateur';


--
-- Name: COLUMN om_utilisateur.pwd; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.pwd IS 'Mot de passe de l''utilisateur';


--
-- Name: COLUMN om_utilisateur.om_collectivite; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.om_collectivite IS 'Collectivité de l''utilisateur';


--
-- Name: COLUMN om_utilisateur.om_type; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.om_type IS 'Type de l''utilisateur (LDAP = récupéré depuis un LDAP, DB = crée depuis l''application)';


--
-- Name: COLUMN om_utilisateur.om_profil; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_utilisateur.om_profil IS 'Profil de l''utilisateur';


--
-- Name: om_utilisateur_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_utilisateur_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_utilisateur_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_utilisateur_seq OWNED BY om_utilisateur.om_utilisateur;


--
-- Name: om_widget; Type: TABLE; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE TABLE om_widget (
    om_widget integer NOT NULL,
    libelle character varying(100) NOT NULL,
    lien character varying(80) DEFAULT ''::character varying NOT NULL,
    texte text DEFAULT ''::text NOT NULL,
    type character varying(40) NOT NULL,
    script character varying(80) DEFAULT ''::character varying NOT NULL,
    arguments text DEFAULT ''::text NOT NULL
);


--
-- Name: TABLE om_widget; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON TABLE om_widget IS 'Widgets pour les tableaux de bord des profils';


--
-- Name: COLUMN om_widget.om_widget; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.om_widget IS 'Identifiant unique';


--
-- Name: COLUMN om_widget.libelle; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.libelle IS 'Libellé du widget';


--
-- Name: COLUMN om_widget.lien; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.lien IS 'Lien qui pointe vers le widget (peut être vers une URL ou un fichier)';


--
-- Name: COLUMN om_widget.texte; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.texte IS 'Texte affiché dans le widget';


--
-- Name: COLUMN om_widget.type; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.type IS 'Type du widget (''web'' si pointe vers une URL ou ''file'' si pointe vers un fichier)';


--
-- Name: COLUMN om_widget.script; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.script IS 'Fichier utilisé par le widget';


--
-- Name: COLUMN om_widget.arguments; Type: COMMENT; Schema: openexemple; Owner: -
--

COMMENT ON COLUMN om_widget.arguments IS 'Arguments affiché dans le widget ';


--
-- Name: om_widget_seq; Type: SEQUENCE; Schema: openexemple; Owner: -
--

CREATE SEQUENCE om_widget_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: om_widget_seq; Type: SEQUENCE OWNED BY; Schema: openexemple; Owner: -
--

ALTER SEQUENCE om_widget_seq OWNED BY om_widget.om_widget;


--
-- Name: om_collectivite_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_collectivite
    ADD CONSTRAINT om_collectivite_pkey PRIMARY KEY (om_collectivite);


--
-- Name: om_dashboard_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_dashboard
    ADD CONSTRAINT om_dashboard_pkey PRIMARY KEY (om_dashboard);


--
-- Name: om_droit_libelle_om_profil_key; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_droit
    ADD CONSTRAINT om_droit_libelle_om_profil_key UNIQUE (libelle, om_profil);


--
-- Name: om_droit_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_droit
    ADD CONSTRAINT om_droit_pkey PRIMARY KEY (om_droit);


--
-- Name: om_etat_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_etat
    ADD CONSTRAINT om_etat_pkey PRIMARY KEY (om_etat);


--
-- Name: om_lettretype_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_lettretype
    ADD CONSTRAINT om_lettretype_pkey PRIMARY KEY (om_lettretype);


--
-- Name: om_logo_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_logo
    ADD CONSTRAINT om_logo_pkey PRIMARY KEY (om_logo);


--
-- Name: om_parametre_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_parametre
    ADD CONSTRAINT om_parametre_pkey PRIMARY KEY (om_parametre);


--
-- Name: om_permission_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_permission
    ADD CONSTRAINT om_permission_pkey PRIMARY KEY (om_permission);


--
-- Name: om_profil_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_profil
    ADD CONSTRAINT om_profil_pkey PRIMARY KEY (om_profil);


--
-- Name: om_requete_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_requete
    ADD CONSTRAINT om_requete_pkey PRIMARY KEY (om_requete);


--
-- Name: om_sig_extent_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_sig_extent
    ADD CONSTRAINT om_sig_extent_pkey PRIMARY KEY (om_sig_extent);


--
-- Name: om_sig_flux_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_sig_flux
    ADD CONSTRAINT om_sig_flux_pkey PRIMARY KEY (om_sig_flux);


--
-- Name: om_sig_map_comp_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_sig_map_comp
    ADD CONSTRAINT om_sig_map_comp_pkey PRIMARY KEY (om_sig_map_comp);


--
-- Name: om_sig_map_flux_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_sig_map_flux
    ADD CONSTRAINT om_sig_map_flux_pkey PRIMARY KEY (om_sig_map_flux);


--
-- Name: om_sig_map_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_sig_map
    ADD CONSTRAINT om_sig_map_pkey PRIMARY KEY (om_sig_map);


--
-- Name: om_sousetat_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_sousetat
    ADD CONSTRAINT om_sousetat_pkey PRIMARY KEY (om_sousetat);


--
-- Name: om_utilisateur_login_key; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_utilisateur
    ADD CONSTRAINT om_utilisateur_login_key UNIQUE (login);


--
-- Name: om_utilisateur_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_utilisateur
    ADD CONSTRAINT om_utilisateur_pkey PRIMARY KEY (om_utilisateur);


--
-- Name: om_widget_pkey; Type: CONSTRAINT; Schema: openexemple; Owner: -; Tablespace: 
--

ALTER TABLE ONLY om_widget
    ADD CONSTRAINT om_widget_pkey PRIMARY KEY (om_widget);


--
-- Name: om_sig_extent_nom_idx; Type: INDEX; Schema: openexemple; Owner: -; Tablespace: 
--

CREATE INDEX om_sig_extent_nom_idx ON om_sig_extent USING btree (nom);


--
-- Name: om_dashboard_om_profil_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_dashboard
    ADD CONSTRAINT om_dashboard_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);


--
-- Name: om_dashboard_om_widget_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_dashboard
    ADD CONSTRAINT om_dashboard_om_widget_fkey FOREIGN KEY (om_widget) REFERENCES om_widget(om_widget);


--
-- Name: om_droit_om_profil_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_droit
    ADD CONSTRAINT om_droit_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);


--
-- Name: om_etat_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_etat
    ADD CONSTRAINT om_etat_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_etat_om_requete_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_etat
    ADD CONSTRAINT om_etat_om_requete_fkey FOREIGN KEY (om_sql) REFERENCES om_requete(om_requete);


--
-- Name: om_lettretype_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_lettretype
    ADD CONSTRAINT om_lettretype_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_lettretype_om_requete_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_lettretype
    ADD CONSTRAINT om_lettretype_om_requete_fkey FOREIGN KEY (om_sql) REFERENCES om_requete(om_requete);


--
-- Name: om_logo_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_logo
    ADD CONSTRAINT om_logo_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_parametre_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_parametre
    ADD CONSTRAINT om_parametre_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_sig_flux_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_flux
    ADD CONSTRAINT om_sig_flux_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_sig_map_comp_om_sig_map_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_map_comp
    ADD CONSTRAINT om_sig_map_comp_om_sig_map_fkey FOREIGN KEY (om_sig_map) REFERENCES om_sig_map(om_sig_map);


--
-- Name: om_sig_map_flux_om_sig_flux_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_map_flux
    ADD CONSTRAINT om_sig_map_flux_om_sig_flux_fkey FOREIGN KEY (om_sig_flux) REFERENCES om_sig_flux(om_sig_flux);


--
-- Name: om_sig_map_flux_om_sig_map_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_map_flux
    ADD CONSTRAINT om_sig_map_flux_om_sig_map_fkey FOREIGN KEY (om_sig_map) REFERENCES om_sig_map(om_sig_map);


--
-- Name: om_sig_map_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_map
    ADD CONSTRAINT om_sig_map_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_sig_map_om_sig_extent_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_map
    ADD CONSTRAINT om_sig_map_om_sig_extent_fkey FOREIGN KEY (om_sig_extent) REFERENCES om_sig_extent(om_sig_extent);


--
-- Name: om_sig_map_om_sig_map_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sig_map
    ADD CONSTRAINT om_sig_map_om_sig_map_fkey FOREIGN KEY (source_flux) REFERENCES om_sig_map(om_sig_map);


--
-- Name: om_sousetat_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_sousetat
    ADD CONSTRAINT om_sousetat_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_utilisateur_om_collectivite_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_utilisateur
    ADD CONSTRAINT om_utilisateur_om_collectivite_fkey FOREIGN KEY (om_collectivite) REFERENCES om_collectivite(om_collectivite);


--
-- Name: om_utilisateur_om_profil_fkey; Type: FK CONSTRAINT; Schema: openexemple; Owner: -
--

ALTER TABLE ONLY om_utilisateur
    ADD CONSTRAINT om_utilisateur_om_profil_fkey FOREIGN KEY (om_profil) REFERENCES om_profil(om_profil);


--
-- PostgreSQL database dump complete
--

