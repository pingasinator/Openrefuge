<?php
//$Id$ 
//gen openMairie le 16/03/2026 16:50

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class sejour_gen extends dbform {

    protected $_absolute_class_name = "sejour";

    var $table = "sejour";
    var $clePrimaire = "sejour";
    var $typeCle = "N";
    var $required_field = array(
        "sejour"
    );
    
    var $foreign_keys_extended = array(
        "animal" => array("animal", ),
        "hebergement" => array("hebergement", ),
        "provenance" => array("provenance", ),
        "sejour_tarif" => array("sejour_tarif", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("date_entree");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "sejour",
            "date_entree",
            "date_sortie",
            "paye",
            "animal",
            "provenance",
            "hebergement",
            "sejour_tarif",
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
    function get_var_sql_forminc__sql_hebergement() {
        return "SELECT hebergement.hebergement, hebergement.nom FROM ".DB_PREFIXE."hebergement ORDER BY hebergement.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_hebergement_by_id() {
        return "SELECT hebergement.hebergement, hebergement.nom FROM ".DB_PREFIXE."hebergement WHERE hebergement = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_provenance() {
        return "SELECT provenance.provenance, provenance.libelle FROM ".DB_PREFIXE."provenance ORDER BY provenance.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_provenance_by_id() {
        return "SELECT provenance.provenance, provenance.libelle FROM ".DB_PREFIXE."provenance WHERE provenance = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_sejour_tarif() {
        return "SELECT sejour_tarif.sejour_tarif, sejour_tarif.libelle FROM ".DB_PREFIXE."sejour_tarif ORDER BY sejour_tarif.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_sejour_tarif_by_id() {
        return "SELECT sejour_tarif.sejour_tarif, sejour_tarif.libelle FROM ".DB_PREFIXE."sejour_tarif WHERE sejour_tarif = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['sejour'])) {
            $this->valF['sejour'] = ""; // -> requis
        } else {
            $this->valF['sejour'] = $val['sejour'];
        }
        if ($val['date_entree'] != "") {
            $this->valF['date_entree'] = $this->dateDB($val['date_entree']);
        } else {
            $this->valF['date_entree'] = NULL;
        }
        if ($val['date_sortie'] != "") {
            $this->valF['date_sortie'] = $this->dateDB($val['date_sortie']);
        } else {
            $this->valF['date_sortie'] = NULL;
        }
        if ($val['paye'] == 1 || $val['paye'] == "t" || $val['paye'] == "Oui") {
            $this->valF['paye'] = true;
        } else {
            $this->valF['paye'] = false;
        }
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = NULL;
        } else {
            $this->valF['animal'] = $val['animal'];
        }
        if (!is_numeric($val['provenance'])) {
            $this->valF['provenance'] = NULL;
        } else {
            $this->valF['provenance'] = $val['provenance'];
        }
        if (!is_numeric($val['hebergement'])) {
            $this->valF['hebergement'] = NULL;
        } else {
            $this->valF['hebergement'] = $val['hebergement'];
        }
        if (!is_numeric($val['sejour_tarif'])) {
            $this->valF['sejour_tarif'] = NULL;
        } else {
            $this->valF['sejour_tarif'] = $val['sejour_tarif'];
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
            $form->setType("sejour", "hidden");
            $form->setType("date_entree", "date");
            $form->setType("date_sortie", "date");
            $form->setType("paye", "checkbox");
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            if ($this->is_in_context_of_foreign_key("provenance", $this->retourformulaire)) {
                $form->setType("provenance", "selecthiddenstatic");
            } else {
                $form->setType("provenance", "select");
            }
            if ($this->is_in_context_of_foreign_key("hebergement", $this->retourformulaire)) {
                $form->setType("hebergement", "selecthiddenstatic");
            } else {
                $form->setType("hebergement", "select");
            }
            if ($this->is_in_context_of_foreign_key("sejour_tarif", $this->retourformulaire)) {
                $form->setType("sejour_tarif", "selecthiddenstatic");
            } else {
                $form->setType("sejour_tarif", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("sejour", "hiddenstatic");
            $form->setType("date_entree", "date");
            $form->setType("date_sortie", "date");
            $form->setType("paye", "checkbox");
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            if ($this->is_in_context_of_foreign_key("provenance", $this->retourformulaire)) {
                $form->setType("provenance", "selecthiddenstatic");
            } else {
                $form->setType("provenance", "select");
            }
            if ($this->is_in_context_of_foreign_key("hebergement", $this->retourformulaire)) {
                $form->setType("hebergement", "selecthiddenstatic");
            } else {
                $form->setType("hebergement", "select");
            }
            if ($this->is_in_context_of_foreign_key("sejour_tarif", $this->retourformulaire)) {
                $form->setType("sejour_tarif", "selecthiddenstatic");
            } else {
                $form->setType("sejour_tarif", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("sejour", "hiddenstatic");
            $form->setType("date_entree", "hiddenstatic");
            $form->setType("date_sortie", "hiddenstatic");
            $form->setType("paye", "hiddenstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("provenance", "selectstatic");
            $form->setType("hebergement", "selectstatic");
            $form->setType("sejour_tarif", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("sejour", "static");
            $form->setType("date_entree", "datestatic");
            $form->setType("date_sortie", "datestatic");
            $form->setType("paye", "checkboxstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("provenance", "selectstatic");
            $form->setType("hebergement", "selectstatic");
            $form->setType("sejour_tarif", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('sejour','VerifNum(this)');
        $form->setOnchange('date_entree','fdate(this)');
        $form->setOnchange('date_sortie','fdate(this)');
        $form->setOnchange('animal','VerifNum(this)');
        $form->setOnchange('provenance','VerifNum(this)');
        $form->setOnchange('hebergement','VerifNum(this)');
        $form->setOnchange('sejour_tarif','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("sejour", 11);
        $form->setTaille("date_entree", 12);
        $form->setTaille("date_sortie", 12);
        $form->setTaille("paye", 1);
        $form->setTaille("animal", 11);
        $form->setTaille("provenance", 11);
        $form->setTaille("hebergement", 11);
        $form->setTaille("sejour_tarif", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("sejour", 11);
        $form->setMax("date_entree", 12);
        $form->setMax("date_sortie", 12);
        $form->setMax("paye", 1);
        $form->setMax("animal", 11);
        $form->setMax("provenance", 11);
        $form->setMax("hebergement", 11);
        $form->setMax("sejour_tarif", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('sejour', __('sejour'));
        $form->setLib('date_entree', __('date_entree'));
        $form->setLib('date_sortie', __('date_sortie'));
        $form->setLib('paye', __('paye'));
        $form->setLib('animal', __('animal'));
        $form->setLib('provenance', __('provenance'));
        $form->setLib('hebergement', __('hebergement'));
        $form->setLib('sejour_tarif', __('sejour_tarif'));
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
        // hebergement
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "hebergement",
            $this->get_var_sql_forminc__sql("hebergement"),
            $this->get_var_sql_forminc__sql("hebergement_by_id"),
            false
        );
        // provenance
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "provenance",
            $this->get_var_sql_forminc__sql("provenance"),
            $this->get_var_sql_forminc__sql("provenance_by_id"),
            false
        );
        // sejour_tarif
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "sejour_tarif",
            $this->get_var_sql_forminc__sql("sejour_tarif"),
            $this->get_var_sql_forminc__sql("sejour_tarif_by_id"),
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
            if($this->is_in_context_of_foreign_key('hebergement', $this->retourformulaire))
                $form->setVal('hebergement', $idxformulaire);
            if($this->is_in_context_of_foreign_key('provenance', $this->retourformulaire))
                $form->setVal('provenance', $idxformulaire);
            if($this->is_in_context_of_foreign_key('sejour_tarif', $this->retourformulaire))
                $form->setVal('sejour_tarif', $idxformulaire);
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
        // Verification de la cle secondaire : facture_sejour
        $this->rechercheTable($this->f->db, "facture_sejour", "sejour", $id);
    }


}
