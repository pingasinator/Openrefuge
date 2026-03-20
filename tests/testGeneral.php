<?php
/**
 * Ce fichier regroupe les tests unitaire général à l'application OpenADS
 *
 * @package framework_openmairie
 * @version SVN : $Id: testGeneral.php 4369 2018-12-02 22:23:57Z fmichon $
 */
require_once "../app/framework_openmairie.class.php";

/**
 * Cette classe permet de tester unitairement les fonctions de l'application.
 */
class General extends PHPUnit\Framework\TestCase {

    /**
     * Méthode lancée en début de traitement
     */
    public function setUp() {

        // Instancie la timezone
        date_default_timezone_set('Europe/Paris');
        echo ' = '.get_class().'.'.str_replace('test_', '', $this->getName())."\r\n";
    }

    /**
     * Méthode lancée en fin de traitement
     */
    public function tearDown() {
        
        //
    }

    /**
     * Vérification de la méthode permettant de récupérer les paramètres de
     * collectivité si le paramètre n'est pas valide
     */
    function test_verification_parametre_get_collectivite() {
        // Instance de la classe Utils
        @session_start();
        $_SESSION['collectivite'] = 1;
        $_SESSION['login'] = "admin";
        $_SERVER['REQUEST_URI'] = "";
        // collectivité existante
        $f = new framework_openmairie("nohtml");
        $f->disableLog();
        // Cas de la collectivité de l'utilisateur
        $result_collectivite = $f->getCollectivite();
        $this->assertNotEquals($result_collectivite, array());
        // collectivité existante
        $result_collectivite = $f->getCollectivite('1');
        $this->assertNotEquals($result_collectivite, array());
        // mauvais paramètre
        $result_collectivite = $f->getCollectivite('');
        $this->assertEquals($result_collectivite, array());
    }

    /**
     * Vérification de la méthode permettant de selectionner le logo de la bonne
     * collectivité.
     */
    function test_selection_du_logo_dans_les_editions() {
        // Instance de la classe Utils
        @session_start();
        $_SESSION['collectivite'] = 1;
        $_SESSION['login'] = "admin";
        $_SERVER['REQUEST_URI'] = "";
        $f = new framework_openmairie("nohtml");
        $GLOBALS['f'] = $f;
        $f->disableLog();

        /**
         * Constitution du jeu de données
         */
        //
        $logo_id = "test_editions_logo_mono_multi.png";
        //
        $logo_mono_key = $f->db->nextId(DB_PREFIXE."om_logo");
        $logo_mono = array(
            //
            "om_logo" => $logo_mono_key,
            "id" => $logo_id,
            "libelle" => "test_editions_logo mono",
            "description" => "",
            "fichier" => "b449b5fae2367bf41ccee5cf974de989",
            "resolution" => null,
            "actif" => "t",
            "om_collectivite" => "1",
        );
        $res = $f->db->autoExecute(DB_PREFIXE."om_logo", $logo_mono, DB_AUTOQUERY_INSERT);
        //
        $logo_multi_key = $f->db->nextId(DB_PREFIXE."om_logo");
        $logo_multi = array(
            //
            "om_logo" => $logo_multi_key,
            "id" => $logo_id,
            "libelle" => "test_editions_logo multi",
            "description" => "",
            "fichier" => "d20a5c36d3b83464bab63035a7f61901",
            "resolution" => "300",
            "actif" => "t",
            "om_collectivite" => "2",
        );
        $res = $f->db->autoExecute(DB_PREFIXE."om_logo", $logo_multi, DB_AUTOQUERY_INSERT);

        /**
         *
         */
        $edition = $f->get_inst__om_edition();
        // Vérification du logo de collectivité mono dans le cas où :
        // - un logo multi actif est défini
        // - un logo actif pour la collectivité est défini
        $logo = $edition->get_logo_from_collectivite($logo_id, 1);
        $logo_valid = array(
            "file" => $f->storage->getPath($logo_mono["fichier"]),
            "w" => 0,
            "h" => 0,
            "type" => "png",
        );
        // Le logo commune doit être retourné par la méthode
        $this->assertEquals($logo, $logo_valid);

        // Désactivation du logo de la commune
        $val_logo["actif"] = 'f';
        $f->db->autoExecute(DB_PREFIXE."om_logo", $val_logo, DB_AUTOQUERY_UPDATE, "om_logo=".$logo_mono_key);

        // Vérification du logo de collectivité mono dans le cas où :
        // - un logo multi actif est défini
        // - un logo la collectivité est inactif
        $logo = $edition->get_logo_from_collectivite($logo_id, 1);
        $logo_valid = array(
            "file" => $f->storage->getPath($logo_multi["fichier"]),
            "w" => 43.349333333333334,
            "h" => 43.349333333333334,
            "type" => "png",
        );
        // Le logo de la collectivité multi doit être retourné
        $this->assertEquals($logo, $logo_valid);

        // Désactivation du logo de la commune
        $val_logo["actif"] = 'f';
        $f->db->autoExecute(DB_PREFIXE."om_logo", $val_logo, DB_AUTOQUERY_UPDATE, "om_logo=".$logo_multi_key);

        // Vérification du logo de collectivité mono dans le cas où aucun logo
        // n'est activé
        $logo = $edition->get_logo_from_collectivite($logo_id, 1);
        $logo_valid = null;
        // Un valeur null doit être retournée
        $this->assertEquals($logo, $logo_valid);
        // Destruction de la classe Utils
        $f->__destruct();
    }

    /**
     * Teste la fonction sendMail() de la classe Application.
     */
    function test_sendmail() {

        // Instance de la classe Application
        @session_start();
        $_SESSION['collectivite'] = 1;
        $_SESSION['login'] = "admin";
        $_SERVER['REQUEST_URI'] = "";
        $f = new framework_openmairie("nohtml");
        $f->disableLog();

        // Paramétrage
        $objet = '[openMairie] Test de l\'envoi de mail';
        $corps = 'Ne pas répondre.';
        $email = 'support@openmairie.org';

        // Succès
        $case_1 = $f->sendMail(
            iconv('UTF-8', 'CP1252', $objet),
            iconv('UTF-8', 'CP1252', $corps),
            iconv('UTF-8', 'CP1252', $email));
        $this->assertEquals(true, $case_1);

        // Échec cause email invalide
        $case_2 = $f->sendMail(
            iconv('UTF-8', 'CP1252', $objet),
            iconv('UTF-8', 'CP1252', $corps),
            iconv('UTF-8', 'CP1252', 'email_incorrect'));
        $this->assertEquals(false, $case_2);

        // Échec cause mauvaise configuration du serveur mail
        $file_path = '../dyn/mail.inc.php';
        $old_file_content = file_get_contents($file_path);
        $line_host = array();
        preg_match_all("/^.*mail_host.*,$/m", $old_file_content, $line_host);
        $old_line_host = $line_host[0];
        $new_line_host = '    \'mail_host\' => \'1234\',';
        $new_file_content = str_replace($old_line_host, $new_line_host, $old_file_content);
        // Reconfiguration incorrecte pour ce test
        file_put_contents($file_path, $new_file_content);
        $f->__destruct();
        $f = new framework_openmairie("nohtml");
        $f->disableLog();
        // Tentative d'envoi
        $case_3 = $f->sendMail(
            iconv('UTF-8', 'CP1252', $objet),
            iconv('UTF-8', 'CP1252', $corps),
            iconv('UTF-8', 'CP1252', $email));
        // Reconfiguration valide pour la suite des tests
        file_put_contents($file_path, $old_file_content);
        $f->__destruct();
        $f = new framework_openmairie("nohtml");
        $f->disableLog();
        // Vérification
        $this->assertEquals(false, $case_3);
        // Destruction de la classe Application
        $f->__destruct();
    }

    /**
     * test de la propriété ZoomMode à l'instanciation de la classe PDF
     */
    public function test_pdf_zoom_mode_is_real() {
        require_once PATH_OPENMAIRIE."fpdf_etat.php";
        // test to ensure that the property ZoomMode of PDF is set to real
        $pdf = new PDF();
        $this->assertTrue( $pdf->GetZoomMode() === 'real');
    }

    /**
     * Test de non régression sur la méthode de composition de la requête
     * de la classe 'om_table' au niveau du tri. Ticket #8786.
     */
    public function testTriAscDesc() {
        @session_start();
        $_SESSION["collectivite"] = 1;
        $_SESSION["login"] = "admin";
        $_SERVER["REQUEST_URI"] = "";
        $f = new framework_openmairie("nohtml");
        $GLOBALS['f'] = $f;
        $f->disableLog();
        // initialize om_table and setup minimal data
        $champAffiche = array(
            "0 as col0",
            "1 as col1",
            "2 as col2",
            "3 as col3",
            "4 as col4",
            "5 as col5",
            "6 as col6",
            "7 as col7",
            "8 as col8",
            "9 as col9",
            "10 as col10",
        );
        $om_table = $f->get_inst__om_table(array(
            "aff" => "om_collectivite",
            "table" => "om_collectivite",
            "serie" => 0,
            "champAffiche" => $champAffiche,
            "champRecherche" => array(),
            "tri" => "",
            "selection" => "",
        ));
        $om_table->tri = "";
        // test no sort
        $om_table->setParam("tricol", "");
        $this->assertTrue(preg_match('/ASC/', $om_table->composeTri()) === 0);
        $this->assertTrue(preg_match('/DESC/', $om_table->composeTri()) === 0);
        // test sort on col 0
        $om_table->setParam("tricol", "0");
        $this->assertTrue(preg_match('/ 0 ASC/', $om_table->composeTri()) === 1);
        $om_table->setParam("tricol", "-0");
        $this->assertTrue(preg_match('/ 0 DESC/', $om_table->composeTri()) === 1);
        // test sort on col 9
        $om_table->setParam("tricol", "9");
        $this->assertTrue(preg_match('/ 9 ASC/', $om_table->composeTri()) === 1);
        $om_table->setParam("tricol", "-9");
        $this->assertTrue(preg_match('/ 9 DESC/', $om_table->composeTri()) === 1);
        // test sort on col 10
        $om_table->setParam("tricol", "10");
        $this->assertTrue(preg_match('/ 10 ASC/', $om_table->composeTri()) === 1);
        $om_table->setParam("tricol", "-10");
        $this->assertTrue(preg_match('/ 10 DESC/', $om_table->composeTri()) === 1);
    }

}
