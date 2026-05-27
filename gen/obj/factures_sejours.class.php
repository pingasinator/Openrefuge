<?php
//$Id$ 
//gen openMairie le 19/02/2026 09:14

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class factures_sejours_gen extends dbform {

    protected $_absolute_class_name = "factures_sejours";

    var $table = "factures_sejours";
    var $clePrimaire = "factures_sejours";
    var $typeCle = "N";
    var $required_field = array(
        "factures_sejours"
    );
    
    var $foreign_keys_extended = array(
        "animale" => array("animale", ),
        "factures" => array("factures", ),
        "hebergement" => array("hebergement", ),
        "provenance" => array("provenance", ),
        "sejours" => array("sejours", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("factures");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "factures_sejours",
            "factures",
            "sejours",
            "date_d_entree",
            "date_de_sortie",
            "payee",
            "animale",
            "provenance",
            "hebergement",
            "tarif",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animale() {
        return "SELECT animale.animale, animale.nom FROM ".DB_PREFIXE."animale ORDER BY animale.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animale_by_id() {
        return "SELECT animale.animale, animale.nom FROM ".DB_PREFIXE."animale WHERE animale = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_factures() {
        return "SELECT factures.factures, factures.personne FROM ".DB_PREFIXE."factures ORDER BY factures.personne ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_factures_by_id() {
        return "SELECT factures.factures, factures.personne FROM ".DB_PREFIXE."factures WHERE factures = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_hebergement() {
        return "SELECT hebergement.hebergement, hebergement.adresse FROM ".DB_PREFIXE."hebergement ORDER BY hebergement.adresse ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_hebergement_by_id() {
        return "SELECT hebergement.hebergement, hebergement.adresse FROM ".DB_PREFIXE."hebergement WHERE hebergement = '<idx>'";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_provenance() {
        return "SELECT provenance.provenance, provenance.provenance FROM ".DB_PREFIXE."provenance ORDER BY provenance.provenance ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_provenance_by_id() {
        return "SELECT provenance.provenance, provenance.provenance FROM ".DB_PREFIXE."provenance WHERE provenance = '<idx>'";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_sejours() {
        return "SELECT sejours.sejours, sejours.date_d_entree FROM ".DB_PREFIXE."sejours ORDER BY sejours.date_d_entree ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_sejours_by_id() {
        return "SELECT sejours.sejours, sejours.date_d_entree FROM ".DB_PREFIXE."sejours WHERE sejours = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['factures_sejours'])) {
            $this->valF['factures_sejours'] = ""; // -> requis
        } else {
            $this->valF['factures_sejours'] = $val['factures_sejours'];
        }
        if (!is_numeric($val['factures'])) {
            $this->valF['factures'] = NULL;
        } else {
            $this->valF['factures'] = $val['factures'];
        }
        if (!is_numeric($val['sejours'])) {
            $this->valF['sejours'] = NULL;
        } else {
            $this->valF['sejours'] = $val['sejours'];
        }
        if ($val['date_d_entree'] != "") {
            $this->valF['date_d_entree'] = $this->dateDB($val['date_d_entree']);
        } else {
            $this->valF['date_d_entree'] = NULL;
        }
        if ($val['date_de_sortie'] != "") {
            $this->valF['date_de_sortie'] = $this->dateDB($val['date_de_sortie']);
        } else {
            $this->valF['date_de_sortie'] = NULL;
        }
        if ($val['payee'] == "") {
            $this->valF['payee'] = NULL;
        } else {
            $this->valF['payee'] = $val['payee'];
        }
        if (!is_numeric($val['animale'])) {
            $this->valF['animale'] = NULL;
        } else {
            $this->valF['animale'] = $val['animale'];
        }
        if ($val['provenance'] == "") {
            $this->valF['provenance'] = NULL;
        } else {
            $this->valF['provenance'] = $val['provenance'];
        }
        if (!is_numeric($val['hebergement'])) {
            $this->valF['hebergement'] = NULL;
        } else {
            $this->valF['hebergement'] = $val['hebergement'];
        }
        if (!is_numeric($val['tarif'])) {
            $this->valF['tarif'] = NULL;
        } else {
            $this->valF['tarif'] = $val['tarif'];
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
            $form->setType("factures_sejours", "hidden");
            if ($this->is_in_context_of_foreign_key("factures", $this->retourformulaire)) {
                $form->setType("factures", "selecthiddenstatic");
            } else {
                $form->setType("factures", "select");
            }
            if ($this->is_in_context_of_foreign_key("sejours", $this->retourformulaire)) {
                $form->setType("sejours", "selecthiddenstatic");
            } else {
                $form->setType("sejours", "select");
            }
            $form->setType("date_d_entree", "date");
            $form->setType("date_de_sortie", "date");
            $form->setType("payee", "text");
            if ($this->is_in_context_of_foreign_key("animale", $this->retourformulaire)) {
                $form->setType("animale", "selecthiddenstatic");
            } else {
                $form->setType("animale", "select");
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
            $form->setType("tarif", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("factures_sejours", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("factures", $this->retourformulaire)) {
                $form->setType("factures", "selecthiddenstatic");
            } else {
                $form->setType("factures", "select");
            }
            if ($this->is_in_context_of_foreign_key("sejours", $this->retourformulaire)) {
                $form->setType("sejours", "selecthiddenstatic");
            } else {
                $form->setType("sejours", "select");
            }
            $form->setType("date_d_entree", "date");
            $form->setType("date_de_sortie", "date");
            $form->setType("payee", "text");
            if ($this->is_in_context_of_foreign_key("animale", $this->retourformulaire)) {
                $form->setType("animale", "selecthiddenstatic");
            } else {
                $form->setType("animale", "select");
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
            $form->setType("tarif", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("factures_sejours", "hiddenstatic");
            $form->setType("factures", "selectstatic");
            $form->setType("sejours", "selectstatic");
            $form->setType("date_d_entree", "hiddenstatic");
            $form->setType("date_de_sortie", "hiddenstatic");
            $form->setType("payee", "hiddenstatic");
            $form->setType("animale", "selectstatic");
            $form->setType("provenance", "selectstatic");
            $form->setType("hebergement", "selectstatic");
            $form->setType("tarif", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("factures_sejours", "static");
            $form->setType("factures", "selectstatic");
            $form->setType("sejours", "selectstatic");
            $form->setType("date_d_entree", "datestatic");
            $form->setType("date_de_sortie", "datestatic");
            $form->setType("payee", "static");
            $form->setType("animale", "selectstatic");
            $form->setType("provenance", "selectstatic");
            $form->setType("hebergement", "selectstatic");
            $form->setType("tarif", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('factures_sejours','VerifNum(this)');
        $form->setOnchange('factures','VerifNum(this)');
        $form->setOnchange('sejours','VerifNum(this)');
        $form->setOnchange('date_d_entree','fdate(this)');
        $form->setOnchange('date_de_sortie','fdate(this)');
        $form->setOnchange('animale','VerifNum(this)');
        $form->setOnchange('hebergement','VerifNum(this)');
        $form->setOnchange('tarif','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("factures_sejours", 20);
        $form->setTaille("factures", 20);
        $form->setTaille("sejours", 20);
        $form->setTaille("date_d_entree", 12);
        $form->setTaille("date_de_sortie", 12);
        $form->setTaille("payee", 10);
        $form->setTaille("animale", 20);
        $form->setTaille("provenance", 10);
        $form->setTaille("hebergement", 20);
        $form->setTaille("tarif", 20);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("factures_sejours", 20);
        $form->setMax("factures", 20);
        $form->setMax("sejours", 20);
        $form->setMax("date_d_entree", 12);
        $form->setMax("date_de_sortie", 12);
        $form->setMax("payee", -5);
        $form->setMax("animale", 20);
        $form->setMax("provenance", -5);
        $form->setMax("hebergement", 20);
        $form->setMax("tarif", 20);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('factures_sejours', __('factures_sejours'));
        $form->setLib('factures', __('factures'));
        $form->setLib('sejours', __('sejours'));
        $form->setLib('date_d_entree', __('date_d_entree'));
        $form->setLib('date_de_sortie', __('date_de_sortie'));
        $form->setLib('payee', __('payee'));
        $form->setLib('animale', __('animale'));
        $form->setLib('provenance', __('provenance'));
        $form->setLib('hebergement', __('hebergement'));
        $form->setLib('tarif', __('tarif'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // animale
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "animale",
            $this->get_var_sql_forminc__sql("animale"),
            $this->get_var_sql_forminc__sql("animale_by_id"),
            false
        );
        // factures
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "factures",
            $this->get_var_sql_forminc__sql("factures"),
            $this->get_var_sql_forminc__sql("factures_by_id"),
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
        // sejours
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "sejours",
            $this->get_var_sql_forminc__sql("sejours"),
            $this->get_var_sql_forminc__sql("sejours_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('animale', $this->retourformulaire))
                $form->setVal('animale', $idxformulaire);
            if($this->is_in_context_of_foreign_key('factures', $this->retourformulaire))
                $form->setVal('factures', $idxformulaire);
            if($this->is_in_context_of_foreign_key('hebergement', $this->retourformulaire))
                $form->setVal('hebergement', $idxformulaire);
            if($this->is_in_context_of_foreign_key('provenance', $this->retourformulaire))
                $form->setVal('provenance', $idxformulaire);
            if($this->is_in_context_of_foreign_key('sejours', $this->retourformulaire))
                $form->setVal('sejours', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
