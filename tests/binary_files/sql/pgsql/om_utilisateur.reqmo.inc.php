<?php
//$Id: om_utilisateur.reqmo.inc.php 1122 2012-03-16 18:36:30Z atreal $ 
//gen openMairie le 16/03/2012 19:16 
$reqmo['libelle']=_('public.om_utilisateur');
$reqmo['reqmo_libelle']=_('public.om_utilisateur');
$reqmo['sql']="select  [om_utilisateur], [nom], [email], [login], [pwd], [om_profil], [om_collectivite], [om_type] from ".DB_PREFIXE."om_utilisateur  order by [tri]";
$reqmo['om_utilisateur']='checked';
$reqmo['nom']='checked';
$reqmo['email']='checked';
$reqmo['login']='checked';
$reqmo['pwd']='checked';
$reqmo['om_profil']='checked';
$reqmo['om_collectivite']='checked';
$reqmo['om_type']='checked';
$reqmo['tri']=array('om_utilisateur','nom','email','login','pwd','om_profil','om_collectivite','om_type');
