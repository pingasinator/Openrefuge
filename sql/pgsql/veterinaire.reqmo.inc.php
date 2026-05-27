<?php
//$Id$ 
//gen openMairie le 02/12/2025 16:13

$reqmo['libelle'] = __('reqmo-libelle-veterinaire');
$reqmo['reqmo_libelle'] = __('reqmo-libelle-veterinaire');
$ent = __('veterinaire');
$reqmo['sql']="select  [veterinaire], [nom], [prénom], [telephone], [clinique] from ".DB_PREFIXE."veterinaire  order by [tri]";
$reqmo['veterinaire']='checked';
$reqmo['nom']='checked';
$reqmo['prénom']='checked';
$reqmo['telephone']='checked';
$reqmo['clinique']='checked';
$reqmo['tri']=array('veterinaire','nom','prénom','telephone','clinique');
