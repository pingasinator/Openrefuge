<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_sig_map_comp_gen extends dbform {

    protected $_absolute_class_name = "om_sig_map_comp";

    var $table = "om_sig_map_comp";
    var $clePrimaire = "om_sig_map_comp";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "obj_class",
        "om_sig_map",
        "om_sig_map_comp",
        "ordre"
    );
    
    var $foreign_keys_extended = array(
        "om_sig_map" => array("om_sig_map", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("libelle");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_sig_map_comp",
            "om_sig_map",
            "libelle",
            "ordre",
            "actif",
            "comp_maj",
            "type_geometrie",
            "comp_table_update",
            "comp_champ",
            "comp_champ_idx",
            "obj_class",
        );
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
        if (!is_numeric($val['om_sig_map_comp'])) {
            $this->valF['om_sig_map_comp'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map_comp'] = $val['om_sig_map_comp'];
        }
        if (!is_numeric($val['om_sig_map'])) {
            $this->valF['om_sig_map'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map'] = $val['om_sig_map'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if (!is_numeric($val['ordre'])) {
            $this->valF['ordre'] = ""; // -> requis
        } else {
            $this->valF['ordre'] = $val['ordre'];
        }
        if ($val['actif'] == 1 || $val['actif'] == "t" || $val['actif'] == "Oui") {
            $this->valF['actif'] = true;
        } else {
            $this->valF['actif'] = false;
        }
        if ($val['comp_maj'] == 1 || $val['comp_maj'] == "t" || $val['comp_maj'] == "Oui") {
            $this->valF['comp_maj'] = true;
        } else {
            $this->valF['comp_maj'] = false;
        }
        if ($val['type_geometrie'] == "") {
            $this->valF['type_geometrie'] = NULL;
        } else {
            $this->valF['type_geometrie'] = $val['type_geometrie'];
        }
        if ($val['comp_table_update'] == "") {
            $this->valF['comp_table_update'] = NULL;
        } else {
            $this->valF['comp_table_update'] = $val['comp_table_update'];
        }
        if ($val['comp_champ'] == "") {
            $this->valF['comp_champ'] = NULL;
        } else {
            $this->valF['comp_champ'] = $val['comp_champ'];
        }
        if ($val['comp_champ_idx'] == "") {
            $this->valF['comp_champ_idx'] = NULL;
        } else {
            $this->valF['comp_champ_idx'] = $val['comp_champ_idx'];
        }
        $this->valF['obj_class'] = $val['obj_class'];
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
            $form->setType("om_sig_map_comp", "hidden");
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("libelle", "text");
            $form->setType("ordre", "text");
            $form->setType("actif", "checkbox");
            $form->setType("comp_maj", "checkbox");
            $form->setType("type_geometrie", "text");
            $form->setType("comp_table_update", "text");
            $form->setType("comp_champ", "text");
            $form->setType("comp_champ_idx", "text");
            $form->setType("obj_class", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_sig_map_comp", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("libelle", "text");
            $form->setType("ordre", "text");
            $form->setType("actif", "checkbox");
            $form->setType("comp_maj", "checkbox");
            $form->setType("type_geometrie", "text");
            $form->setType("comp_table_update", "text");
            $form->setType("comp_champ", "text");
            $form->setType("comp_champ_idx", "text");
            $form->setType("obj_class", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_sig_map_comp", "hiddenstatic");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("ordre", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("comp_maj", "hiddenstatic");
            $form->setType("type_geometrie", "hiddenstatic");
            $form->setType("comp_table_update", "hiddenstatic");
            $form->setType("comp_champ", "hiddenstatic");
            $form->setType("comp_champ_idx", "hiddenstatic");
            $form->setType("obj_class", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_sig_map_comp", "static");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("libelle", "static");
            $form->setType("ordre", "static");
            $form->setType("actif", "checkboxstatic");
            $form->setType("comp_maj", "checkboxstatic");
            $form->setType("type_geometrie", "static");
            $form->setType("comp_table_update", "static");
            $form->setType("comp_champ", "static");
            $form->setType("comp_champ_idx", "static");
            $form->setType("obj_class", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_map_comp','VerifNum(this)');
        $form->setOnchange('om_sig_map','VerifNum(this)');
        $form->setOnchange('ordre','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_map_comp", 11);
        $form->setTaille("om_sig_map", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("ordre", 11);
        $form->setTaille("actif", 1);
        $form->setTaille("comp_maj", 1);
        $form->setTaille("type_geometrie", 30);
        $form->setTaille("comp_table_update", 30);
        $form->setTaille("comp_champ", 30);
        $form->setTaille("comp_champ_idx", 30);
        $form->setTaille("obj_class", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_map_comp", 11);
        $form->setMax("om_sig_map", 11);
        $form->setMax("libelle", 50);
        $form->setMax("ordre", 11);
        $form->setMax("actif", 1);
        $form->setMax("comp_maj", 1);
        $form->setMax("type_geometrie", 30);
        $form->setMax("comp_table_update", 30);
        $form->setMax("comp_champ", 30);
        $form->setMax("comp_champ_idx", 30);
        $form->setMax("obj_class", 100);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_map_comp', __('om_sig_map_comp'));
        $form->setLib('om_sig_map', __('om_sig_map'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('ordre', __('ordre'));
        $form->setLib('actif', __('actif'));
        $form->setLib('comp_maj', __('comp_maj'));
        $form->setLib('type_geometrie', __('type_geometrie'));
        $form->setLib('comp_table_update', __('comp_table_update'));
        $form->setLib('comp_champ', __('comp_champ'));
        $form->setLib('comp_champ_idx', __('comp_champ_idx'));
        $form->setLib('obj_class', __('obj_class'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

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
            if($this->is_in_context_of_foreign_key('om_sig_map', $this->retourformulaire))
                $form->setVal('om_sig_map', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
