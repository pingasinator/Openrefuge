<?php
//$Id$ 
//gen openMairie le 02/12/2025 14:25

$reqmo['libelle'] = __('reqmo-libelle-animal');
$reqmo['reqmo_libelle'] = __('reqmo-libelle-animal');
$ent = __('animal_soins');
$reqmo['sql']="select  [nom], [race], [date_de_naissance], [animal], [personne], [pension], [refuge], [prestation], [espece], [sexe] from ".DB_PREFIXE."animal where soins = '[soins]' order by [tri]";
$reqmo['nom']='checked';
$reqmo['race']='checked';
$reqmo['date_de_naissance']='checked';
$reqmo['animal']='checked';
$reqmo['personne']='checked';
$reqmo['pension']='checked';
$reqmo['refuge']='checked';
$reqmo['prestation']='checked';
$reqmo['espece']='checked';
$reqmo['sexe']='checked';
$reqmo['soins']="select * from ".DB_PREFIXE."soins";
$reqmo['tri']=array('nom','race','date_de_naissance','animal','personne','pension','refuge','prestation','espece','sexe');
