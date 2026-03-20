<?php
//$Id$ 
//gen openMairie le 16/12/2025 08:52

include PATH_OPENMAIRIE."sql/pgsql/om_utilisateur.inc.php";

$options[] = array('type' => 'search',
'display' => true,
'advanced' => $champs,
'default_form' => 'advanced',
'absolute_object' => 'om_utilisateur');