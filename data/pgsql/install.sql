--------------------------------------------------------------------------------
-- Script d'installation
--
-- ATTENTION ce script peut supprimer des données de votre base de données
-- il n'est à utiliser qu'en connaissance de cause
--
-- Usage :
-- cd data/pgsql/
-- dropdb framework_openmairie && createdb framework_openmairie && psql framework_openmairie -f install.sql
--
-- @package framework_openmairie
-- @version SVN : $Id: install.sql 4348 2018-07-20 16:49:26Z softime $
--------------------------------------------------------------------------------

-- Force l'encoding client à UTF8
SET client_encoding = 'UTF8';

-- Nom du schéma
\set schema 'openrefuge'

--
START TRANSACTION;

-- Initialisation de postgis
CREATE EXTENSION IF NOT EXISTS postgis;

-- Suppression, Création et Utilisation du schéma
DROP SCHEMA IF EXISTS :schema CASCADE;
CREATE SCHEMA :schema;
SET search_path = :schema, public, pg_catalog;

-- Instructions de base du framework openmairie
\i ../../core/data/pgsql/init.sql

-- Initialisation du paramétrage
\i ../../core/data/pgsql/init_permissions.sql
\i ../../core/data/pgsql/init_parametrage.sql
\i ../../core/data/pgsql/init_refuge.sql
\i ../../core/data/pgsql/init_refuge_data.sql
\i ../../core/data/pgsql/refuge_v2.sql
-- Mise à jour depuis la dernière version
-- A commenter/décommenter en cours de développement
-- \i ../../core/data/pgsql/v4.9.0.dev0.sql

--


COMMIT;