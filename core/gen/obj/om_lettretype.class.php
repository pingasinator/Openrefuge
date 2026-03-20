<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_lettretype_gen extends dbform {

    protected $_absolute_class_name = "om_lettretype";

    var $table = "om_lettretype";
    var $clePrimaire = "om_lettretype";
    var $typeCle = "N";
    var $required_field = array(
        "corps_om_htmletatex",
        "format",
        "id",
        "libelle",
        "logoleft",
        "logotop",
        "om_collectivite",
        "om_lettretype",
        "om_sql",
        "orientation",
        "titrebordure",
        "titrehauteur",
        "titrelargeur",
        "titreleft",
        "titre_om_htmletat",
        "titretop"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
        "om_requete" => array("om_requete", ),
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
            "om_lettretype",
            "om_collectivite",
            "id",
            "libelle",
            "actif",
            "orientation",
            "format",
            "logo",
            "logoleft",
            "logotop",
            "titre_om_htmletat",
            "titreleft",
            "titretop",
            "titrelargeur",
            "titrehauteur",
            "titrebordure",
            "corps_om_htmletatex",
            "om_sql",
            "margeleft",
            "margetop",
            "margeright",
            "margebottom",
            "se_font",
            "se_couleurtexte",
            "header_om_htmletat",
            "header_offset",
            "footer_om_htmletat",
            "footer_offset",
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

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sql() {
        return "SELECT om_requete.om_requete, om_requete.libelle FROM ".DB_PREFIXE."om_requete ORDER BY om_requete.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sql_by_id() {
        return "SELECT om_requete.om_requete, om_requete.libelle FROM ".DB_PREFIXE."om_requete WHERE om_requete = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_lettretype'])) {
            $this->valF['om_lettretype'] = ""; // -> requis
        } else {
            $this->valF['om_lettretype'] = $val['om_lettretype'];
        }
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
        $this->valF['libelle'] = $val['libelle'];
        if ($val['actif'] == 1 || $val['actif'] == "t" || $val['actif'] == "Oui") {
            $this->valF['actif'] = true;
        } else {
            $this->valF['actif'] = false;
        }
        $this->valF['orientation'] = $val['orientation'];
        $this->valF['format'] = $val['format'];
        if ($val['logo'] == "") {
            $this->valF['logo'] = NULL;
        } else {
            $this->valF['logo'] = $val['logo'];
        }
        if (!is_numeric($val['logoleft'])) {
            $this->valF['logoleft'] = ""; // -> requis
        } else {
            $this->valF['logoleft'] = $val['logoleft'];
        }
        if (!is_numeric($val['logotop'])) {
            $this->valF['logotop'] = ""; // -> requis
        } else {
            $this->valF['logotop'] = $val['logotop'];
        }
            $this->valF['titre_om_htmletat'] = $val['titre_om_htmletat'];
        if (!is_numeric($val['titreleft'])) {
            $this->valF['titreleft'] = ""; // -> requis
        } else {
            $this->valF['titreleft'] = $val['titreleft'];
        }
        if (!is_numeric($val['titretop'])) {
            $this->valF['titretop'] = ""; // -> requis
        } else {
            $this->valF['titretop'] = $val['titretop'];
        }
        if (!is_numeric($val['titrelargeur'])) {
            $this->valF['titrelargeur'] = ""; // -> requis
        } else {
            $this->valF['titrelargeur'] = $val['titrelargeur'];
        }
        if (!is_numeric($val['titrehauteur'])) {
            $this->valF['titrehauteur'] = ""; // -> requis
        } else {
            $this->valF['titrehauteur'] = $val['titrehauteur'];
        }
        $this->valF['titrebordure'] = $val['titrebordure'];
            $this->valF['corps_om_htmletatex'] = $val['corps_om_htmletatex'];
        if (!is_numeric($val['om_sql'])) {
            $this->valF['om_sql'] = ""; // -> requis
        } else {
            $this->valF['om_sql'] = $val['om_sql'];
        }
        if (!is_numeric($val['margeleft'])) {
            $this->valF['margeleft'] = 0; // -> default
        } else {
            $this->valF['margeleft'] = $val['margeleft'];
        }
        if (!is_numeric($val['margetop'])) {
            $this->valF['margetop'] = 0; // -> default
        } else {
            $this->valF['margetop'] = $val['margetop'];
        }
        if (!is_numeric($val['margeright'])) {
            $this->valF['margeright'] = 0; // -> default
        } else {
            $this->valF['margeright'] = $val['margeright'];
        }
        if (!is_numeric($val['margebottom'])) {
            $this->valF['margebottom'] = 0; // -> default
        } else {
            $this->valF['margebottom'] = $val['margebottom'];
        }
        if ($val['se_font'] == "") {
            $this->valF['se_font'] = NULL;
        } else {
            $this->valF['se_font'] = $val['se_font'];
        }
        if ($val['se_couleurtexte'] == "") {
            $this->valF['se_couleurtexte'] = NULL;
        } else {
            $this->valF['se_couleurtexte'] = $val['se_couleurtexte'];
        }
            $this->valF['header_om_htmletat'] = $val['header_om_htmletat'];
        if (!is_numeric($val['header_offset'])) {
            $this->valF['header_offset'] = 0; // -> default
        } else {
            $this->valF['header_offset'] = $val['header_offset'];
        }
            $this->valF['footer_om_htmletat'] = $val['footer_om_htmletat'];
        if (!is_numeric($val['footer_offset'])) {
            $this->valF['footer_offset'] = 0; // -> default
        } else {
            $this->valF['footer_offset'] = $val['footer_offset'];
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
            $form->setType("om_lettretype", "hidden");
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
            $form->setType("libelle", "text");
            $form->setType("actif", "checkbox");
            $form->setType("orientation", "text");
            $form->setType("format", "text");
            $form->setType("logo", "text");
            $form->setType("logoleft", "text");
            $form->setType("logotop", "text");
            $form->setType("titre_om_htmletat", "htmlEtat");
            $form->setType("titreleft", "text");
            $form->setType("titretop", "text");
            $form->setType("titrelargeur", "text");
            $form->setType("titrehauteur", "text");
            $form->setType("titrebordure", "text");
            $form->setType("corps_om_htmletatex", "htmlEtatEx");
            if ($this->is_in_context_of_foreign_key("om_requete", $this->retourformulaire)) {
                $form->setType("om_sql", "selecthiddenstatic");
            } else {
                $form->setType("om_sql", "select");
            }
            $form->setType("margeleft", "text");
            $form->setType("margetop", "text");
            $form->setType("margeright", "text");
            $form->setType("margebottom", "text");
            $form->setType("se_font", "text");
            $form->setType("se_couleurtexte", "text");
            $form->setType("header_om_htmletat", "htmlEtat");
            $form->setType("header_offset", "text");
            $form->setType("footer_om_htmletat", "htmlEtat");
            $form->setType("footer_offset", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_lettretype", "hiddenstatic");
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
            $form->setType("libelle", "text");
            $form->setType("actif", "checkbox");
            $form->setType("orientation", "text");
            $form->setType("format", "text");
            $form->setType("logo", "text");
            $form->setType("logoleft", "text");
            $form->setType("logotop", "text");
            $form->setType("titre_om_htmletat", "htmlEtat");
            $form->setType("titreleft", "text");
            $form->setType("titretop", "text");
            $form->setType("titrelargeur", "text");
            $form->setType("titrehauteur", "text");
            $form->setType("titrebordure", "text");
            $form->setType("corps_om_htmletatex", "htmlEtatEx");
            if ($this->is_in_context_of_foreign_key("om_requete", $this->retourformulaire)) {
                $form->setType("om_sql", "selecthiddenstatic");
            } else {
                $form->setType("om_sql", "select");
            }
            $form->setType("margeleft", "text");
            $form->setType("margetop", "text");
            $form->setType("margeright", "text");
            $form->setType("margebottom", "text");
            $form->setType("se_font", "text");
            $form->setType("se_couleurtexte", "text");
            $form->setType("header_om_htmletat", "htmlEtat");
            $form->setType("header_offset", "text");
            $form->setType("footer_om_htmletat", "htmlEtat");
            $form->setType("footer_offset", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_lettretype", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("orientation", "hiddenstatic");
            $form->setType("format", "hiddenstatic");
            $form->setType("logo", "hiddenstatic");
            $form->setType("logoleft", "hiddenstatic");
            $form->setType("logotop", "hiddenstatic");
            $form->setType("titre_om_htmletat", "hiddenstatic");
            $form->setType("titreleft", "hiddenstatic");
            $form->setType("titretop", "hiddenstatic");
            $form->setType("titrelargeur", "hiddenstatic");
            $form->setType("titrehauteur", "hiddenstatic");
            $form->setType("titrebordure", "hiddenstatic");
            $form->setType("corps_om_htmletatex", "hiddenstatic");
            $form->setType("om_sql", "selectstatic");
            $form->setType("margeleft", "hiddenstatic");
            $form->setType("margetop", "hiddenstatic");
            $form->setType("margeright", "hiddenstatic");
            $form->setType("margebottom", "hiddenstatic");
            $form->setType("se_font", "hiddenstatic");
            $form->setType("se_couleurtexte", "hiddenstatic");
            $form->setType("header_om_htmletat", "hiddenstatic");
            $form->setType("header_offset", "hiddenstatic");
            $form->setType("footer_om_htmletat", "hiddenstatic");
            $form->setType("footer_offset", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_lettretype", "static");
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
            $form->setType("libelle", "static");
            $form->setType("actif", "checkboxstatic");
            $form->setType("orientation", "static");
            $form->setType("format", "static");
            $form->setType("logo", "static");
            $form->setType("logoleft", "static");
            $form->setType("logotop", "static");
            $form->setType("titre_om_htmletat", "htmlstatic");
            $form->setType("titreleft", "static");
            $form->setType("titretop", "static");
            $form->setType("titrelargeur", "static");
            $form->setType("titrehauteur", "static");
            $form->setType("titrebordure", "static");
            $form->setType("corps_om_htmletatex", "htmlstatic");
            $form->setType("om_sql", "selectstatic");
            $form->setType("margeleft", "static");
            $form->setType("margetop", "static");
            $form->setType("margeright", "static");
            $form->setType("margebottom", "static");
            $form->setType("se_font", "static");
            $form->setType("se_couleurtexte", "static");
            $form->setType("header_om_htmletat", "htmlstatic");
            $form->setType("header_offset", "static");
            $form->setType("footer_om_htmletat", "htmlstatic");
            $form->setType("footer_offset", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_lettretype','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
        $form->setOnchange('logoleft','VerifNum(this)');
        $form->setOnchange('logotop','VerifNum(this)');
        $form->setOnchange('titreleft','VerifNum(this)');
        $form->setOnchange('titretop','VerifNum(this)');
        $form->setOnchange('titrelargeur','VerifNum(this)');
        $form->setOnchange('titrehauteur','VerifNum(this)');
        $form->setOnchange('om_sql','VerifNum(this)');
        $form->setOnchange('margeleft','VerifNum(this)');
        $form->setOnchange('margetop','VerifNum(this)');
        $form->setOnchange('margeright','VerifNum(this)');
        $form->setOnchange('margebottom','VerifNum(this)');
        $form->setOnchange('header_offset','VerifNum(this)');
        $form->setOnchange('footer_offset','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_lettretype", 11);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("libelle", 30);
        $form->setTaille("actif", 1);
        $form->setTaille("orientation", 10);
        $form->setTaille("format", 10);
        $form->setTaille("logo", 30);
        $form->setTaille("logoleft", 11);
        $form->setTaille("logotop", 11);
        $form->setTaille("titre_om_htmletat", 80);
        $form->setTaille("titreleft", 11);
        $form->setTaille("titretop", 11);
        $form->setTaille("titrelargeur", 11);
        $form->setTaille("titrehauteur", 11);
        $form->setTaille("titrebordure", 20);
        $form->setTaille("corps_om_htmletatex", 80);
        $form->setTaille("om_sql", 11);
        $form->setTaille("margeleft", 11);
        $form->setTaille("margetop", 11);
        $form->setTaille("margeright", 11);
        $form->setTaille("margebottom", 11);
        $form->setTaille("se_font", 20);
        $form->setTaille("se_couleurtexte", 11);
        $form->setTaille("header_om_htmletat", 80);
        $form->setTaille("header_offset", 11);
        $form->setTaille("footer_om_htmletat", 80);
        $form->setTaille("footer_offset", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_lettretype", 11);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("libelle", 100);
        $form->setMax("actif", 1);
        $form->setMax("orientation", 2);
        $form->setMax("format", 5);
        $form->setMax("logo", 30);
        $form->setMax("logoleft", 11);
        $form->setMax("logotop", 11);
        $form->setMax("titre_om_htmletat", 6);
        $form->setMax("titreleft", 11);
        $form->setMax("titretop", 11);
        $form->setMax("titrelargeur", 11);
        $form->setMax("titrehauteur", 11);
        $form->setMax("titrebordure", 20);
        $form->setMax("corps_om_htmletatex", 6);
        $form->setMax("om_sql", 11);
        $form->setMax("margeleft", 11);
        $form->setMax("margetop", 11);
        $form->setMax("margeright", 11);
        $form->setMax("margebottom", 11);
        $form->setMax("se_font", 20);
        $form->setMax("se_couleurtexte", 11);
        $form->setMax("header_om_htmletat", 6);
        $form->setMax("header_offset", 11);
        $form->setMax("footer_om_htmletat", 6);
        $form->setMax("footer_offset", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_lettretype', __('om_lettretype'));
        $form->setLib('om_collectivite', __('om_collectivite'));
        $form->setLib('id', __('id'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('actif', __('actif'));
        $form->setLib('orientation', __('orientation'));
        $form->setLib('format', __('format'));
        $form->setLib('logo', __('logo'));
        $form->setLib('logoleft', __('logoleft'));
        $form->setLib('logotop', __('logotop'));
        $form->setLib('titre_om_htmletat', __('titre_om_htmletat'));
        $form->setLib('titreleft', __('titreleft'));
        $form->setLib('titretop', __('titretop'));
        $form->setLib('titrelargeur', __('titrelargeur'));
        $form->setLib('titrehauteur', __('titrehauteur'));
        $form->setLib('titrebordure', __('titrebordure'));
        $form->setLib('corps_om_htmletatex', __('corps_om_htmletatex'));
        $form->setLib('om_sql', __('om_sql'));
        $form->setLib('margeleft', __('margeleft'));
        $form->setLib('margetop', __('margetop'));
        $form->setLib('margeright', __('margeright'));
        $form->setLib('margebottom', __('margebottom'));
        $form->setLib('se_font', __('se_font'));
        $form->setLib('se_couleurtexte', __('se_couleurtexte'));
        $form->setLib('header_om_htmletat', __('header_om_htmletat'));
        $form->setLib('header_offset', __('header_offset'));
        $form->setLib('footer_om_htmletat', __('footer_om_htmletat'));
        $form->setLib('footer_offset', __('footer_offset'));
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
        // om_sql
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_sql",
            $this->get_var_sql_forminc__sql("om_sql"),
            $this->get_var_sql_forminc__sql("om_sql_by_id"),
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
            if($this->is_in_context_of_foreign_key('om_requete', $this->retourformulaire))
                $form->setVal('om_sql', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
