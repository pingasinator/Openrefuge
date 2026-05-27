<?php
/**
 * Ce script contient la définition de la classe 'om_rest_client'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_rest_client.class.php 4348 2018-07-20 16:49:26Z softime $
 */

/**
 *
 */
if (defined("PATH_OPENMAIRIE") !== true) {
    /**
     * @ignore
     */
    define("PATH_OPENMAIRIE", "");
}
require_once PATH_OPENMAIRIE."om_logger.class.php";

/**
 * Définition de la classe 'om_rest_client'.
 *
 * Classe générique d'envoi de requêtes REST avec cURL.
 */
class om_rest_client {

    /**
     * Adresse URL qui sera ciblée lors de l'appel à la fonction execute() si
     * un autre URL n'est pas précisé lors de l'appel.
     * @var string
     */
    private $url;

    /**
     * Username utilisé dans le contexte d'authentification HTTP Basic.
     * @var string
     */
    private $username;

    /**
     * Password utilisé dans le contexte d'authentification HTTP Basic.
     * @var string
     */
    private $password;

    /**
     * Identifiant de session cURL.
     * @var string
     */
    private $curl;

    /**
     * Charset envoyé dans le header des requêtes HTTP.
     * @var string
     */
    private $charset;

    /**
     * Taille maximum allouée aux données du log en octets.
     * @var integer
     */
    private $max_log_size = 500;

    /**
     * Contenu des headers content_type et http_code.
     * @var array
     */
    private $headers = array(
        'content_type' => "",
        'http_code' => "",
    );

    /**
     * Contenu de la réponse de la requête REST.
     * @var array
     */
    private $response = "";

    /**
     * Contient les différents content-types acceptés en retour des requêtes
     * envoyées par om_rest_client.
     * @var array
     */
    private $acceptedContentType = array(
        'application/xml' => 'xml',
        'text/xml' => 'xml',
        'application/json' => 'json',
        'text/json' => 'json',
        'application/html' => 'html',
        'text/html' => 'html',
        'application/pdf' => 'pdf',
        'text/pdf' => 'pdf',
        'text/plain' => 'plain',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpeg',
        'image/png' => 'png',
        'application/base64' => 'base64',
    );


    /**
     * Constructeur
     *
     * @param string $url      URL cible des requêtes qui seront envoyées par
     * cette instance d'om_rest_client.
     * @param string $username Optionnel : username en cas d'authentification
     * basic.
     * @param string $password Optionnel : password en cas d'authentification
     * basic.
     */
    public function __construct($url, $username = "", $password = "") {
        //Initialisation des variables de la classe
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;

        //Initialisation de la session curl
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, true);

        //Tente une authentification si les options ont été fournies
        if ($this->username !== "" && $this->password !== "") {
            curl_setopt($this->curl, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
            curl_setopt($this->curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }
    }


    /**
     * Lance l'appel au webservice
     *
     * @param string $method      Type de verbe HTTP (GET, POST, ...).
     * @param string $contentType Contenu du header HTTP content-type.
     * @param string $data        Données à envoyer dans le cas d'un POST.
     * @param string $url         URL cible de la requête. null peut-être passé pour que
     * l'URL défini lors de l'instanciation de la classe soit utilisé.
     * @param array  $headers     Autres headers à passer dans la requête.
     *
     * @return Retourne la réponse du webservice ou -1 en cas d'erreur
     */
    public function execute($method, $contentType, $data, $url = null, $headers = array()){
        //
        $data_log = print_r($data, true);
        $max = $this->getMaxLogSize();
        $data_log = strlen($data_log) > $max ?
            substr($data_log, 0, $max)."[...]" : $data_log;
        $this->log(
            sprintf(
                '%s / url : %s / method : %s / data : %s',
                __METHOD__,
                $this->clean_url($this->getUrl()),
                $method,
                $data_log
            )
        );

        //
        if ($url != null) {
            $this->setUrl($url);
        }

        //L'URL d'envoi doit être non vide
        if (strcmp($this->getUrl(), "")==0) {
            return -1;
        }

        //Formatage des données passées en paramètre
        if (is_array($data) === true) {
            $data = http_build_query($data);
        }

        // XXX
        // Méthode temporaire permettant de passer autre chose que content-type en header
        // de la requête, on fusionne $contentType et $headers
        $http_headers = array_merge(array("Content-Type: ".$contentType), $headers);
        curl_setopt ($this->getCurl(), CURLOPT_HTTPHEADER, $http_headers);

        // DEBUG
        if (file_exists("../services/debug")) {
            $t = explode(" ", microtime());
            $d = new DateTime();
            $time_identifier = $d->format('Ymd-His-').$t[0];
            file_put_contents(
                sprintf(
                    "../var/log/messagesend-%s-%s-data.txt",
                    $time_identifier,
                    $method
                ),
                $data
            );
        }

        //Ajoute les certaines options à la session curl selon la méthode d'envoi
        //souhaitée
        switch ($method) {
            case 'GET':
                curl_setopt($this->getCurl(), CURLOPT_HTTPGET, true);
                break;
            case 'PUT':
                curl_setopt($this->getCurl(), CURLOPT_PUT, true);
                //Ajout du xml dans un fichier ataché à la requête
                //Crée un fichier temporaire
                $putData = tmpfile();
                //Ecrit les données dans le fichier temporaire
                fwrite($putData, $data);
                //Place le curseur au début du fichier
                fseek($putData, 0);
                //Le fichier lu par le transfert lors du chargement
                curl_setopt($this->getCurl(), CURLOPT_INFILE, $putData);
                //Taille du fichier en octet attendue
                curl_setopt($this->getCurl(), CURLOPT_INFILESIZE, strlen($data));
                break;
            case 'POST':
                curl_setopt($this->getCurl(), CURLOPT_POST, true);
                curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($this->getCurl(), CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            //Si aucune méthode n'a été fournie
            default:
                return -1;
        }
        // une reponse est attendue
        curl_setopt($this->getCurl(), CURLOPT_RETURNTRANSFER, true);
        //Ajoute l'url
        curl_setopt($this->getCurl(), CURLOPT_URL, $this->getUrl());
        $this->setResponse(curl_exec($this->getCurl()));

        $this->treatResponse();
        //
        $response_log = print_r($this->getResponse(), true);
        $max = $this->getMaxLogSize();
        $response_log = strlen($response_log) > $max ?
            substr($response_log, 0, $max)."[...]" : $response_log;
        $this->log(
            sprintf(
                '%s / url : %s / method : %s / response : %s',
                __METHOD__,
                $this->clean_url($this->getUrl()),
                $method,
                $response_log
            )
        );

        // DEBUG
        if (file_exists("../services/debug")) {
            if (!isset($time_identifier)) {
                $time_identifier = "na";
            }
            file_put_contents(
                sprintf(
                    "../var/log/messagesend-%s-%s-response.txt",
                    $time_identifier,
                    $method
                ),
                print_r($this->getResponse(), true)
            );
        }

        //
        return $this->getResponse();
    }

    /**
     * Va traiter le retour de l'exécution d'une requête afin d'en extraire
     * les headers et la réponse.
     *
     * @return null
     */
    private function treatResponse(){
        //
        $headers = array();
        //Si la réponse n'est pas vide
        if ($this->getResponse() !== null) {
            //Récupération des informations du retour de la requête
            $response_info = curl_getinfo($this->getCurl());

            //Si la requête ne retourne aucune information
            if (!is_array($response_info)) {
                return;
            }

            //Récupère le content-type et le code HTTP de la requête réponse
            $headers['content_type'] = $response_info['content_type'];
            $headers['http_code'] = $response_info['http_code'];
            $this->setHeaders($headers);

            $contentType = $headers['content_type'];

            if ($contentType !== '' AND $contentType !== null) {
                // Si le header content-type contient un charset et que celui-ci est différent
                // de chaine vide, alors on le met dans $charset
                $regex = "/([\w-\/]*);?\s*(?:charset=(.*))?/";
                if (preg_match($regex, $contentType, $matches)) {
                    $contentType = $matches[1];
                    if (isset($matches[2]) AND $matches[2] !== '') {
                        $this->setCharset($matches[2]);
                    }
                }
            }

            //Décomposition des données selon le type de retour
            $acceptedContentType = $this->getAcceptedContentType();
            if (isset($acceptedContentType[$contentType]) === true) {
                $contentType = $acceptedContentType[$contentType];
            } else {
                $contentType = '';
            }

            switch ($contentType) {
                case 'xml':
                    $this->setResponse((array) simplexml_load_string($this->getResponse(), 'SimpleXMLElement', LIBXML_NOCDATA));
                    break;
                case 'json':
                    $this->setResponse((array) json_decode(trim($this->getResponse()), true));
                    break;
                //Si la réponse n'est pas un des formats supportés
                default:
                    // Le header et le contenu du retour sont séparés par une ligne vide
                    $parts  = explode("\n\r", $this->getResponse());

                    //Recompose la réponse dans le cas où elle contiendrait des
                    //sauts de lignes
                    $this->setResponse("");
                    for ($i = 0; $i < count($parts); $i++) {
                        if ($i > 0) {
                            $this->setResponse($this->getResponse()."\n\r") ;
                        }
                        $this->setResponse($this->getResponse().$parts[$i]) ;
                    }
                    break;
            }
        }
    }

    /**
     * Destructeur
     */
    public function __destruct() {
        curl_close($this->curl);
    }

    /**
     * Ferme la connexion curl
     * @return void
     */
    public function close() {
        curl_close($this->curl);
    }

    // {{{ Accesseur
    /**
     * Accesseur de la donnée url
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Accesseur de l'objet curl
     * @return object
     */
    public function getCurl() {
        return $this->curl;
    }

    /**
     * Accesseur de la donnée headers
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Accesseur de la donnée response
     * @return string
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Accesseur du code HTTP défini dans la donnée headers
     * @return numeric
     */
    public function getResponseCode() {
        return $this->headers['http_code'];
    }

    /**
     * Accesseur du content-type défini dans la donnée headers
     * @return string
     */
    public function getResponseContentType() {
        return $this->headers['content_type'];
    }

    /**
     * Accesseur de la donnée acceptedContentType
     * @return array
     */
    public function getAcceptedContentType() {
        return $this->acceptedContentType;
    }

    /**
     * Accesseur du message d'erreur curl de l'instance courante
     * @return string
     */
    public function getErrorMessage() {
        return curl_error($this->getCurl());
    }
    // }}}

    // {{{ Mutateur
    /**
     * Met à jour le header.
     * @param array $headers Tableau associatif de headers HTTP.
     * @return void
     */
    public function setHeaders($headers) {
        $this->headers=$headers;
    }

    /**
     * Met à jour l'URL.
     * @param string $url URL qui sera ciblée par défaut lors d'un appel de la
     * méthode execute si l'URL n'est pas passé lors l'appel.
     * @return void
     */
    public function setUrl($url) {
        $this->url=$url;
    }

    /**
     * Met à jour la réponse.
     * @param string $response Réponse retournée par l'exécution de la requête.
     * @return void
     */
    public function setResponse($response) {
        $this->response=$response;
    }

    /**
     * Met à jour les types de contenu acceptés.
     * @param array $acceptedContentType Setter de la donnée acceptedContentType
     * qui est un tableau de valeur des différents contenus acceptés par la
     * classe.
     * @return void
     */
    public function setAcceptedContentType($acceptedContentType) {
        $this->acceptedContentType=$acceptedContentType;
    }

    /**
     * Met à jour le charset.
     * @param string $charset Setter du charset envoyé dans le header des
     * requêtes HTTP.
     * @return void
     */
    public function setCharset($charset) {
        $this->charset=$charset;
    }

    /**
     * Accesseur retournant le charset.
     * @return string
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * Accesseur retournant la taille maximum alloué aux données du log.
     * @return integer (en octets)
     */
    public function getMaxLogSize() {
        return $this->max_log_size;
    }

    // }}}

    /**
     * Nettoyer une URL des informations d'authentification HTTP en les
     * remplaçant par ...
     *
     * @param string $url URL à nettoyer.
     * @return string URL nettoyée
     */
    function clean_url($url) {
        return preg_replace("#(?<=://)[^:@]+(:[^@]+)?(?=@)#", "...", $url);
    }

    /**
     * Nettoyer une URL des informations d'authentification HTTP en les
     * remplaçant par ...
     *
     * @param string $message Écriture de la chaîne de caractères passé en
     * paramètre .
     * @return void
    */
    function log($message) {
        logger::instance()->log_to_file("services.log", "OUT - ".$message);
    }

}
