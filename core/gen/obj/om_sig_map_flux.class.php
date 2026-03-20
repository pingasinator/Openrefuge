<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_sig_map_flux_gen extends dbform {

    protected $_absolute_class_name = "om_sig_map_flux";

    var $table = "om_sig_map_flux";
    var $clePrimaire = "om_sig_map_flux";
    var $typeCle = "N";
    var $required_field = array(
        "ol_map",
        "om_sig_flux",
        "om_sig_map",
        "om_sig_map_flux",
        "ordre"
    );
    
    var $foreign_keys_extended = array(
        "om_sig_flux" => array("om_sig_flux", ),
        "om_sig_map" => array("om_sig_map", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("om_sig_flux");
    }

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
            "ordre",
            "visibility",
            "panier",
            "pa_nom",
            "pa_layer",
            "pa_attribut",
            "pa_encaps",
            "pa_sql",
            "pa_type_geometrie",
            "sql_filter",
            "baselayer",
            "singletile",
            "maxzoomlevel",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_flux() {
        return "SELECT om_sig_flux.om_sig_flux, om_sig_flux.libelle FROM ".DB_PREFIXE."om_sig_flux ORDER BY om_sig_flux.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_flux_by_id() {
        return "SELECT om_sig_flux.om_sig_flux, om_sig_flux.libelle FROM ".DB_PREFIXE."om_sig_flux WHERE om_sig_flux = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_map() {
        return "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map ORDER BY om_sig_map.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_map_by_id() {
        return "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_map_flux'])) {
            $this->valF['om_sig_map_flux'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map_flux'] = $val['om_sig_map_flux'];
        }
        if (!is_numeric($val['om_sig_flux'])) {
            $this->valF['om_sig_flux'] = ""; // -> requis
        } else {
            $this->valF['om_sig_flux'] = $val['om_sig_flux'];
        }
        if (!is_numeric($val['om_sig_map'])) {
            $this->valF['om_sig_map'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map'] = $val['om_sig_map'];
        }
        $this->valF['ol_map'] = $val['ol_map'];
        if (!is_numeric($val['ordre'])) {
            $this->valF['ordre'] = ""; // -> requis
        } else {
            $this->valF['ordre'] = $val['ordre'];
        }
        if ($val['visibility'] == 1 || $val['visibility'] == "t" || $val['visibility'] == "Oui") {
            $this->valF['visibility'] = true;
        } else {
            $this->valF['visibility'] = false;
        }
        if ($val['panier'] == 1 || $val['panier'] == "t" || $val['panier'] == "Oui") {
            $this->valF['panier'] = true;
        } else {
            $this->valF['panier'] = false;
        }
        if ($val['pa_nom'] == "") {
            $this->valF['pa_nom'] = NULL;
        } else {
            $this->valF['pa_nom'] = $val['pa_nom'];
        }
        if ($val['pa_layer'] == "") {
            $this->valF['pa_layer'] = NULL;
        } else {
            $this->valF['pa_layer'] = $val['pa_layer'];
        }
        if ($val['pa_attribut'] == "") {
            $this->valF['pa_attribut'] = NULL;
        } else {
            $this->valF['pa_attribut'] = $val['pa_attribut'];
        }
        if ($val['pa_encaps'] == "") {
            $this->valF['pa_encaps'] = NULL;
        } else {
            $this->valF['pa_encaps'] = $val['pa_encaps'];
        }
            $this->valF['pa_sql'] = $val['pa_sql'];
        if ($val['pa_type_geometrie'] == "") {
            $this->valF['pa_type_geometrie'] = NULL;
        } else {
            $this->valF['pa_type_geometrie'] = $val['pa_type_geometrie'];
        }
            $this->valF['sql_filter'] = $val['sql_filter'];
        if ($val['baselayer'] == 1 || $val['baselayer'] == "t" || $val['baselayer'] == "Oui") {
            $this->valF['baselayer'] = true;
        } else {
            $this->valF['baselayer'] = false;
        }
        if ($val['singletile'] == 1 || $val['singletile'] == "t" || $val['singletile'] == "Oui") {
            $this->valF['singletile'] = true;
        } else {
            $this->valF['singletile'] = false;
        }
        if (!is_numeric($val['maxzoomlevel'])) {
            $this->valF['maxzoomlevel'] = NULL;
        } else {
            $this->valF['maxzoomlevel'] = $val['maxzoomlevel'];
        }
    }

    //=================================================
    //cle primaire automatique [automatic primary key]
    //==================================================

    function setId(&$dnu1 = null) {
    //numero automatique
        $this->valF[$this->clePrimaire] = $this->f->db->nextId(DB_PREFIXE.$this->table);
    }

    function setValFAjout($val = array()) {
    //numero automatique -> pas de controle ajout cle primaire
    }

    function verifierAjout($val = array(), &$dnu1 = null) {
    //numero automatique -> pas de verfication de cle primaire
    }

    //==========================
    // Formulaire  [form]
    //==========================
    /**
     *
     */
    function setType(&$form, $maj) {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud($maj);

        // MODE AJOUTER
        if ($maj == 0 || $crud == 'create') {
            $form->setType("om_sig_map_flux", "hidden");
            if ($this->is_in_context_of_foreign_key("om_sig_flux", $this->retourformulaire)) {
                $form->setType("om_sig_flux", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_flux", "select");
            }
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("ol_map", "text");
            $form->setType("ordre", "text");
            $form->setType("visibility", "checkbox");
            $form->setType("panier", "checkbox");
            $form->setType("pa_nom", "text");
            $form->setType("pa_layer", "text");
            $form->setType("pa_attribut", "text");
            $form->setType("pa_encaps", "text");
            $form->setType("pa_sql", "textarea");
            $form->setType("pa_type_geometrie", "text");
            $form->setType("sql_filter", "textarea");
            $form->setType("baselayer", "checkbox");
            $form->setType("singletile", "checkbox");
            $form->setType("maxzoomlevel", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_sig_map_flux", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_sig_flux", $this->retourformulaire)) {
                $form->setType("om_sig_flux", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_flux", "select");
            }
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("ol_map", "text");
            $form->setType("ordre", "text");
            $form->setType("visibility", "checkbox");
            $form->setType("panier", "checkbox");
            $form->setType("pa_nom", "text");
            $form->setType("pa_layer", "text");
            $form->setType("pa_attribut", "text");
            $form->setType("pa_encaps", "text");
            $form->setType("pa_sql", "textarea");
            $form->setType("pa_type_geometrie", "text");
            $form->setType("sql_filter", "textarea");
            $form->setType("baselayer", "checkbox");
            $form->setType("singletile", "checkbox");
            $form->setType("maxzoomlevel", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_sig_map_flux", "hiddenstatic");
            $form->setType("om_sig_flux", "selectstatic");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("ol_map", "hiddenstatic");
            $form->setType("ordre", "hiddenstatic");
            $form->setType("visibility", "hiddenstatic");
            $form->setType("panier", "hiddenstatic");
            $form->setType("pa_nom", "hiddenstatic");
            $form->setType("pa_layer", "hiddenstatic");
            $form->setType("pa_attribut", "hiddenstatic");
            $form->setType("pa_encaps", "hiddenstatic");
            $form->setType("pa_sql", "hiddenstatic");
            $form->setType("pa_type_geometrie", "hiddenstatic");
            $form->setType("sql_filter", "hiddenstatic");
            $form->setType("baselayer", "hiddenstatic");
            $form->setType("singletile", "hiddenstatic");
            $form->setType("maxzoomlevel", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_sig_map_flux", "static");
            $form->setType("om_sig_flux", "selectstatic");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("ol_map", "static");
            $form->setType("ordre", "static");
            $form->setType("visibility", "checkboxstatic");
            $form->setType("panier", "checkboxstatic");
            $form->setType("pa_nom", "static");
            $form->setType("pa_layer", "static");
            $form->setType("pa_attribut", "static");
            $form->setType("pa_encaps", "static");
            $form->setType("pa_sql", "textareastatic");
            $form->setType("pa_type_geometrie", "static");
            $form->setType("sql_filter", "textareastatic");
            $form->setType("baselayer", "checkboxstatic");
            $form->setType("singletile", "checkboxstatic");
            $form->setType("maxzoomlevel", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_map_flux','VerifNum(this)');
        $form->setOnchange('om_sig_flux','VerifNum(this)');
        $form->setOnchange('om_sig_map','VerifNum(this)');
        $form->setOnchange('ordre','VerifNum(this)');
        $form->setOnchange('maxzoomlevel','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_map_flux", 11);
        $form->setTaille("om_sig_flux", 11);
        $form->setTaille("om_sig_map", 11);
        $form->setTaille("ol_map", 30);
        $form->setTaille("ordre", 11);
        $form->setTaille("visibility", 1);
        $form->setTaille("panier", 1);
        $form->setTaille("pa_nom", 30);
        $form->setTaille("pa_layer", 30);
        $form->setTaille("pa_attribut", 30);
        $form->setTaille("pa_encaps", 10);
        $form->setTaille("pa_sql", 80);
        $form->setTaille("pa_type_geometrie", 30);
        $form->setTaille("sql_filter", 80);
        $form->setTaille("baselayer", 1);
        $form->setTaille("singletile", 1);
        $form->setTaille("maxzoomlevel", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_map_flux", 11);
        $form->setMax("om_sig_flux", 11);
        $form->setMax("om_sig_map", 11);
        $form->setMax("ol_map", 50);
        $form->setMax("ordre", 11);
        $form->setMax("visibility", 1);
        $form->setMax("panier", 1);
        $form->setMax("pa_nom", 50);
        $form->setMax("pa_layer", 50);
        $form->setMax("pa_attribut", 50);
        $form->setMax("pa_encaps", 3);
        $form->setMax("pa_sql", 6);
        $form->setMax("pa_type_geometrie", 30);
        $form->setMax("sql_filter", 6);
        $form->setMax("baselayer", 1);
        $form->setMax("singletile", 1);
        $form->setMax("maxzoomlevel", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_map_flux', __('om_sig_map_flux'));
        $form->setLib('om_sig_flux', __('om_sig_flux'));
        $form->setLib('om_sig_map', __('om_sig_map'));
        $form->setLib('ol_map', __('ol_map'));
        $form->setLib('ordre', __('ordre'));
        $form->setLib('visibility', __('visibility'));
        $form->setLib('panier', __('panier'));
        $form->setLib('pa_nom', __('pa_nom'));
        $form->setLib('pa_layer', __('pa_layer'));
        $form->setLib('pa_attribut', __('pa_attribut'));
        $form->setLib('pa_encaps', __('pa_encaps'));
        $form->setLib('pa_sql', __('pa_sql'));
        $form->setLib('pa_type_geometrie', __('pa_type_geometrie'));
        $form->setLib('sql_filter', __('sql_filter'));
        $form->setLib('baselayer', __('baselayer'));
        $form->setLib('singletile', __('singletile'));
        $form->setLib('maxzoomlevel', __('maxzoomlevel'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // om_sig_flux
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_sig_flux",
            $this->get_var_sql_forminc__sql("om_sig_flux"),
            $this->get_var_sql_forminc__sql("om_sig_flux_by_id"),
            false
        );
        // om_sig_map
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_sig_map",
            $this->get_var_sql_forminc__sql("om_sig_map"),
            $this->get_var_sql_forminc__sql("om_sig_map_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_sig_flux', $this->retourformulaire))
                $form->setVal('om_sig_flux', $idxformulaire);
            if($this->is_in_context_of_foreign_key('om_sig_map', $this->retourformulaire))
                $form->setVal('om_sig_map', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
