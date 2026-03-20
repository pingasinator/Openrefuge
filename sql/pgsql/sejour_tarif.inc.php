<?php
//$Id$ 
//gen openMairie le 18/02/2026 14:29

include "../gen/sql/pgsql/sejour_tarif.inc.php";

$ent = __("application")." -> Séjours -> ".__("Tarifs");
$tab_title = _("Tarifs");

$champAffiche = array(
    'sejour_tarif.sejour_tarif as "'.__("ID").'"',
    'sejour_tarif.libelle as "'.__("libellé").'"',
    'round(coalesce(sejour_tarif.prix,\'0\')::numeric,2) as "'.__("prix").'"',
);

$options[] = array(
	'type' => 'search',
	'display' => true,
	'absolute_object' => 'sejour_tarif',
	'export' => array("csv")
);