<?php
//$Id: om_profil.reqmo.inc.php 1122 2012-03-16 18:36:30Z atreal $ 
//gen openMairie le 16/03/2012 19:16 
$reqmo['libelle']=_('public.om_profil');
$reqmo['reqmo_libelle']=_('public.om_profil');
$reqmo['sql']="select  [om_profil], [libelle] from ".DB_PREFIXE."om_profil  order by [tri]";
$reqmo['om_profil']='checked';
$reqmo['libelle']='checked';
$reqmo['tri']=array('om_profil','libelle');
