<?php
/**
 * Ce fichier est destine a permettre la surcharge de certaines methodes de
 * la classe om_application pour des besoins specifiques de l'application
 *
 * @package framework_openmairie
 * @version SVN : $Id: framework_openmairie.class.php 4348 2018-07-20 16:49:26Z softime $
 */

/**
 *
 */
if (file_exists("../dyn/locales.inc.php") === true) {
    require_once "../dyn/locales.inc.php";
}

/**
 * Définition de la constante représentant le chemin d'accès au framework
 */
define("PATH_OPENMAIRIE", getcwd()."/../core/");

/**
 * Dépendances PHP du framework
 * On modifie la valeur de la directive de configuration include_path en
 * fonction pour y ajouter les chemins vers les librairies dont le framework
 * dépend.
 */
set_include_path(
    get_include_path().PATH_SEPARATOR.implode(
        PATH_SEPARATOR,
        array(
            getcwd()."/../php/pear",
            getcwd()."/../php/db",
            getcwd()."/../php/fpdf",
            getcwd()."/../php/phpmailer",
            getcwd()."/../php/tcpdf",
        )
    )
);

/**
 *
 */
if (file_exists("../dyn/debug.inc.php") === true) {
    require_once "../dyn/debug.inc.php";
}

/**
 *
 */
require_once PATH_OPENMAIRIE."om_application.class.php";

/**
 *
 */
class framework_openmairie extends application {

    protected function set_config__menu(){

	parent::set_config__menu();
	$parent_menu = $this->config__menu;
	$menu = array();

	$rubrik = array(
	"title" => __("édition"),
	"class" => "édition"
	);

	$links = array(
	    array(
                "href" => OM_ROUTE_TAB."&obj=animal",
                "class" => "animal",
                "title" => _("animaux"),
                "open" => array(
                "tab.php|animal",
                "index.php|animal[module=tab]",
                "form.php|animal",
                "index.php|animal[module=form]",
                )
	    ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=animal_espece",
                "class" => "animal_espece",
                "title" => _("espèces"),
                "open" => array(
                "tab.php|animal_espece",
                "index.php|animal_espece[module=tab]",
                "form.php|animal_espece",
                "index.php|animal_espece[module=form]",
		    )
	    ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=animal_race",
                "class" => "animal_race",
                "title" => _("races"),
                "open" => array(
                "tab.php|animal_race",
                "index.php|animal_race[module=tab]",
                "form.php|animal_race",
                "index.php|animal_race[module=form]",
		    )
	    ),
	    array(
		    "href" => OM_ROUTE_TAB."&obj=personne",
                "class" => "personne",
                "title" => _("personnes"),
                "open" => array(
                "tab.php|personne",
                "index.php|personne[module=tab]",
                "form.php|personne",
                "index.php|personne[module=form]",
		    )
	    ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=facture",
                "class" => "facture",
                "title" => _("factures"),
                "open" => array(
                "tab.php|facture",
                "index.php|facture[module=tab]",
                "form.php|facture",
                "index.php|facture[module=form]",
		    )
	    ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=ville",
                "class" => "ville",
                "title" => _("villes"),
                "open" => array(
                "tab.php|ville",
                "index.php|ville[module=tab]",
                "form.php|ville",
                "index.php|ville[module=form]",
		    )
	    )
	);

	$rubrik['links'] = $links;
	$menu[] = $rubrik;

    $rubrik = array(
        "title" => __("Logements"),
        "class" => "sejours"
    );

    $links = array(
        array(
		    "href" => OM_ROUTE_TAB."&obj=hebergement",
                "class" => "hébergement",
                "title" => _("hébergements"),
                "open" => array(
                "tab.php|hebergement",
                "index.php|hebergement[module=tab]",
                "form.php|hebergement",
                "index.php|hebergement[module=form]",
		    )
	    ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=sejour",
                "class" => "séjours",
                "title" => _("séjours"),
                "open" => array(
                "tab.php|sejour",
                "index.php|sejour[module=tab]",
                "form.php|sejour",
                "index.php|sejour[module=form]",
		    )
	    ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=sejour_tarif",
                "class" => "sejour_tarif",
                "title" => _("tarifs"),
                "open" => array(
                "tab.php|sejour_tarif",
                "index.php|sejour_tarif[module=tab]",
                "form.php|sejour_tarif",
                "index.php|sejour_tarif[module=form]",
		    )
	    )
    );

    $rubrik['links'] = $links;
	$menu[] = $rubrik;

    $rubrik = array(
	"title" => __("soins vétérinaires"),
	"class" => "soins"
	);

    $links = array(
        array(
		    "href" => OM_ROUTE_TAB."&obj=soin",
            "class" => "soin",
            "title" => _("soins"),
            "open" => array(
            "tab.php|soin",
            "index.php|soin[module=tab]",
            "form.php|soin",
            "index.php|soin[module=form]",
            )
        ),
        array(
            "href" => OM_ROUTE_TAB."&obj=veterinaire",
            "class" => "veterinaire",
            "title" => _("vétérinaires"),
            "open" => array(
            "tab.php|veterinaire",
            "index.php|veterinaire[module=tab]",
            "form.php|veterinaire",
            "index.php|veterinaire[module=form]",
            )
        ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=clinique",
            "class" => "clinique",
            "title" => _("cliniques"),
            "open" => array(
            "tab.php|clinique",
            "index.php|clinique[module=tab]",
            "form.php|clinique",
            "index.php|clinique[module=form]",
	    	)
        ),
        array(
		    "href" => OM_ROUTE_TAB."&obj=medicament",
            "class" => "medicament",
            "title" => _("médicaments"),
            "open" => array(
            "tab.php|medicament",
            "index.php|medicament[module=tab]",
            "form.php|medicament",
            "index.php|medicament[module=form]",
	    	)
	    )
    );

    $rubrik['links'] = $links;
	$menu[] = $rubrik;

	$this->config__menu = array_merge(
	$menu,
	$parent_menu
	);
    }

    /**
     * Cette variable est un marqueur permettant d'indiquer si nous sommes
     * en mode développement du framework ou non.
     * @var boolean
     */
    protected $_framework_development_mode = true;

    /**
     * Gestion du nom de l'application.
     *
     * @var mixed Configuration niveau application.
     */
    protected $_application_name = "Framework openMairie";

    /**
     * Titre HTML.
     *
     * @var mixed Configuration niveau application.
     */
    protected $html_head_title = ":: openMairie :: Framework";

    /**
     *
     * @return void
     */
    function setDefaultValues() {
        $this->addHTMLHeadCss(
            array(
                "../lib/om-theme/jquery-ui-theme/jquery-ui.custom.css",
                "../lib/om-theme/om.css",
            ),
            21
        );
    }
}
