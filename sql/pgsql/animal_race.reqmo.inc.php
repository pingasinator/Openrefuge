<?php
//$Id$ 
//gen openMairie le 24/02/2026 08:32

$reqmo['libelle'] = __('reqmo-libelle-animal');
$reqmo['reqmo_libelle'] = __('reqmo-libelle-animal');
$ent = __('animal_race');
$reqmo['sql']="select  [animal], [nom], [date_de_naissance], [espece], [sexe], [personne] from ".DB_PREFIXE."animal where race = '[race]' order by [tri]";
$reqmo['animal']='checked';
$reqmo['nom']='checked';
$reqmo['date_de_naissance']='checked';
$reqmo['espece']='checked';
$reqmo['race']="select * from ".DB_PREFIXE."race";
$reqmo['sexe']='checked';
$reqmo['personne']='checked';
$reqmo['tri']=array('animal','nom','date_de_naissance','espece','sexe','personne');
