<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:57

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class personne_gen extends dbform {

    protected $_absolute_class_name = "personne";

    var $table = "personne";
    var $clePrimaire = "personne";
    var $typeCle = "N";
    var $required_field = array(
        "personne"
    );
    
    var $foreign_keys_extended = array(
        "civilite" => array("civilite", ),
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
            "personne",
            "nom",
            "prenom",
            "adresse",
            "ville",
            "telephone",
            "telephone_sec",
            "mail",
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
        if (!is_numeric($val['personne'])) {
            $this->valF['personne'] = ""; // -> requis
        } else {
            $this->valF['personne'] = $val['personne'];
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
        if ($val['telephone_sec'] == "") {
            $this->valF['telephone_sec'] = NULL;
        } else {
            $this->valF['telephone_sec'] = $val['telephone_sec'];
        }
        if ($val['mail'] == "") {
            $this->valF['mail'] = NULL;
        } else {
            $this->valF['mail'] = $val['mail'];
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
            $form->setType("personne", "hidden");
            $form->setType("nom", "text");
            $form->setType("prenom", "text");
            $form->setType("adresse", "text");
            if ($this->is_in_context_of_foreign_key("ville", $this->retourformulaire)) {
                $form->setType("ville", "selecthiddenstatic");
            } else {
                $form->setType("ville", "select");
            }
            $form->setType("telephone", "text");
            $form->setType("telephone_sec", "text");
            $form->setType("mail", "text");
            if ($this->is_in_context_of_foreign_key("civilite", $this->retourformulaire)) {
                $form->setType("civilite", "selecthiddenstatic");
            } else {
                $form->setType("civilite", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("personne", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("prenom", "text");
            $form->setType("adresse", "text");
            if ($this->is_in_context_of_foreign_key("ville", $this->retourformulaire)) {
                $form->setType("ville", "selecthiddenstatic");
            } else {
                $form->setType("ville", "select");
            }
            $form->setType("telephone", "text");
            $form->setType("telephone_sec", "text");
            $form->setType("mail", "text");
            if ($this->is_in_context_of_foreign_key("civilite", $this->retourformulaire)) {
                $form->setType("civilite", "selecthiddenstatic");
            } else {
                $form->setType("civilite", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("personne", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("prenom", "hiddenstatic");
            $form->setType("adresse", "hiddenstatic");
            $form->setType("ville", "selectstatic");
            $form->setType("telephone", "hiddenstatic");
            $form->setType("telephone_sec", "hiddenstatic");
            $form->setType("mail", "hiddenstatic");
            $form->setType("civilite", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("personne", "static");
            $form->setType("nom", "static");
            $form->setType("prenom", "static");
            $form->setType("adresse", "static");
            $form->setType("ville", "selectstatic");
            $form->setType("telephone", "static");
            $form->setType("telephone_sec", "static");
            $form->setType("mail", "static");
            $form->setType("civilite", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('personne','VerifNum(this)');
        $form->setOnchange('ville','VerifNum(this)');
        $form->setOnchange('civilite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("personne", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("prenom", 10);
        $form->setTaille("adresse", 10);
        $form->setTaille("ville", 11);
        $form->setTaille("telephone", 10);
        $form->setTaille("telephone_sec", 10);
        $form->setTaille("mail", 10);
        $form->setTaille("civilite", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("personne", 11);
        $form->setMax("nom", -5);
        $form->setMax("prenom", -5);
        $form->setMax("adresse", -5);
        $form->setMax("ville", 11);
        $form->setMax("telephone", -5);
        $form->setMax("telephone_sec", -5);
        $form->setMax("mail", -5);
        $form->setMax("civilite", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('personne', __('personne'));
        $form->setLib('nom', __('nom'));
        $form->setLib('prenom', __('prenom'));
        $form->setLib('adresse', __('adresse'));
        $form->setLib('ville', __('ville'));
        $form->setLib('telephone', __('telephone'));
        $form->setLib('telephone_sec', __('telephone_sec'));
        $form->setLib('mail', __('mail'));
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
            if($this->is_in_context_of_foreign_key('civilite', $this->retourformulaire))
                $form->setVal('civilite', $idxformulaire);
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
        // Verification de la cle secondaire : animal
        $this->rechercheTable($this->f->db, "animal", "personne", $id);
        // Verification de la cle secondaire : facture
        $this->rechercheTable($this->f->db, "facture", "personne", $id);
    }


}
