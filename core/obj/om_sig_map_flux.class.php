<?php
//$Id: om_sig_map_flux.class.php 4348 2018-07-20 16:49:26Z softime $
//gen openMairie le 10/04/2015 12:03

if (file_exists("../gen/obj/om_sig_map_flux.class.php")) {
    require_once "../gen/obj/om_sig_map_flux.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_sig_map_flux.class.php";
}

class om_sig_map_flux_core extends om_sig_map_flux_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_sig_map_flux",
            "om_sig_flux",
            "om_sig_map",
            "ol_map",
            "baselayer",
            "maxzoomlevel",
            "ordre",
            "visibility",
            "singletile",
            "panier",
            "pa_nom",
            "pa_layer",
            "pa_attribut",
            "pa_encaps",
            "pa_sql",
            "pa_type_geometrie",
            "sql_filter",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_flux() {
        return "SELECT om_sig_flux.om_sig_flux, om_sig_flux.libelle||' - '||CASE WHEN cache_type IS NULL OR cache_type='' THEN 'WMS' ELSE cache_type END||CASE WHEN length(cache_gfi_chemin||cache_gfi_couches) > 0 THEN '*' ELSE '.' END FROM ".DB_PREFIXE."om_sig_flux ORDER BY om_sig_flux.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_flux_by_id() {
        return "SELECT om_sig_flux.om_sig_flux, om_sig_flux.libelle||' - '||CASE WHEN cache_type IS NULL OR cache_type='' THEN 'WMS' ELSE cache_type END||CASE WHEN length(cache_gfi_chemin||cache_gfi_couches) > 0 THEN '*' ELSE '.' END FROM ".DB_PREFIXE."om_sig_flux WHERE om_sig_flux = <idx>";
    }

    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            if($this->retourformulaire=='om_sig_map') {
                $form->setType('om_sig_flux','select');
                $form->setType('om_sig_map', 'hidden');
            } else {
                $form->setType('om_sig_flux','hidden');
                $form->setType('om_sig_map', 'select');
            }
            $form->setType('visibility', 'checkbox');
            $form->setType('panier', 'checkbox');
            $form->setType('pa_type_geometrie','select');
            $form->setType('baselayer', 'checkbox');
            $form->setType('singletile', 'checkbox');
        }
        if ($maj == 2 or $maj == 3) {
            $form->setType('om_sig_flux','');
            $form->setType('pa_type_geometrie','selectstatic');
        }
    }

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('om_sig_flux',20);
        $form->setTaille('ol_map',50);
        $form->setTaille('ordre',4);
        $form->setTaille('pa_nom',20);
        $form->setTaille('pa_layer',50);
        $form->setTaille('pa_attribut',50);
        $form->setTaille('pa_encaps',3);
        $form->setTaille('pa_type_geometrie',30);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj);
        $form->setMax('ol_map',50);
        $form->setMax('ordre',4);
        $form->setMax('pa_nom',50);
        $form->setMax('pa_layer',50);
        $form->setMax('pa_attribut',50);
        $form->setMax('pa_encaps',3);
        $form->setMax('maxzoomlevel',3);
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('om_sig_flux', __('flux WMS : '));
        $form->setLib('ol_map', __('nom map OpenLayer : '));
        $form->setLib('baselayer', __('fond de carte :'));
        $form->setLib('maxzoomlevel', __('niveau de zoom maximum :'));
        $form->setLib('ordre', __('ordre : '));
        $form->setLib('visibility', __('visible par défaut :'));
        $form->setLib('singletile', __('singletile OL :'));
        $form->setLib('panier', __('panier :'));
        $form->setLib('pa_nom', __('nom du panier :'));
        $form->setLib('pa_layer', __('couche du panier :'));
        $form->setLib('pa_attribut', __('attribut de la couche du panier :'));
        $form->setLib('pa_encaps', __('caractère d\'encapsulation de valeur panier :'));
        $form->setLib('pa_sql', __('requète d\'union (&lst) du panier :'));
        $form->setLib('pa_type_geometrie', __('type de géometrie du panier :'));
        $form->setLib('sql_filter', __('requète de filtrage (&idx) :'));
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        //
        if(file_exists ("../dyn/var_sig.inc")) {
            include ("../dyn/var_sig.inc");
        }

        $type_geometrie[0] = array("","point","linestring","polygon","multipoint","multilinestring","multipolygon");
        $type_geometrie[1] = array("choisir le type de géométrie",'point','ligne','polygone','multipoint','multiligne','multipolygone');

        $form->setSelect("pa_type_geometrie",$type_geometrie);

        $k = 0;
        $om_sig_flux = "";
        foreach ($form->select['om_sig_flux'] as $elem) {
            while ($k <count($elem)) {
                if ($form->val['om_sig_flux'] == $form->select['om_sig_flux'][0][$k]) {
                    $om_sig_flux = substr($form->select['om_sig_flux'][1][$k],-4);
                    $k = count($elem);
                }
                $k++;
            }
        }
         if ($om_sig_flux == 'TCF.' || $om_sig_flux == 'SMT.') {
            $form->setLib('baselayer', __('fond de carte :'));
            $form->setLib('singletile', __('sans objet'));
            $form->setLib('panier', __('sans objet'));
            $form->setLib('pa_nom', __('sans objet'));
            $form->setLib('lib-pa_layer', __('sans objet'));
            $form->setLib('pa_attribut', __('sans objet'));
            $form->setLib('pa_encaps', __('sans objet'));
            $form->setLib('pa_sql', __('sans objet'));
            $form->setLib('pa_type_geometrie', __('sans objet'));
            $form->setLib('sql_filter', __('sans objet'));
         } else if ($om_sig_flux == 'IMP.' || $om_sig_flux == 'IMP*') {
            $form->setLib('baselayer', __('sans objet'));
            $form->setLib('singletile', __('sans objet'));
            $form->setLib('panier', __('sans objet'));
            $form->setLib('pa_nom', __('sans objet'));
            $form->setLib('pa_layer', __('sans objet'));
            $form->setLib('pa_attribut', __('sans objet'));
            $form->setLib('pa_encaps', __('sans objet'));
            $form->setLib('pa_sql', __('requète de titres (&idx, &user) :'));
            $form->setLib('pa_type_geometrie', __('sans objet'));
            $form->setLib('sql_filter', __('requète de filtrage (&idx):'));
        }

    }

    /**
     *
     */
    function setGroupe (&$form, $maj) {
        $form->setGroupe('baselayer','D');
        $form->setGroupe('maxzoomlevel','G');
        $form->setGroupe('ordre','F');
        $form->setGroupe('visibility','D');
        $form->setGroupe('singletile','F');
    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
        $form->setRegroupe('baselayer','D',' '.__('Paramètres généraux').' ', "collapsible");
        $form->setRegroupe('maxzoomlevel','G','');
        $form->setRegroupe('ordre','G','');
        $form->setRegroupe('visibility','G','');
        $form->setRegroupe('singletile','F','');

        $form->setRegroupe('panier','D',' '.__('Paramètres avancés').' ', "collapsible");
        $form->setRegroupe('pa_nom','G','');
        $form->setRegroupe('pa_layer','G','');
        $form->setRegroupe('pa_attribut','G','');
        $form->setRegroupe('pa_encaps','G','');
        $form->setRegroupe('pa_sql','G','');
        $form->setRegroupe('pa_type_geometrie','G','');
        $form->setRegroupe('sql_filter','F','');
    }

    /**
     *
     */
    function setOnchange(&$form,$maj){
        parent::setOnchange($form,$maj);
        $form->setOnchange("om_sig_flux",
        " var elt = document.getElementById('om_sig_flux');".
        " var choix = elt.options[elt.selectedIndex].text.substr(elt.options[elt.selectedIndex].text.length - 4);".
        " if (choix == 'TCF.' || choix == 'SMT.') { ".
        "   document.getElementById('lib-baselayer').innerHTML='fond de carte :';".
        "   document.getElementById('lib-singletile').innerHTML='sans objet';".
        "   document.getElementById('lib-panier').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_nom').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_layer').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_attribut').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_encaps').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_sql').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_type_geometrie').innerHTML='sans objet';".
        "   document.getElementById('lib-sql_filter').innerHTML='sans objet';".
        " } else if  (choix == 'IMP.' || choix == 'IMP*') { ".
        "   document.getElementById('lib-baselayer').innerHTML='sans objet';".
        "   document.getElementById('lib-singletile').innerHTML='sans objet';".
        "   document.getElementById('lib-panier').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_nom').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_layer').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_attribut').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_encaps').innerHTML='sans objet';".
        "   document.getElementById('lib-pa_sql').innerHTML='requète de titres (&idx, &user) :';".
        "   document.getElementById('lib-pa_type_geometrie').innerHTML='sans objet';".
        "   document.getElementById('lib-sql_filter').innerHTML='requète de filtrage (&idx):';".
        " } else { ".
        "   document.getElementById('lib-baselayer').innerHTML='fond de carte :';".
        "   document.getElementById('lib-singletile').innerHTML='singletile OL :';".
        "   document.getElementById('lib-panier').innerHTML='panier :';".
        "   document.getElementById('lib-pa_nom').innerHTML='nom du panier : ';".
        "   document.getElementById('lib-pa_layer').innerHTML='couche du panier : ';".
        "   document.getElementById('lib-pa_attribut').innerHTML='attribut de la couche du panier :';".
        "   document.getElementById('lib-pa_encaps').innerHTML='caractère d\'encapsulation de valeur panier :';".
        "   document.getElementById('lib-pa_sql').innerHTML='requète d\'union (&lst) du panier :';".
        "   document.getElementById('lib-pa_type_geometrie').innerHTML='type de géometrie du panier :';".
        "   document.getElementById('lib-sql_filter').innerHTML='requète de filtrage (&idx):';".
        " } ");
    }

    /**
     *
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        parent::verifier($val);
        //
        if($this->valF['baselayer'] == 'Oui' && $this->valF['maxzoomlevel'] == '') {
            $this->correct = false;
            $msg = __("niveau de zoom maximum obligatoire");
            $this->addToMessage($msg);
        }
        if($this->valF['panier'] == 'Oui' && ($this->valF['pa_nom'] == '' || $this->valF['pa_layer'] == '' || $this->valF['pa_attribut'] == '' || $this->valF['pa_sql'] == '' || $this->valF['pa_type_geometrie'] == '')) {
            $this->correct = false;
            $msg = __("Tous les champs de définitions du panier sont obligatoires à l'exception du caractère d'encapsulation");
            $this->addToMessage($msg);
        }
    }
}
