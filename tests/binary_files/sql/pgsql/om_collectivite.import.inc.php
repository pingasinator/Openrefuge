<?php
//$Id: om_collectivite.import.inc.php 1080 2012-02-25 15:33:52Z atreal $ 
//gen openMairie le 17/11/2010 18:34 
$import= "Insertion dans la table om_collectivite voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_collectivite";
$id='om_collectivite'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$DEBUG=0; // =0 pas d affichage messages / =1 affichage detail enregistrement
$fic_erreur=1; // =0 pas de fichier d erreur / =1  fichier erreur
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
$obligatoire['om_collectivite']=1;// obligatoire = 1
// * champ = om_collectivite
$zone['om_collectivite']='0';
// $defaut['om_collectivite']='***'; // *** par defaut si non renseigne
// * champ = libelle
$zone['libelle']='1';
// $defaut['libelle']='***'; // *** par defaut si non renseigne
// * champ = niveau
$zone['niveau']='2';
// $defaut['niveau']='***'; // *** par defaut si non renseigne
