<?php
//$Id$ 
//gen openMairie le 20/02/2026 10:21

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class factures_soins_gen extends dbform {

    protected $_absolute_class_name = "factures_soins";

    var $table = "factures_soins";
    var $clePrimaire = "factures_soins";
    var $typeCle = "N";
    var $required_field = array(
        "factures_soins"
    );
    
    var $foreign_keys_extended = array(
        "animale" => array("animale", ),
        "clinique" => array("clinique", ),
        "factures" => array("factures", ),
        "soins" => array("soins", ),
        "veterinaire" => array("veterinaire", ),
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
            "factures_soins",
            "factures",
            "animale",
            "clinique",
            "veterinaire",
            "date_soin",
            "tarifs",
            "soins",
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
    function get_var_sql_forminc__sql_clinique() {
        return "SELECT clinique.clinique, clinique.ville FROM ".DB_PREFIXE."clinique ORDER BY clinique.ville ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_clinique_by_id() {
        return "SELECT clinique.clinique, clinique.ville FROM ".DB_PREFIXE."clinique WHERE clinique = '<idx>'";
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
    function get_var_sql_forminc__sql_soins() {
        return "SELECT soins.soins, soins.posologie FROM ".DB_PREFIXE."soins ORDER BY soins.posologie ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_soins_by_id() {
        return "SELECT soins.soins, soins.posologie FROM ".DB_PREFIXE."soins WHERE soins = '<idx>'";
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
        if (!is_numeric($val['factures_soins'])) {
            $this->valF['factures_soins'] = ""; // -> requis
        } else {
            $this->valF['factures_soins'] = $val['factures_soins'];
        }
        if (!is_numeric($val['factures'])) {
            $this->valF['factures'] = NULL;
        } else {
            $this->valF['factures'] = $val['factures'];
        }
        if (!is_numeric($val['animale'])) {
            $this->valF['animale'] = NULL;
        } else {
            $this->valF['animale'] = $val['animale'];
        }
        if (!is_numeric($val['clinique'])) {
            $this->valF['clinique'] = NULL;
        } else {
            $this->valF['clinique'] = $val['clinique'];
        }
        if (!is_numeric($val['veterinaire'])) {
            $this->valF['veterinaire'] = NULL;
        } else {
            $this->valF['veterinaire'] = $val['veterinaire'];
        }
        if ($val['date_soin'] != "") {
            $this->valF['date_soin'] = $this->dateDB($val['date_soin']);
        } else {
            $this->valF['date_soin'] = NULL;
        }
        if (!is_numeric($val['tarifs'])) {
            $this->valF['tarifs'] = NULL;
        } else {
            $this->valF['tarifs'] = $val['tarifs'];
        }
        if (!is_numeric($val['soins'])) {
            $this->valF['soins'] = NULL;
        } else {
            $this->valF['soins'] = $val['soins'];
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
            $form->setType("factures_soins", "hidden");
            if ($this->is_in_context_of_foreign_key("factures", $this->retourformulaire)) {
                $form->setType("factures", "selecthiddenstatic");
            } else {
                $form->setType("factures", "select");
            }
            if ($this->is_in_context_of_foreign_key("animale", $this->retourformulaire)) {
                $form->setType("animale", "selecthiddenstatic");
            } else {
                $form->setType("animale", "select");
            }
            if ($this->is_in_context_of_foreign_key("clinique", $this->retourformulaire)) {
                $form->setType("clinique", "selecthiddenstatic");
            } else {
                $form->setType("clinique", "select");
            }
            if ($this->is_in_context_of_foreign_key("veterinaire", $this->retourformulaire)) {
                $form->setType("veterinaire", "selecthiddenstatic");
            } else {
                $form->setType("veterinaire", "select");
            }
            $form->setType("date_soin", "date");
            $form->setType("tarifs", "text");
            if ($this->is_in_context_of_foreign_key("soins", $this->retourformulaire)) {
                $form->setType("soins", "selecthiddenstatic");
            } else {
                $form->setType("soins", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("factures_soins", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("factures", $this->retourformulaire)) {
                $form->setType("factures", "selecthiddenstatic");
            } else {
                $form->setType("factures", "select");
            }
            if ($this->is_in_context_of_foreign_key("animale", $this->retourformulaire)) {
                $form->setType("animale", "selecthiddenstatic");
            } else {
                $form->setType("animale", "select");
            }
            if ($this->is_in_context_of_foreign_key("clinique", $this->retourformulaire)) {
                $form->setType("clinique", "selecthiddenstatic");
            } else {
                $form->setType("clinique", "select");
            }
            if ($this->is_in_context_of_foreign_key("veterinaire", $this->retourformulaire)) {
                $form->setType("veterinaire", "selecthiddenstatic");
            } else {
                $form->setType("veterinaire", "select");
            }
            $form->setType("date_soin", "date");
            $form->setType("tarifs", "text");
            if ($this->is_in_context_of_foreign_key("soins", $this->retourformulaire)) {
                $form->setType("soins", "selecthiddenstatic");
            } else {
                $form->setType("soins", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("factures_soins", "hiddenstatic");
            $form->setType("factures", "selectstatic");
            $form->setType("animale", "selectstatic");
            $form->setType("clinique", "selectstatic");
            $form->setType("veterinaire", "selectstatic");
            $form->setType("date_soin", "hiddenstatic");
            $form->setType("tarifs", "hiddenstatic");
            $form->setType("soins", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("factures_soins", "static");
            $form->setType("factures", "selectstatic");
            $form->setType("animale", "selectstatic");
            $form->setType("clinique", "selectstatic");
            $form->setType("veterinaire", "selectstatic");
            $form->setType("date_soin", "datestatic");
            $form->setType("tarifs", "static");
            $form->setType("soins", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('factures_soins','VerifNum(this)');
        $form->setOnchange('factures','VerifNum(this)');
        $form->setOnchange('animale','VerifNum(this)');
        $form->setOnchange('clinique','VerifNum(this)');
        $form->setOnchange('veterinaire','VerifNum(this)');
        $form->setOnchange('date_soin','fdate(this)');
        $form->setOnchange('tarifs','VerifFloat(this)');
        $form->setOnchange('soins','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("factures_soins", 20);
        $form->setTaille("factures", 20);
        $form->setTaille("animale", 20);
        $form->setTaille("clinique", 20);
        $form->setTaille("veterinaire", 20);
        $form->setTaille("date_soin", 12);
        $form->setTaille("tarifs", 20);
        $form->setTaille("soins", 20);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("factures_soins", 20);
        $form->setMax("factures", 20);
        $form->setMax("animale", 20);
        $form->setMax("clinique", 20);
        $form->setMax("veterinaire", 20);
        $form->setMax("date_soin", 12);
        $form->setMax("tarifs", 20);
        $form->setMax("soins", 20);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('factures_soins', __('factures_soins'));
        $form->setLib('factures', __('factures'));
        $form->setLib('animale', __('animale'));
        $form->setLib('clinique', __('clinique'));
        $form->setLib('veterinaire', __('veterinaire'));
        $form->setLib('date_soin', __('date_soin'));
        $form->setLib('tarifs', __('tarifs'));
        $form->setLib('soins', __('soins'));
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
        // soins
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "soins",
            $this->get_var_sql_forminc__sql("soins"),
            $this->get_var_sql_forminc__sql("soins_by_id"),
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
            if($this->is_in_context_of_foreign_key('animale', $this->retourformulaire))
                $form->setVal('animale', $idxformulaire);
            if($this->is_in_context_of_foreign_key('clinique', $this->retourformulaire))
                $form->setVal('clinique', $idxformulaire);
            if($this->is_in_context_of_foreign_key('factures', $this->retourformulaire))
                $form->setVal('factures', $idxformulaire);
            if($this->is_in_context_of_foreign_key('soins', $this->retourformulaire))
                $form->setVal('soins', $idxformulaire);
            if($this->is_in_context_of_foreign_key('veterinaire', $this->retourformulaire))
                $form->setVal('veterinaire', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
