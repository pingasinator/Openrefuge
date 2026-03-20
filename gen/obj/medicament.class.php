<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:16

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class medicament_gen extends dbform {

    protected $_absolute_class_name = "medicament";

    var $table = "medicament";
    var $clePrimaire = "medicament";
    var $typeCle = "N";
    var $required_field = array(
        "medicament"
    );
    
    var $foreign_keys_extended = array(
        "animal" => array("animal", ),
        "soin" => array("soin", ),
        "unite_mesure" => array("unite_mesure", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("nom");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "medicament",
            "nom",
            "date_debut",
            "date_fin",
            "dose",
            "frequence",
            "unite_mesure",
            "soin",
            "animal",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal() {
        return "SELECT animal.animal, animal.nom FROM ".DB_PREFIXE."animal ORDER BY animal.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_by_id() {
        return "SELECT animal.animal, animal.nom FROM ".DB_PREFIXE."animal WHERE animal = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_soin() {
        return "SELECT soin.soin, soin.date_soin FROM ".DB_PREFIXE."soin ORDER BY soin.date_soin ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_soin_by_id() {
        return "SELECT soin.soin, soin.date_soin FROM ".DB_PREFIXE."soin WHERE soin = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_unite_mesure() {
        return "SELECT unite_mesure.unite_mesure, unite_mesure.libelle FROM ".DB_PREFIXE."unite_mesure ORDER BY unite_mesure.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_unite_mesure_by_id() {
        return "SELECT unite_mesure.unite_mesure, unite_mesure.libelle FROM ".DB_PREFIXE."unite_mesure WHERE unite_mesure = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['medicament'])) {
            $this->valF['medicament'] = ""; // -> requis
        } else {
            $this->valF['medicament'] = $val['medicament'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if ($val['date_debut'] != "") {
            $this->valF['date_debut'] = $this->dateDB($val['date_debut']);
        } else {
            $this->valF['date_debut'] = NULL;
        }
        if ($val['date_fin'] != "") {
            $this->valF['date_fin'] = $this->dateDB($val['date_fin']);
        } else {
            $this->valF['date_fin'] = NULL;
        }
        if (!is_numeric($val['dose'])) {
            $this->valF['dose'] = NULL;
        } else {
            $this->valF['dose'] = $val['dose'];
        }
        if ($val['frequence'] == "") {
            $this->valF['frequence'] = NULL;
        } else {
            $this->valF['frequence'] = $val['frequence'];
        }
        if (!is_numeric($val['unite_mesure'])) {
            $this->valF['unite_mesure'] = NULL;
        } else {
            $this->valF['unite_mesure'] = $val['unite_mesure'];
        }
        if (!is_numeric($val['soin'])) {
            $this->valF['soin'] = NULL;
        } else {
            $this->valF['soin'] = $val['soin'];
        }
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = NULL;
        } else {
            $this->valF['animal'] = $val['animal'];
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
            $form->setType("medicament", "hidden");
            $form->setType("nom", "text");
            $form->setType("date_debut", "date");
            $form->setType("date_fin", "date");
            $form->setType("dose", "text");
            $form->setType("frequence", "text");
            if ($this->is_in_context_of_foreign_key("unite_mesure", $this->retourformulaire)) {
                $form->setType("unite_mesure", "selecthiddenstatic");
            } else {
                $form->setType("unite_mesure", "select");
            }
            if ($this->is_in_context_of_foreign_key("soin", $this->retourformulaire)) {
                $form->setType("soin", "selecthiddenstatic");
            } else {
                $form->setType("soin", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("medicament", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("date_debut", "date");
            $form->setType("date_fin", "date");
            $form->setType("dose", "text");
            $form->setType("frequence", "text");
            if ($this->is_in_context_of_foreign_key("unite_mesure", $this->retourformulaire)) {
                $form->setType("unite_mesure", "selecthiddenstatic");
            } else {
                $form->setType("unite_mesure", "select");
            }
            if ($this->is_in_context_of_foreign_key("soin", $this->retourformulaire)) {
                $form->setType("soin", "selecthiddenstatic");
            } else {
                $form->setType("soin", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("medicament", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("date_debut", "hiddenstatic");
            $form->setType("date_fin", "hiddenstatic");
            $form->setType("dose", "hiddenstatic");
            $form->setType("frequence", "hiddenstatic");
            $form->setType("unite_mesure", "selectstatic");
            $form->setType("soin", "selectstatic");
            $form->setType("animal", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("medicament", "static");
            $form->setType("nom", "static");
            $form->setType("date_debut", "datestatic");
            $form->setType("date_fin", "datestatic");
            $form->setType("dose", "static");
            $form->setType("frequence", "static");
            $form->setType("unite_mesure", "selectstatic");
            $form->setType("soin", "selectstatic");
            $form->setType("animal", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('medicament','VerifNum(this)');
        $form->setOnchange('date_debut','fdate(this)');
        $form->setOnchange('date_fin','fdate(this)');
        $form->setOnchange('dose','VerifNum(this)');
        $form->setOnchange('unite_mesure','VerifNum(this)');
        $form->setOnchange('soin','VerifNum(this)');
        $form->setOnchange('animal','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("medicament", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("date_debut", 12);
        $form->setTaille("date_fin", 12);
        $form->setTaille("dose", 11);
        $form->setTaille("frequence", 10);
        $form->setTaille("unite_mesure", 11);
        $form->setTaille("soin", 11);
        $form->setTaille("animal", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("medicament", 11);
        $form->setMax("nom", -5);
        $form->setMax("date_debut", 12);
        $form->setMax("date_fin", 12);
        $form->setMax("dose", 11);
        $form->setMax("frequence", -5);
        $form->setMax("unite_mesure", 11);
        $form->setMax("soin", 11);
        $form->setMax("animal", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('medicament', __('medicament'));
        $form->setLib('nom', __('nom'));
        $form->setLib('date_debut', __('date_debut'));
        $form->setLib('date_fin', __('date_fin'));
        $form->setLib('dose', __('dose'));
        $form->setLib('frequence', __('frequence'));
        $form->setLib('unite_mesure', __('unite_mesure'));
        $form->setLib('soin', __('soin'));
        $form->setLib('animal', __('animal'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // animal
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "animal",
            $this->get_var_sql_forminc__sql("animal"),
            $this->get_var_sql_forminc__sql("animal_by_id"),
            false
        );
        // soin
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "soin",
            $this->get_var_sql_forminc__sql("soin"),
            $this->get_var_sql_forminc__sql("soin_by_id"),
            false
        );
        // unite_mesure
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "unite_mesure",
            $this->get_var_sql_forminc__sql("unite_mesure"),
            $this->get_var_sql_forminc__sql("unite_mesure_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('animal', $this->retourformulaire))
                $form->setVal('animal', $idxformulaire);
            if($this->is_in_context_of_foreign_key('soin', $this->retourformulaire))
                $form->setVal('soin', $idxformulaire);
            if($this->is_in_context_of_foreign_key('unite_mesure', $this->retourformulaire))
                $form->setVal('unite_mesure', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
