<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:29

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class veterinaire_gen extends dbform {

    protected $_absolute_class_name = "veterinaire";

    var $table = "veterinaire";
    var $clePrimaire = "veterinaire";
    var $typeCle = "N";
    var $required_field = array(
        "veterinaire"
    );
    
    var $foreign_keys_extended = array(
        "civilite" => array("civilite", ),
        "clinique" => array("clinique", ),
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
            "veterinaire",
            "nom",
            "prenom",
            "telephone",
            "clinique",
            "civilite",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_civilite() {
        return "SELECT civilite.civilite, civilite.libelle FROM ".DB_PREFIXE."civilite ORDER BY civilite.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_civilite_by_id() {
        return "SELECT civilite.civilite, civilite.libelle FROM ".DB_PREFIXE."civilite WHERE civilite = <idx>";
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




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['veterinaire'])) {
            $this->valF['veterinaire'] = ""; // -> requis
        } else {
            $this->valF['veterinaire'] = $val['veterinaire'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if ($val['prenom'] == "") {
            $this->valF['prenom'] = NULL;
        } else {
            $this->valF['prenom'] = $val['prenom'];
        }
        if ($val['telephone'] == "") {
            $this->valF['telephone'] = NULL;
        } else {
            $this->valF['telephone'] = $val['telephone'];
        }
        if (!is_numeric($val['clinique'])) {
            $this->valF['clinique'] = NULL;
        } else {
            $this->valF['clinique'] = $val['clinique'];
        }
        if (!is_numeric($val['civilite'])) {
            $this->valF['civilite'] = NULL;
        } else {
            $this->valF['civilite'] = $val['civilite'];
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
            $form->setType("veterinaire", "hidden");
            $form->setType("nom", "text");
            $form->setType("prenom", "text");
            $form->setType("telephone", "text");
            if ($this->is_in_context_of_foreign_key("clinique", $this->retourformulaire)) {
                $form->setType("clinique", "selecthiddenstatic");
            } else {
                $form->setType("clinique", "select");
            }
            if ($this->is_in_context_of_foreign_key("civilite", $this->retourformulaire)) {
                $form->setType("civilite", "selecthiddenstatic");
            } else {
                $form->setType("civilite", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("veterinaire", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("prenom", "text");
            $form->setType("telephone", "text");
            if ($this->is_in_context_of_foreign_key("clinique", $this->retourformulaire)) {
                $form->setType("clinique", "selecthiddenstatic");
            } else {
                $form->setType("clinique", "select");
            }
            if ($this->is_in_context_of_foreign_key("civilite", $this->retourformulaire)) {
                $form->setType("civilite", "selecthiddenstatic");
            } else {
                $form->setType("civilite", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("veterinaire", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("prenom", "hiddenstatic");
            $form->setType("telephone", "hiddenstatic");
            $form->setType("clinique", "selectstatic");
            $form->setType("civilite", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("veterinaire", "static");
            $form->setType("nom", "static");
            $form->setType("prenom", "static");
            $form->setType("telephone", "static");
            $form->setType("clinique", "selectstatic");
            $form->setType("civilite", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('veterinaire','VerifNum(this)');
        $form->setOnchange('clinique','VerifNum(this)');
        $form->setOnchange('civilite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("veterinaire", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("prenom", 10);
        $form->setTaille("telephone", 10);
        $form->setTaille("clinique", 11);
        $form->setTaille("civilite", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("veterinaire", 11);
        $form->setMax("nom", -5);
        $form->setMax("prenom", -5);
        $form->setMax("telephone", -5);
        $form->setMax("clinique", 11);
        $form->setMax("civilite", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('veterinaire', __('veterinaire'));
        $form->setLib('nom', __('nom'));
        $form->setLib('prenom', __('prenom'));
        $form->setLib('telephone', __('telephone'));
        $form->setLib('clinique', __('clinique'));
        $form->setLib('civilite', __('civilite'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // civilite
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "civilite",
            $this->get_var_sql_forminc__sql("civilite"),
            $this->get_var_sql_forminc__sql("civilite_by_id"),
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
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('civilite', $this->retourformulaire))
                $form->setVal('civilite', $idxformulaire);
            if($this->is_in_context_of_foreign_key('clinique', $this->retourformulaire))
                $form->setVal('clinique', $idxformulaire);
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
        $this->rechercheTable($this->f->db, "facture_soin", "veterinaire", $id);
        // Verification de la cle secondaire : soin
        $this->rechercheTable($this->f->db, "soin", "veterinaire", $id);
    }


}
