<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_sig_flux_gen extends dbform {

    protected $_absolute_class_name = "om_sig_flux";

    var $table = "om_sig_flux";
    var $clePrimaire = "om_sig_flux";
    var $typeCle = "N";
    var $required_field = array(
        "chemin",
        "couches",
        "id",
        "libelle",
        "om_collectivite",
        "om_sig_flux"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
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
            "om_sig_flux",
            "libelle",
            "om_collectivite",
            "id",
            "attribution",
            "chemin",
            "couches",
            "cache_type",
            "cache_gfi_chemin",
            "cache_gfi_couches",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_collectivite() {
        return "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_collectivite_by_id() {
        return "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_flux'])) {
            $this->valF['om_sig_flux'] = ""; // -> requis
        } else {
            $this->valF['om_sig_flux'] = $val['om_sig_flux'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if (!is_numeric($val['om_collectivite'])) {
            $this->valF['om_collectivite'] = ""; // -> requis
        } else {
            if($_SESSION['niveau']==1) {
                $this->valF['om_collectivite'] = $_SESSION['collectivite'];
            } else {
                $this->valF['om_collectivite'] = $val['om_collectivite'];
            }
        }
        $this->valF['id'] = $val['id'];
        if ($val['attribution'] == "") {
            $this->valF['attribution'] = NULL;
        } else {
            $this->valF['attribution'] = $val['attribution'];
        }
        $this->valF['chemin'] = $val['chemin'];
        $this->valF['couches'] = $val['couches'];
        if ($val['cache_type'] == "") {
            $this->valF['cache_type'] = NULL;
        } else {
            $this->valF['cache_type'] = $val['cache_type'];
        }
        if ($val['cache_gfi_chemin'] == "") {
            $this->valF['cache_gfi_chemin'] = NULL;
        } else {
            $this->valF['cache_gfi_chemin'] = $val['cache_gfi_chemin'];
        }
        if ($val['cache_gfi_couches'] == "") {
            $this->valF['cache_gfi_couches'] = NULL;
        } else {
            $this->valF['cache_gfi_couches'] = $val['cache_gfi_couches'];
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
            $form->setType("om_sig_flux", "hidden");
            $form->setType("libelle", "text");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selecthiddenstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "select");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
            $form->setType("id", "text");
            $form->setType("attribution", "text");
            $form->setType("chemin", "text");
            $form->setType("couches", "text");
            $form->setType("cache_type", "text");
            $form->setType("cache_gfi_chemin", "text");
            $form->setType("cache_gfi_couches", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_sig_flux", "hiddenstatic");
            $form->setType("libelle", "text");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selecthiddenstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "select");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
            $form->setType("id", "text");
            $form->setType("attribution", "text");
            $form->setType("chemin", "text");
            $form->setType("couches", "text");
            $form->setType("cache_type", "text");
            $form->setType("cache_gfi_chemin", "text");
            $form->setType("cache_gfi_couches", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_sig_flux", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("attribution", "hiddenstatic");
            $form->setType("chemin", "hiddenstatic");
            $form->setType("couches", "hiddenstatic");
            $form->setType("cache_type", "hiddenstatic");
            $form->setType("cache_gfi_chemin", "hiddenstatic");
            $form->setType("cache_gfi_couches", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_sig_flux", "static");
            $form->setType("libelle", "static");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selectstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selectstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
            $form->setType("id", "static");
            $form->setType("attribution", "static");
            $form->setType("chemin", "static");
            $form->setType("couches", "static");
            $form->setType("cache_type", "static");
            $form->setType("cache_gfi_chemin", "static");
            $form->setType("cache_gfi_couches", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_flux','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_flux", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("attribution", 30);
        $form->setTaille("chemin", 30);
        $form->setTaille("couches", 30);
        $form->setTaille("cache_type", 10);
        $form->setTaille("cache_gfi_chemin", 30);
        $form->setTaille("cache_gfi_couches", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_flux", 11);
        $form->setMax("libelle", 50);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("attribution", 150);
        $form->setMax("chemin", 255);
        $form->setMax("couches", 255);
        $form->setMax("cache_type", 3);
        $form->setMax("cache_gfi_chemin", 255);
        $form->setMax("cache_gfi_couches", 255);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_flux', __('om_sig_flux'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('om_collectivite', __('om_collectivite'));
        $form->setLib('id', __('id'));
        $form->setLib('attribution', __('attribution'));
        $form->setLib('chemin', __('chemin'));
        $form->setLib('couches', __('couches'));
        $form->setLib('cache_type', __('cache_type'));
        $form->setLib('cache_gfi_chemin', __('cache_gfi_chemin'));
        $form->setLib('cache_gfi_couches', __('cache_gfi_couches'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // om_collectivite
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_collectivite",
            $this->get_var_sql_forminc__sql("om_collectivite"),
            $this->get_var_sql_forminc__sql("om_collectivite_by_id"),
            false
        );
    }


    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        if($validation==0 and $maj==0 and $_SESSION['niveau']==1) {
            $form->setVal('om_collectivite', $_SESSION['collectivite']);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setVal

    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation==0 and $maj==0 and $_SESSION['niveau']==1) {
            $form->setVal('om_collectivite', $_SESSION['collectivite']);
        }// fin validation
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_collectivite', $this->retourformulaire))
                $form->setVal('om_collectivite', $idxformulaire);
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
        // Verification de la cle secondaire : om_sig_map_flux
        $this->rechercheTable($this->f->db, "om_sig_map_flux", "om_sig_flux", $id);
    }


}
