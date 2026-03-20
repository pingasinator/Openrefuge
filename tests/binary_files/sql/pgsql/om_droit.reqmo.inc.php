<?php
//$Id: om_droit.reqmo.inc.php 1122 2012-03-16 18:36:30Z atreal $ 
//gen openMairie le 16/03/2012 19:13 
$reqmo['libelle']=_('public.om_droit');
$reqmo['reqmo_libelle']=_('public.om_droit');
$reqmo['sql']="select  [om_droit], [om_profil] from ".DB_PREFIXE."om_droit  order by [tri]";
$reqmo['om_droit']='checked';
$reqmo['om_profil']='checked';
$reqmo['tri']=array('om_droit','om_profil');
