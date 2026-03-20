<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:09

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class hebergement_gen extends dbform {

    protected $_absolute_class_name = "hebergement";

    var $table = "hebergement";
    var $clePrimaire = "hebergement";
    var $typeCle = "N";
    var $required_field = array(
        "hebergement"
    );
    
    var $foreign_keys_extended = array(
        "hebergement_type" => array("hebergement_type", ),
        "ville" => array("ville", ),
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
            "hebergement",
            "nom",
            "adresse",
            "ville",
            "telephone",
            "hebergement_type",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_hebergement_type() {
        return "SELECT hebergement_type.hebergement_type, hebergement_type.libelle FROM ".DB_PREFIXE."hebergement_type ORDER BY hebergement_type.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_hebergement_type_by_id() {
        return "SELECT hebergement_type.hebergement_type, hebergement_type.libelle FROM ".DB_PREFIXE."hebergement_type WHERE hebergement_type = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_ville() {
        return "SELECT ville.ville, ville.nom FROM ".DB_PREFIXE."ville ORDER BY ville.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_ville_by_id() {
        return "SELECT ville.ville, ville.nom FROM ".DB_PREFIXE."ville WHERE ville = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['hebergement'])) {
            $this->valF['hebergement'] = ""; // -> requis
        } else {
            $this->valF['hebergement'] = $val['hebergement'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if ($val['adresse'] == "") {
            $this->valF['adresse'] = NULL;
        } else {
            $this->valF['adresse'] = $val['adresse'];
        }
        if (!is_numeric($val['ville'])) {
            $this->valF['ville'] = NULL;
        } else {
            $this->valF['ville'] = $val['ville'];
        }
        if ($val['telephone'] == "") {
            $this->valF['telephone'] = NULL;
        } else {
            $this->valF['telephone'] = $val['telephone'];
        }
        if (!is_numeric($val['hebergement_type'])) {
            $this->valF['hebergement_type'] = NULL;
        } else {
            $this->valF['hebergement_type'] = $val['hebergement_type'];
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
            $form->setType("hebergement", "hidden");
            $form->setType("nom", "text");
            $form->setType("adresse", "text");
            if ($this->is_in_context_of_foreign_key("ville", $this->retourformulaire)) {
                $form->setType("ville", "selecthiddenstatic");
            } else {
                $form->setType("ville", "select");
            }
            $form->setType("telephone", "text");
            if ($this->is_in_context_of_foreign_key("hebergement_type", $this->retourformulaire)) {
                $form->setType("hebergement_type", "selecthiddenstatic");
            } else {
                $form->setType("hebergement_type", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("hebergement", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("adresse", "text");
            if ($this->is_in_context_of_foreign_key("ville", $this->retourformulaire)) {
                $form->setType("ville", "selecthiddenstatic");
            } else {
                $form->setType("ville", "select");
            }
            $form->setType("telephone", "text");
            if ($this->is_in_context_of_foreign_key("hebergement_type", $this->retourformulaire)) {
                $form->setType("hebergement_type", "selecthiddenstatic");
            } else {
                $form->setType("hebergement_type", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("hebergement", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("adresse", "hiddenstatic");
            $form->setType("ville", "selectstatic");
            $form->setType("telephone", "hiddenstatic");
            $form->setType("hebergement_type", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("hebergement", "static");
            $form->setType("nom", "static");
            $form->setType("adresse", "static");
            $form->setType("ville", "selectstatic");
            $form->setType("telephone", "static");
            $form->setType("hebergement_type", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('hebergement','VerifNum(this)');
        $form->setOnchange('ville','VerifNum(this)');
        $form->setOnchange('hebergement_type','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("hebergement", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("adresse", 10);
        $form->setTaille("ville", 11);
        $form->setTaille("telephone", 10);
        $form->setTaille("hebergement_type", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("hebergement", 11);
        $form->setMax("nom", -5);
        $form->setMax("adresse", -5);
        $form->setMax("ville", 11);
        $form->setMax("telephone", -5);
        $form->setMax("hebergement_type", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('hebergement', __('hebergement'));
        $form->setLib('nom', __('nom'));
        $form->setLib('adresse', __('adresse'));
        $form->setLib('ville', __('ville'));
        $form->setLib('telephone', __('telephone'));
        $form->setLib('hebergement_type', __('hebergement_type'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // hebergement_type
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "hebergement_type",
            $this->get_var_sql_forminc__sql("hebergement_type"),
            $this->get_var_sql_forminc__sql("hebergement_type_by_id"),
            false
        );
        // ville
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "ville",
            $this->get_var_sql_forminc__sql("ville"),
            $this->get_var_sql_forminc__sql("ville_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('hebergement_type', $this->retourformulaire))
                $form->setVal('hebergement_type', $idxformulaire);
            if($this->is_in_context_of_foreign_key('ville', $this->retourformulaire))
                $form->setVal('ville', $idxformulaire);
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
        $this->rechercheTable($this->f->db, "facture_sejour", "hebergement", $id);
        // Verification de la cle secondaire : sejour
        $this->rechercheTable($this->f->db, "sejour", "hebergement", $id);
    }


}
