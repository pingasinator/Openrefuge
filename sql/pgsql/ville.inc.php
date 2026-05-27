<?php
//$Id$ 
//gen openMairie le 10/03/2026 08:31

include "../gen/sql/pgsql/ville.inc.php";

$ent = __("application")." -> ".__("villes");
$tab_title = _("Villes");


$champAffiche = array(
    'ville.ville as "'.__("id").'"',
    'ville.nom as "'.__("nom").'"',
    'ville.code_postal as "'.__("code postal").'"',
    );