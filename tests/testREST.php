<?php
/**
 * Ce fichier regroupe les tests des requêtes REST sortantes de la classe
 * rest_api.
 *
 * @package framework_openmairie
 * @version SVN : $Id: testREST.php 4369 2018-12-02 22:23:57Z fmichon $:
 */

require_once "../app/framework_openmairie.class.php";
require_once PATH_OPENMAIRIE."om_rest_client.class.php";

/**
 * Cette classe contient des méthodes génériques d'utilisation de la classe
 * rest_api ainsi que les tests cases.
 * Les tests utilisent la ressource REST publique JSONPlaceholder.
 *
 */
class RESTTest extends PHPUnit\Framework\TestCase {

    var $base_url = '';

    /**
     * Instance de la classe om_rest_client.
     *
     * @var null
     */
    var $inst_om_rest_client = null;


    /**
     * Méthode lancée en début de traitement
     *
     * @return void
     */
    public function setUp() {
        // URL de la ressource REST de test
        $this->base_url = 'https://jsonplaceholder.typicode.com/';
        // Instancie la timezone
        date_default_timezone_set('Europe/Paris');
        echo ' = '.get_class().'.'.str_replace('test_', '', $this->getName())."\r\n";
        if ($this->inst_om_rest_client === null) {
            $this->inst_om_rest_client = new om_rest_client(null);
        }
        // Permet d'avoir un message d'erreur cURL lors d'un retour 404
        curl_setopt(
            $this->inst_om_rest_client->getCurl(),
            CURLOPT_FAILONERROR,
            true
        );
    }


    /**
     * Méthode étant appelée lors du fail d'un test.
     * 
     * @param $e Exception remontée lors du test
     * @return void
     */
    public function onNotSuccessfulTest($e){
        echo 'Line '.$e->getLine().' : '.$e->getMessage()."\r\n";
        parent::onNotSuccessfulTest($e);
    }


    /**
     * Test de la méthode GET.
     *
     * @return void
     */
    public function test_get_method() {
        $url = $this->base_url.'posts/1';
        $response = $this->inst_om_rest_client->execute(
            "GET",
            "",
            array(),
            $url,
            array()
        );

        // Vérification que le code retour est 200. Si ce n'est pas le cas, le
        // message d'erreur cURL est affiché dans la console
        $this->assertEquals(
            200,
            $this->inst_om_rest_client->getResponseCode(),
            $this->inst_om_rest_client->getErrorMessage()
        );

        $expected_return = array(
            "userId" => 1,
            "id" => 1,
            "title" => "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
            "body" => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"
        );
        $this->assertEquals($expected_return, $response);
    }


    /**
     * Test de la méthode POST.
     *
     * @return void
     */
    public function test_post_method() {
        $url = $this->base_url . 'posts';
        $data = array(
            "title" => 'foo',
            "body" => 'bar',
            "userId" => 20
        );
        $response = $this->inst_om_rest_client->execute(
            "POST",
            "application/json; charset=UTF-8",
            json_encode($data),
            $url,
            array(
                "Accept: application/json",
                "Accept-charset: UTF-8"
            )
        );

        // Vérification que le code retour est 201. Si ce n'est pas le cas, le
        // message d'erreur cURL est affiché dans la console
        $this->assertEquals(
            201,
            $this->inst_om_rest_client->getResponseCode(),
            $this->inst_om_rest_client->getErrorMessage()
        );

        $expected_return = array(
            "id" => 101,
            "title" => 'foo',
            "body" => 'bar',
            "userId" => 20
        );
        $this->assertEquals($expected_return, $response);
    }


    /**
     * Test de la méthode PUT.
     *
     * @return void
     */
    public function test_put_method() {

        $url = $this->base_url . 'posts/2';
        $data = array(
            "id" => 2,
            "title" => 'foo',
            "body" => 'bar',
            "userId" => 20
        );
        $response = $this->inst_om_rest_client->execute(
            "PUT",
            "application/json; charset=UTF-8",
            json_encode($data),
            $url,
            array(
                "Accept: application/json",
                "Accept-charset: UTF-8"
            )
        );

        // Vérification que le code retour est 200. Si ce n'est pas le cas, le
        // message d'erreur cURL est affiché dans la console
        $this->assertEquals(
            200,
            $this->inst_om_rest_client->getResponseCode(),
            $this->inst_om_rest_client->getErrorMessage()
        );

        $expected_return = array(
            "id" => 2,
            "title" => 'foo',
            "body" => 'bar',
            "userId" => 20
        );
        $this->assertEquals($expected_return, $response);
    }


    /**
     * Test de la méthode DELETE.
     *
     * @return void
     */
    public function test_delete_method() {
        $url = $this->base_url . 'posts/1';
        $response = $this->inst_om_rest_client->execute(
            "DELETE",
            "",
            array(),
            $url,
            array()
        );

        // Vérification que le code retour est 200. Si ce n'est pas le cas, le
        // message d'erreur cURL est affiché dans la console
        $this->assertEquals(
            200,
            $this->inst_om_rest_client->getResponseCode(),
            $this->inst_om_rest_client->getErrorMessage()
        );

        // Le retour doit être un tableau vide
        $this->assertEquals($response, array());
    }


}
