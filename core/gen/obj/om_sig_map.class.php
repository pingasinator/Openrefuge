<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_sig_map_gen extends dbform {

    protected $_absolute_class_name = "om_sig_map";

    var $table = "om_sig_map";
    var $clePrimaire = "om_sig_map";
    var $typeCle = "N";
    var $required_field = array(
        "fond_default",
        "id",
        "libelle",
        "om_collectivite",
        "om_sig_extent",
        "om_sig_map",
        "om_sql",
        "projection_externe",
        "retour",
        "url",
        "zoom"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
        "om_sig_extent" => array("om_sig_extent", ),
        "om_sig_map" => array("om_sig_map", ),
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
            "om_sig_map",
            "om_collectivite",
            "id",
            "libelle",
            "actif",
            "zoom",
            "fond_osm",
            "fond_bing",
            "fond_sat",
            "layer_info",
            "projection_externe",
            "url",
            "om_sql",
            "retour",
            "util_idx",
            "util_reqmo",
            "util_recherche",
            "source_flux",
            "fond_default",
            "om_sig_extent",
            "restrict_extent",
            "sld_marqueur",
            "sld_data",
            "point_centrage",
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
    function get_var_sql_forminc__sql_om_sig_extent() {
        return "SELECT om_sig_extent.om_sig_extent, om_sig_extent.nom FROM ".DB_PREFIXE."om_sig_extent ORDER BY om_sig_extent.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sig_extent_by_id() {
        return "SELECT om_sig_extent.om_sig_extent, om_sig_extent.nom FROM ".DB_PREFIXE."om_sig_extent WHERE om_sig_extent = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_source_flux() {
        return "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map ORDER BY om_sig_map.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_source_flux_by_id() {
        return "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_map'])) {
            $this->valF['om_sig_map'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map'] = $val['om_sig_map'];
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
        $this->valF['zoom'] = $val['zoom'];
        if ($val['fond_osm'] == 1 || $val['fond_osm'] == "t" || $val['fond_osm'] == "Oui") {
            $this->valF['fond_osm'] = true;
        } else {
            $this->valF['fond_osm'] = false;
        }
        if ($val['fond_bing'] == 1 || $val['fond_bing'] == "t" || $val['fond_bing'] == "Oui") {
            $this->valF['fond_bing'] = true;
        } else {
            $this->valF['fond_bing'] = false;
        }
        if ($val['fond_sat'] == 1 || $val['fond_sat'] == "t" || $val['fond_sat'] == "Oui") {
            $this->valF['fond_sat'] = true;
        } else {
            $this->valF['fond_sat'] = false;
        }
        if ($val['layer_info'] == 1 || $val['layer_info'] == "t" || $val['layer_info'] == "Oui") {
            $this->valF['layer_info'] = true;
        } else {
            $this->valF['layer_info'] = false;
        }
        $this->valF['projection_externe'] = $val['projection_externe'];
            $this->valF['url'] = $val['url'];
            $this->valF['om_sql'] = $val['om_sql'];
        $this->valF['retour'] = $val['retour'];
        if ($val['util_idx'] == 1 || $val['util_idx'] == "t" || $val['util_idx'] == "Oui") {
            $this->valF['util_idx'] = true;
        } else {
            $this->valF['util_idx'] = false;
        }
        if ($val['util_reqmo'] == 1 || $val['util_reqmo'] == "t" || $val['util_reqmo'] == "Oui") {
            $this->valF['util_reqmo'] = true;
        } else {
            $this->valF['util_reqmo'] = false;
        }
        if ($val['util_recherche'] == 1 || $val['util_recherche'] == "t" || $val['util_recherche'] == "Oui") {
            $this->valF['util_recherche'] = true;
        } else {
            $this->valF['util_recherche'] = false;
        }
        if (!is_numeric($val['source_flux'])) {
            $this->valF['source_flux'] = NULL;
        } else {
            $this->valF['source_flux'] = $val['source_flux'];
        }
        $this->valF['fond_default'] = $val['fond_default'];
        if (!is_numeric($val['om_sig_extent'])) {
            $this->valF['om_sig_extent'] = ""; // -> requis
        } else {
            $this->valF['om_sig_extent'] = $val['om_sig_extent'];
        }
        if ($val['restrict_extent'] == 1 || $val['restrict_extent'] == "t" || $val['restrict_extent'] == "Oui") {
            $this->valF['restrict_extent'] = true;
        } else {
            $this->valF['restrict_extent'] = false;
        }
        if ($val['sld_marqueur'] == "") {
            $this->valF['sld_marqueur'] = NULL;
        } else {
            $this->valF['sld_marqueur'] = $val['sld_marqueur'];
        }
        if ($val['sld_data'] == "") {
            $this->valF['sld_data'] = NULL;
        } else {
            $this->valF['sld_data'] = $val['sld_data'];
        }
        if ($val['point_centrage'] == "") {
            unset($this->valF['point_centrage']);
        } else {
            $this->valF['point_centrage'] = $val['point_centrage'];
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
            $form->setType("om_sig_map", "hidden");
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
            $form->setType("zoom", "text");
            $form->setType("fond_osm", "checkbox");
            $form->setType("fond_bing", "checkbox");
            $form->setType("fond_sat", "checkbox");
            $form->setType("layer_info", "checkbox");
            $form->setType("projection_externe", "text");
            $form->setType("url", "textarea");
            $form->setType("om_sql", "textarea");
            $form->setType("retour", "text");
            $form->setType("util_idx", "checkbox");
            $form->setType("util_reqmo", "checkbox");
            $form->setType("util_recherche", "checkbox");
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("source_flux", "selecthiddenstatic");
            } else {
                $form->setType("source_flux", "select");
            }
            $form->setType("fond_default", "text");
            if ($this->is_in_context_of_foreign_key("om_sig_extent", $this->retourformulaire)) {
                $form->setType("om_sig_extent", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_extent", "select");
            }
            $form->setType("restrict_extent", "checkbox");
            $form->setType("sld_marqueur", "text");
            $form->setType("sld_data", "text");
            $form->setType("point_centrage", "geom");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_sig_map", "hiddenstatic");
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
            $form->setType("zoom", "text");
            $form->setType("fond_osm", "checkbox");
            $form->setType("fond_bing", "checkbox");
            $form->setType("fond_sat", "checkbox");
            $form->setType("layer_info", "checkbox");
            $form->setType("projection_externe", "text");
            $form->setType("url", "textarea");
            $form->setType("om_sql", "textarea");
            $form->setType("retour", "text");
            $form->setType("util_idx", "checkbox");
            $form->setType("util_reqmo", "checkbox");
            $form->setType("util_recherche", "checkbox");
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("source_flux", "selecthiddenstatic");
            } else {
                $form->setType("source_flux", "select");
            }
            $form->setType("fond_default", "text");
            if ($this->is_in_context_of_foreign_key("om_sig_extent", $this->retourformulaire)) {
                $form->setType("om_sig_extent", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_extent", "select");
            }
            $form->setType("restrict_extent", "checkbox");
            $form->setType("sld_marqueur", "text");
            $form->setType("sld_data", "text");
            $form->setType("point_centrage", "geom");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_sig_map", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("zoom", "hiddenstatic");
            $form->setType("fond_osm", "hiddenstatic");
            $form->setType("fond_bing", "hiddenstatic");
            $form->setType("fond_sat", "hiddenstatic");
            $form->setType("layer_info", "hiddenstatic");
            $form->setType("projection_externe", "hiddenstatic");
            $form->setType("url", "hiddenstatic");
            $form->setType("om_sql", "hiddenstatic");
            $form->setType("retour", "hiddenstatic");
            $form->setType("util_idx", "hiddenstatic");
            $form->setType("util_reqmo", "hiddenstatic");
            $form->setType("util_recherche", "hiddenstatic");
            $form->setType("source_flux", "selectstatic");
            $form->setType("fond_default", "hiddenstatic");
            $form->setType("om_sig_extent", "selectstatic");
            $form->setType("restrict_extent", "hiddenstatic");
            $form->setType("sld_marqueur", "hiddenstatic");
            $form->setType("sld_data", "hiddenstatic");
            $form->setType("point_centrage", "geom");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_sig_map", "static");
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
            $form->setType("zoom", "static");
            $form->setType("fond_osm", "checkboxstatic");
            $form->setType("fond_bing", "checkboxstatic");
            $form->setType("fond_sat", "checkboxstatic");
            $form->setType("layer_info", "checkboxstatic");
            $form->setType("projection_externe", "static");
            $form->setType("url", "textareastatic");
            $form->setType("om_sql", "textareastatic");
            $form->setType("retour", "static");
            $form->setType("util_idx", "checkboxstatic");
            $form->setType("util_reqmo", "checkboxstatic");
            $form->setType("util_recherche", "checkboxstatic");
            $form->setType("source_flux", "selectstatic");
            $form->setType("fond_default", "static");
            $form->setType("om_sig_extent", "selectstatic");
            $form->setType("restrict_extent", "checkboxstatic");
            $form->setType("sld_marqueur", "static");
            $form->setType("sld_data", "static");
            $form->setType("point_centrage", "geom");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_map','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
        $form->setOnchange('source_flux','VerifNum(this)');
        $form->setOnchange('om_sig_extent','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_map", 11);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("libelle", 30);
        $form->setTaille("actif", 1);
        $form->setTaille("zoom", 10);
        $form->setTaille("fond_osm", 1);
        $form->setTaille("fond_bing", 1);
        $form->setTaille("fond_sat", 1);
        $form->setTaille("layer_info", 1);
        $form->setTaille("projection_externe", 30);
        $form->setTaille("url", 80);
        $form->setTaille("om_sql", 80);
        $form->setTaille("retour", 30);
        $form->setTaille("util_idx", 1);
        $form->setTaille("util_reqmo", 1);
        $form->setTaille("util_recherche", 1);
        $form->setTaille("source_flux", 11);
        $form->setTaille("fond_default", 10);
        $form->setTaille("om_sig_extent", 11);
        $form->setTaille("restrict_extent", 1);
        $form->setTaille("sld_marqueur", 30);
        $form->setTaille("sld_data", 30);
        $form->setTaille("point_centrage", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_map", 11);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("libelle", 50);
        $form->setMax("actif", 1);
        $form->setMax("zoom", 3);
        $form->setMax("fond_osm", 1);
        $form->setMax("fond_bing", 1);
        $form->setMax("fond_sat", 1);
        $form->setMax("layer_info", 1);
        $form->setMax("projection_externe", 60);
        $form->setMax("url", 6);
        $form->setMax("om_sql", 6);
        $form->setMax("retour", 50);
        $form->setMax("util_idx", 1);
        $form->setMax("util_reqmo", 1);
        $form->setMax("util_recherche", 1);
        $form->setMax("source_flux", 11);
        $form->setMax("fond_default", 10);
        $form->setMax("om_sig_extent", 11);
        $form->setMax("restrict_extent", 1);
        $form->setMax("sld_marqueur", 254);
        $form->setMax("sld_data", 254);
        $form->setMax("point_centrage", 551424);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_map', __('om_sig_map'));
        $form->setLib('om_collectivite', __('om_collectivite'));
        $form->setLib('id', __('id'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('actif', __('actif'));
        $form->setLib('zoom', __('zoom'));
        $form->setLib('fond_osm', __('fond_osm'));
        $form->setLib('fond_bing', __('fond_bing'));
        $form->setLib('fond_sat', __('fond_sat'));
        $form->setLib('layer_info', __('layer_info'));
        $form->setLib('projection_externe', __('projection_externe'));
        $form->setLib('url', __('url'));
        $form->setLib('om_sql', __('om_sql'));
        $form->setLib('retour', __('retour'));
        $form->setLib('util_idx', __('util_idx'));
        $form->setLib('util_reqmo', __('util_reqmo'));
        $form->setLib('util_recherche', __('util_recherche'));
        $form->setLib('source_flux', __('source_flux'));
        $form->setLib('fond_default', __('fond_default'));
        $form->setLib('om_sig_extent', __('om_sig_extent'));
        $form->setLib('restrict_extent', __('restrict_extent'));
        $form->setLib('sld_marqueur', __('sld_marqueur'));
        $form->setLib('sld_data', __('sld_data'));
        $form->setLib('point_centrage', __('point_centrage'));
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
        // om_sig_extent
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_sig_extent",
            $this->get_var_sql_forminc__sql("om_sig_extent"),
            $this->get_var_sql_forminc__sql("om_sig_extent_by_id"),
            false
        );
        // source_flux
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "source_flux",
            $this->get_var_sql_forminc__sql("source_flux"),
            $this->get_var_sql_forminc__sql("source_flux_by_id"),
            false
        );
        // point_centrage
        if ($maj == 1 || $maj == 3) {
            $contenu = array();
            $contenu[0] = array("om_sig_map", $this->getParameter("idx"), "0");
            $form->setSelect("point_centrage", $contenu);
        }
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
            if($this->is_in_context_of_foreign_key('om_sig_extent', $this->retourformulaire))
                $form->setVal('om_sig_extent', $idxformulaire);
            if($this->is_in_context_of_foreign_key('om_sig_map', $this->retourformulaire))
                $form->setVal('source_flux', $idxformulaire);
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
        // Verification de la cle secondaire : om_sig_map
        $this->rechercheTable($this->f->db, "om_sig_map", "source_flux", $id);
        // Verification de la cle secondaire : om_sig_map_comp
        $this->rechercheTable($this->f->db, "om_sig_map_comp", "om_sig_map", $id);
        // Verification de la cle secondaire : om_sig_map_flux
        $this->rechercheTable($this->f->db, "om_sig_map_flux", "om_sig_map", $id);
    }


}
