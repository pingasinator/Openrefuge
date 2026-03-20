<?php
//$Id$ 
//gen openMairie le 02/12/2025 15:38

$reqmo['libelle'] = __('reqmo-libelle-veterinaire');
$reqmo['reqmo_libelle'] = __('reqmo-libelle-veterinaire');
$ent = __('veterinaire_clinique_vet');
$reqmo['sql']="select  [veterinaire], [nom], [prénom], [telephone] from ".DB_PREFIXE."veterinaire where clinique_vet = '[clinique_vet]' order by [tri]";
$reqmo['veterinaire']='checked';
$reqmo['nom']='checked';
$reqmo['prénom']='checked';
$reqmo['telephone']='checked';
$reqmo['clinique_vet']="select * from ".DB_PREFIXE."clinique_vet";
$reqmo['tri']=array('veterinaire','nom','prénom','telephone');
