<?php
//$Id: om_sig_flux.class.php 4348 2018-07-20 16:49:26Z softime $
//gen openMairie le 10/04/2015 12:03

if (file_exists("../gen/obj/om_sig_flux.class.php")) {
    require_once "../gen/obj/om_sig_flux.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_sig_flux.class.php";
}

class om_sig_flux_core extends om_sig_flux_gen {

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
            "om_sig_flux",
            "libelle",
            "om_collectivite",
            "id",
            "attribution",
            "cache_type",
            "chemin",
            "couches",
            "cache_gfi_chemin",
            "cache_gfi_couches"
        );
    }

    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('om_sig_flux',4);
        $form->setTaille('om_collectivite',4);
        $form->setTaille('id',20);
        $form->setTaille('libelle',30);
        $form->setTaille('chemin',70);
        $form->setTaille('couches',70);
        $form->setTaille('cache_gfi_chemin',70);
        $form->setTaille('cache_gfi_couches',70);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj);
        $form->setMax('om_sig_flux',4);
        $form->setMax('om_collectivite',4);
        $form->setMax('id',50);
        $form->setMax('libelle',50);
        $form->setMax('chemin',6);
        $form->setMax('couches',3);
        $form->setMax('cache_gfi_chemin',6);
        $form->setMax('cache_gfi_couches',3);
    }

    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            $form->setType('cache_type','select');
            $form->setType('chemin','textarea');
            $form->setType('couches','textarea');
            $form->setType('cache_gfi_chemin','textarea');
            $form->setType('cache_gfi_couches','textarea');
        } else {
            $form->setType('chemin','textareastatic');
            $form->setType('couches','textareastatic');
            $form->setType('cache_gfi_chemin','textareastatic');
            $form->setType('cache_gfi_couches','textareastatic');
        }
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('libelle', __('libelle : '));
        $form->setLib('cache_type', __('Type de flux : '));
        $form->setLib('couches', __('couches (séparées par ,) : '));
        if ($form->val['cache_type'] == '') {
            $form->setLib('chemin', __('URL : '));
            $form->setLib('cache_gfi_chemin', __('sans objet'));
            $form->setLib('cache_gfi_couches', __('sans objet'));
        } else if ($form->val['cache_type'] == 'IMP') {
            $form->setLib('chemin', __('URL ([EXTENT], [LAYERS]) : '));
            $form->setLib('cache_gfi_chemin', __('largeur carte dans composeur x 2 : '));
            $form->setLib('cache_gfi_couches', __('hauteur carte dans composeur x 2 : '));
        } else {
            $form->setLib('chemin', __('URL : '));
            $form->setLib('cache_gfi_chemin', __('URL pour GetFeatureInfo : '));
            $form->setLib('cache_gfi_couches', __('couches pour GetFeatureInfo : '));
        }
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        //
        if($maj<2){
            $contenu_cache_type[0] = array("","TCF","SMT","IMP");
            $contenu_cache_type[1] = array("WMS",'Flux TileCache (via OpenLayers.layer.WMS)',"Slippy Map Tiles (type OSM)","Impression");
            $form->setSelect("cache_type",$contenu_cache_type);
        }
    }

    /**
     *
     */
    function setOnchange(&$form,$maj){
        parent::setOnchange($form,$maj);
        $form->setOnchange("cache_type",
        "if ( this.value=='') { ".
            "document.getElementById('lib-chemin').innerHTML='URL : ';".
            "document.getElementById('lib-cache_gfi_chemin').innerHTML='sans objet'; ".
            "document.getElementById('lib-cache_gfi_couches').innerHTML='sans objet'; ".
        "} else if ( this.value=='IMP') { ".
            "document.getElementById('lib-chemin').innerHTML='URL ([EXTENT], [LAYERS]) : ';".
            "document.getElementById('lib-cache_gfi_chemin').innerHTML='largeur carte dans composeur x 2 : '; ".
            "document.getElementById('lib-cache_gfi_couches').innerHTML='hauteur carte dans composeur x 2 : '; ".
        "} else { ".
            "document.getElementById('lib-chemin').innerHTML='URL : ';".
            "document.getElementById('lib-cache_gfi_chemin').innerHTML='URL pour GetFeatureInfo : '; ".
            "document.getElementById('lib-cache_gfi_couches').innerHTML='couches pour GetFeatureInfo : '; ".
        " }");
    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
        parent::setRegroupe ($form, $maj);
        $form->setRegroupe('chemin','D',' '.__('adresse').' ', "collapsible");
        $form->setRegroupe('couches','F','');

        $form->setRegroupe('cache_gfi_chemin','D',' '.__('paramètres').' ', "collapsible");
        $form->setRegroupe('cache_gfi_couches','F','');

    }

}
