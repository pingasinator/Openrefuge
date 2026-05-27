<?php
//$Id$ 
//gen openMairie le 02/12/2025 16:13

$import= "Insertion dans la table veterinaire voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."veterinaire";
$id='veterinaire'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "veterinaire" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "20",
    ),
    "nom" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "-5",
    ),
    "prénom" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "-5",
    ),
    "telephone" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "-5",
    ),
    "clinique" => array(
        "notnull" => "",
        "type" => "int",
        "len" => "20",
    ),
);
