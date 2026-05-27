<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_map_comp.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_sig_map_comp.inc.php")) {
    include "../gen/sql/pgsql/om_sig_map_comp.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_sig_map_comp.inc.php";
}

// Onglets
$tab_title = __("géométrie");

//
$champAffiche = array(
    'om_sig_map_comp.om_sig_map_comp as "'.__("n°").'"',
    'om_sig_map.id as "'.__("carte").'"',
    'om_sig_map_comp.libelle as "'.__("libellé").'"',
    'om_sig_map_comp.obj_class as "'.__("Obj.").'"',
    'om_sig_map_comp.ordre as "'.__("ordre").'"',
    "case om_sig_map_comp.actif when 't' then 'Oui' else 'Non' end as \"".__("Act.")."\"",
    "case om_sig_map_comp.comp_maj when 't' then 'Oui' else 'Non' end as \"".__("Maj.")."\"",
    'om_sig_map_comp.comp_table_update||\'.\'||om_sig_map_comp.comp_champ_idx||\'(\'||om_sig_map_comp.comp_champ||\'->\'||om_sig_map_comp.type_geometrie||\')\' as "'.__("Req.").'"',
);

//
$tri = "ORDER BY om_sig_map_comp.ordre ASC NULLS LAST";
