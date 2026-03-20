<?php
//$Id$ 
//gen openMairie le 18/12/2025 09:41

include "../gen/sql/pgsql/factures_sejours.inc.php";

$champAffiche = array(
    'factures_sejours.factures_sejours as "'.__("factures_sejours").'"',
    'factures.factures as "'.__("factures").'"',
    'sejours.date_d_entree as "'.__("sejours").'"',
    'to_char(factures_sejours.date_d_entree ,\'DD/MM/YYYY\') as "'.__("date_d_entree").'"',
    'to_char(factures_sejours.date_de_sortie ,\'DD/MM/YYYY\') as "'.__("date_de_sortie").'"',
    "case factures_sejours.payee when 't' then 'Oui' else 'Non' end as \"".__("payee")."\"",
    'animale.race as "'.__("animale").'"',
    'provenance.provenance as "'.__("provenance").'"',
    'hebergement.adresse as "'.__("hebergement").'"',
    'factures_sejours.tarif as "'.__("tarifs").'"',
    );