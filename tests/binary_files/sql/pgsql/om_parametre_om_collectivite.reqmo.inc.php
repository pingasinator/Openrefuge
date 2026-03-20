<?php
//$Id: om_parametre_om_collectivite.reqmo.inc.php 1138 2012-03-20 14:24:37Z atreal $ 
//gen openMairie le 20/03/2012 14:21 
$reqmo['libelle']=_('reqmo-libelle-om_parametre');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_parametre');
$ent=_('om_parametre_om_collectivite');
$reqmo['sql']="select  [om_parametre], [libelle], [valeur] from ".DB_PREFIXE."om_parametre where om_collectivite = '[om_collectivite]' order by [tri]";
$reqmo['om_parametre']='checked';
$reqmo['libelle']='checked';
$reqmo['valeur']='checked';
$reqmo['om_collectivite']="select * from ".DB_PREFIXE."om_collectivite";
$reqmo['tri']=array('om_parametre','libelle','valeur');
