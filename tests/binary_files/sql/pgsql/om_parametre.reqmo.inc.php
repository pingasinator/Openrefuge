<?php
//$Id: om_parametre.reqmo.inc.php 1138 2012-03-20 14:24:37Z atreal $ 
//gen openMairie le 20/03/2012 14:21 
$reqmo['libelle']=_('reqmo-libelle-om_parametre');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_parametre');
$ent=_('om_parametre');
$reqmo['sql']="select  [om_parametre], [libelle], [valeur], [om_collectivite] from ".DB_PREFIXE."om_parametre  order by [tri]";
$reqmo['om_parametre']='checked';
$reqmo['libelle']='checked';
$reqmo['valeur']='checked';
$reqmo['om_collectivite']='checked';
$reqmo['tri']=array('om_parametre','libelle','valeur','om_collectivite');
