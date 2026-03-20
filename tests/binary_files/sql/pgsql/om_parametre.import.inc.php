<?php
//$Id: om_parametre.import.inc.php 1122 2012-03-16 18:36:30Z atreal $ 
//gen openMairie le 16/03/2012 19:25 
$import= "Insertion dans la table om_parametre voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_parametre";
$id='om_parametre'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$DEBUG=0; // =0 pas d affichage messages / =1 affichage detail enregistrement
$fic_erreur=1; // =0 pas de fichier d erreur / =1  fichier erreur
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
$obligatoire['om_parametre']=1;// obligatoire = 1
//* cle secondaire=om_collectivite
$exist['om_collectivite']=1;//  0=non / 1=oui
$sql_exist['om_collectivite']= "select * from ".DB_PREFIXE."om_collectivite where om_collectivite = '";
// * champ = om_parametre
$zone['om_parametre']='0';
// $defaut['om_parametre']='***'; // *** par defaut si non renseigne
// * champ = libelle
$zone['libelle']='1';
// $defaut['libelle']='***'; // *** par defaut si non renseigne
// * champ = valeur
$zone['valeur']='2';
// $defaut['valeur']='***'; // *** par defaut si non renseigne
// * champ = om_collectivite
$zone['om_collectivite']='3';
// $defaut['om_collectivite']='***'; // *** par defaut si non renseigne
