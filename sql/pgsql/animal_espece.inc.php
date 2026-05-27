<?php
//$Id$ 
//gen openMairie le 12/03/2026 15:32

include "../gen/sql/pgsql/animal_espece.inc.php";

$ent = __("application")." -> ".__("Espèces");
$tab_title = _("Espèces");

$champAffiche = array(
    'animal_espece.animal_espece as "'.__("ID").'"',
    'animal_espece.nom as "'.__("nom").'"'
);