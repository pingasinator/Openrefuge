<?php
//$Id$ 
//gen openMairie le 13/11/2025 15:51

$reqmo['libelle'] = __('reqmo-libelle-refuge');
$reqmo['reqmo_libelle'] = __('reqmo-libelle-refuge');
$ent = __('refuge_animale');
$reqmo['sql']="select  [refuge_entré], [refuge_sortie], [adoption], [tarifs], [refuge] from ".DB_PREFIXE."refuge where animale = '[animale]' order by [tri]";
$reqmo['refuge_entré']='checked';
$reqmo['refuge_sortie']='checked';
$reqmo['adoption']='checked';
$reqmo['tarifs']='checked';
$reqmo['refuge']='checked';
$reqmo['animale']="select * from ".DB_PREFIXE."animale";
$reqmo['tri']=array('refuge_entré','refuge_sortie','adoption','tarifs','refuge');
