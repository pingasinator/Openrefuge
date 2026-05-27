<?php
//$Id$ 
//gen openMairie le 23/04/2026 10:41

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class soin_gen extends dbform {

    protected $_absolute_class_name = "soin";

    var $table = "soin";
    var $clePrimaire = "soin";
    var $typeCle = "N";
    var $required_field = array(
        "soin"
    );
    
    var $foreign_keys_extended = array(
        "animal" => array("animal", ),
        "clinique" => array("clinique", ),
        "soin_type" => array("soin_type", ),
        "veterinaire" => array("veterinaire", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("date_soin");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "soin",
            "date_soin",
            "description",
            "posologie",
            "veterinaire",
            "animal",
            "clinique",
            "soin_type",
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
    function get_var_sql_forminc__sql_clinique() {
        return "SELECT clinique.clinique, clinique.nom FROM ".DB_PREFIXE."clinique ORDER BY clinique.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_clinique_by_id() {
        return "SELECT clinique.clinique, clinique.nom FROM ".DB_PREFIXE."clinique WHERE clinique = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_soin_type() {
        return "SELECT soin_type.soin_type, soin_type.libelle FROM ".DB_PREFIXE."soin_type ORDER BY soin_type.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_soin_type_by_id() {
        return "SELECT soin_type.soin_type, soin_type.libelle FROM ".DB_PREFIXE."soin_type WHERE soin_type = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_veterinaire() {
        return "SELECT veterinaire.veterinaire, veterinaire.nom FROM ".DB_PREFIXE."veterinaire ORDER BY veterinaire.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_veterinaire_by_id() {
        return "SELECT veterinaire.veterinaire, veterinaire.nom FROM ".DB_PREFIXE."veterinaire WHERE veterinaire = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['soin'])) {
            $this->valF['soin'] = ""; // -> requis
        } else {
            $this->valF['soin'] = $val['soin'];
        }
        if ($val['date_soin'] != "") {
            $this->valF['date_soin'] = $this->dateDB($val['date_soin']);
        } else {
            $this->valF['date_soin'] = NULL;
        }
            $this->valF['description'] = $val['description'];
        if ($val['posologie'] == "") {
            $this->valF['posologie'] = NULL;
        } else {
            $this->valF['posologie'] = $val['posologie'];
        }
        if (!is_numeric($val['veterinaire'])) {
            $this->valF['veterinaire'] = NULL;
        } else {
            $this->valF['veterinaire'] = $val['veterinaire'];
        }
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = NULL;
        } else {
            $this->valF['animal'] = $val['animal'];
        }
        if (!is_numeric($val['clinique'])) {
            $this->valF['clinique'] = NULL;
        } else {
            $this->valF['clinique'] = $val['clinique'];
        }
        if (!is_numeric($val['soin_type'])) {
            $this->valF['soin_type'] = NULL;
        } else {
            $this->valF['soin_type'] = $val['soin_type'];
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
            $form->setType("soin", "hidden");
            $form->setType("date_soin", "date");
            $form->setType("description", "textarea");
            $form->setType("posologie", "text");
            if ($this->is_in_context_of_foreign_key("veterinaire", $this->retourformulaire)) {
                $form->setType("veterinaire", "selecthiddenstatic");
            } else {
                $form->setType("veterinaire", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            if ($this->is_in_context_of_foreign_key("clinique", $this->retourformulaire)) {
                $form->setType("clinique", "selecthiddenstatic");
            } else {
                $form->setType("clinique", "select");
            }
            if ($this->is_in_context_of_foreign_key("soin_type", $this->retourformulaire)) {
                $form->setType("soin_type", "selecthiddenstatic");
            } else {
                $form->setType("soin_type", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("soin", "hiddenstatic");
            $form->setType("date_soin", "date");
            $form->setType("description", "textarea");
            $form->setType("posologie", "text");
            if ($this->is_in_context_of_foreign_key("veterinaire", $this->retourformulaire)) {
                $form->setType("veterinaire", "selecthiddenstatic");
            } else {
                $form->setType("veterinaire", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            if ($this->is_in_context_of_foreign_key("clinique", $this->retourformulaire)) {
                $form->setType("clinique", "selecthiddenstatic");
            } else {
                $form->setType("clinique", "select");
            }
            if ($this->is_in_context_of_foreign_key("soin_type", $this->retourformulaire)) {
                $form->setType("soin_type", "selecthiddenstatic");
            } else {
                $form->setType("soin_type", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("soin", "hiddenstatic");
            $form->setType("date_soin", "hiddenstatic");
            $form->setType("description", "hiddenstatic");
            $form->setType("posologie", "hiddenstatic");
            $form->setType("veterinaire", "selectstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("clinique", "selectstatic");
            $form->setType("soin_type", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("soin", "static");
            $form->setType("date_soin", "datestatic");
            $form->setType("description", "textareastatic");
            $form->setType("posologie", "static");
            $form->setType("veterinaire", "selectstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("clinique", "selectstatic");
            $form->setType("soin_type", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('soin','VerifNum(this)');
        $form->setOnchange('date_soin','fdate(this)');
        $form->setOnchange('veterinaire','VerifNum(this)');
        $form->setOnchange('animal','VerifNum(this)');
        $form->setOnchange('clinique','VerifNum(this)');
        $form->setOnchange('soin_type','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("soin", 11);
        $form->setTaille("date_soin", 12);
        $form->setTaille("description", 80);
        $form->setTaille("posologie", 10);
        $form->setTaille("veterinaire", 11);
        $form->setTaille("animal", 11);
        $form->setTaille("clinique", 11);
        $form->setTaille("soin_type", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("soin", 11);
        $form->setMax("date_soin", 12);
        $form->setMax("description", 6);
        $form->setMax("posologie", -5);
        $form->setMax("veterinaire", 11);
        $form->setMax("animal", 11);
        $form->setMax("clinique", 11);
        $form->setMax("soin_type", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('soin', __('soin'));
        $form->setLib('date_soin', __('date_soin'));
        $form->setLib('description', __('description'));
        $form->setLib('posologie', __('posologie'));
        $form->setLib('veterinaire', __('veterinaire'));
        $form->setLib('animal', __('animal'));
        $form->setLib('clinique', __('clinique'));
        $form->setLib('soin_type', __('soin_type'));
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
        // clinique
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "clinique",
            $this->get_var_sql_forminc__sql("clinique"),
            $this->get_var_sql_forminc__sql("clinique_by_id"),
            false
        );
        // soin_type
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "soin_type",
            $this->get_var_sql_forminc__sql("soin_type"),
            $this->get_var_sql_forminc__sql("soin_type_by_id"),
            false
        );
        // veterinaire
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "veterinaire",
            $this->get_var_sql_forminc__sql("veterinaire"),
            $this->get_var_sql_forminc__sql("veterinaire_by_id"),
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
            if($this->is_in_context_of_foreign_key('clinique', $this->retourformulaire))
                $form->setVal('clinique', $idxformulaire);
            if($this->is_in_context_of_foreign_key('soin_type', $this->retourformulaire))
                $form->setVal('soin_type', $idxformulaire);
            if($this->is_in_context_of_foreign_key('veterinaire', $this->retourformulaire))
                $form->setVal('veterinaire', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    
    /**
     * Methode clesecondaire
     */
    function cleSecondaire($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        // On appelle la methode de la classe parent
        parent::cleSecondaire($id);
        // Verification de la cle secondaire : medicament
        $this->rechercheTable($this->f->db, "medicament", "soin", $id);
    }


}
