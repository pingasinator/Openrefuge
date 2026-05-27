#! /bin/bash
##
# Ce script permet de générer les fichiers sql d'initialisation de la base de
# données pour permettre de publier une nouvelle version facilement
#
# @package framework_openmairie
# @version SVN : $Id: make_init.sh 4348 2018-07-20 16:49:26Z softime $
##

schema="framework_openmairie"
database="framework_openmairie"

# Génération du fichier init.sql
sudo su postgres -c "pg_dump --column-inserts -s -O -n $schema -t $schema.om_* $database" > ../../core/data/pgsql/init.sql

# Suppression du schéma et des instructions
sed -i "s/CREATE SCHEMA $schema;/-- CREATE SCHEMA $schema;/g" ../../core/data/pgsql/init*.sql
sed -i "s/^SET/-- SET/g" ../../core/data/pgsql/init*.sql

