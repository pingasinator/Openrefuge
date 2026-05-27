<?php

// Surcharge de la configuration du listing des lettres-types
// à des fins de test des actions direct en tableaux et sous-tableaux

include PATH_OPENMAIRIE."sql/pgsql/om_lettretype.inc.php";

if (isset($premier) === false) {
    $premier = '';
}
if (isset($advs_id) === false) {
    $advs_id = '';
}
if (isset($tricol) === false) {
    $tricol = '';
}
if (isset($valide) === false) {
    $valide = '';
}

$tab_actions['left']['copier'] = array(
    'lien' => OM_ROUTE_FORM.'&obj=om_lettretype&amp;action=4&amp;idx=',
    'id' => '&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=tab',
    'lib' => "<span class=\"om-icon om-icon-16 om-icon-fix copy-16\" title=\""._("Copier")."\">"._("Copier")."</span>",
    'type' => 'action-direct',
    'ordre' => 40,
);
