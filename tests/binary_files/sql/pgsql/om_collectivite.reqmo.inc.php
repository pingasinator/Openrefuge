<?php
//$Id: om_collectivite.reqmo.inc.php 1122 2012-03-16 18:36:30Z atreal $ 
//gen openMairie le 16/03/2012 19:17 
$reqmo['libelle']=_('public.om_collectivite');
$reqmo['reqmo_libelle']=_('public.om_collectivite');
$reqmo['sql']="select  [om_collectivite], [libelle], [niveau] from ".DB_PREFIXE."om_collectivite  order by [tri]";
$reqmo['om_collectivite']='checked';
$reqmo['libelle']='checked';
$reqmo['niveau']='checked';
$reqmo['tri']=array('om_collectivite','libelle','niveau');
