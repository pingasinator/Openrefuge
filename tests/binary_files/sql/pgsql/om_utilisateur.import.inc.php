<?php
//$Id$ 
//gen openMairie le 01/09/2016 14:40

$import= "Insertion dans la table om_utilisateur voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_utilisateur";
$id='om_utilisateur'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_utilisateur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "nom" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "30",
    ),
    "email" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "login" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "30",
    ),
    "pwd" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "om_collectivite" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_collectivite",
            "foreign_column_name" => "om_collectivite",
            "sql_exist" => "select * from ".DB_PREFIXE."om_collectivite where om_collectivite = '",
        ),
    ),
    "om_type" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "om_profil" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_profil",
            "foreign_column_name" => "om_profil",
            "sql_exist" => "select * from ".DB_PREFIXE."om_profil where om_profil = '",
        ),
    ),
);
