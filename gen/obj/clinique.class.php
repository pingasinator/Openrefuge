<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:55

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class clinique_gen extends dbform {

    protected $_absolute_class_name = "clinique";

    var $table = "clinique";
    var $clePrimaire = "clinique";
    var $typeCle = "N";
    var $required_field = array(
        "clinique"
    );
    
    var $foreign_keys_extended = array(
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
            "clinique",
            "nom",
            "adresse",
            "ville",
            "telephone",
        );
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
        if (!is_numeric($val['clinique'])) {
            $this->valF['clinique'] = ""; // -> requis
        } else {
            $this->valF['clinique'] = $val['clinique'];
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
            $form->setType("clinique", "hidden");
            $form->setType("nom", "text");
            $form->setType("adresse", "text");
            if ($this->is_in_context_of_foreign_key("ville", $this->retourformulaire)) {
                $form->setType("ville", "selecthiddenstatic");
            } else {
                $form->setType("ville", "select");
            }
            $form->setType("telephone", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("clinique", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("adresse", "text");
            if ($this->is_in_context_of_foreign_key("ville", $this->retourformulaire)) {
                $form->setType("ville", "selecthiddenstatic");
            } else {
                $form->setType("ville", "select");
            }
            $form->setType("telephone", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("clinique", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("adresse", "hiddenstatic");
            $form->setType("ville", "selectstatic");
            $form->setType("telephone", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("clinique", "static");
            $form->setType("nom", "static");
            $form->setType("adresse", "static");
            $form->setType("ville", "selectstatic");
            $form->setType("telephone", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('clinique','VerifNum(this)');
        $form->setOnchange('ville','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("clinique", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("adresse", 10);
        $form->setTaille("ville", 11);
        $form->setTaille("telephone", 10);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("clinique", 11);
        $form->setMax("nom", -5);
        $form->setMax("adresse", -5);
        $form->setMax("ville", 11);
        $form->setMax("telephone", -5);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('clinique', __('clinique'));
        $form->setLib('nom', __('nom'));
        $form->setLib('adresse', __('adresse'));
        $form->setLib('ville', __('ville'));
        $form->setLib('telephone', __('telephone'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

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
        // Verification de la cle secondaire : facture_soin
        $this->rechercheTable($this->f->db, "facture_soin", "clinique", $id);
        // Verification de la cle secondaire : soin
        $this->rechercheTable($this->f->db, "soin", "clinique", $id);
        // Verification de la cle secondaire : veterinaire
        $this->rechercheTable($this->f->db, "veterinaire", "clinique", $id);
    }


}
