<?php

require_once("Repository.php");
require_once(__ROOT__ . "/Infraestructure/Interfaces/I_InbentaRepository.php");

/**
 * This class represent the repository where can get the data
 *
 * @author José Luis
 */
class InbentaApiRepository extends Repository implements I_InbentaRepository {

    private $conf = null;

    public function __construct($_conf) {
        $this->conf = $_conf;
    }

    public static function getInstance($conf) {
        return new InbentaApiRepository($conf);
    }

    /**
     * Call to inbenta API to get a valid access token
     * @return Returns an object reprensenting the response got from inbenta API
     */
    public function getAccessToken() {

        $headers = [
            'x-inbenta-key: ' . $this->conf->api_key,
            'Content-Type: application/json'
        ];
        $body = [
            'secret' => $this->conf->secret
        ];
        $response = json_decode($this->makeCall($this->conf->inbenta_auth, $headers, $body));
        $resp = new stdClass();
        $resp->access_token = $response->accessToken;
        $resp->access_token_expiration = $response->expiration;
        return $resp;
    }

    /**
     * Call to inbenta API to get the chatbot URL
     * @return Returns an object reprensenting the response got from inbenta API
     */
    public function getChatBotUrl($token) {

        $headers = [
            'x-inbenta-key: ' . $this->conf->api_key,
            'Authorization: Bearer ' . $token
        ];
        return json_decode($this->makeCall($this->conf->inbenta_apis, $headers));
    }

    /**
     * Call to inbenta API to get the session token which is necesary to begin a conversation
     * @param token Represent the access token which is setted in the authorization header
     * @param url Represent the chatbot api url
     * @return Returns an object reprensenting the response got from inbenta API
     */
    public function openConversation($token, $url) {
        $headers = [
            'x-inbenta-key: ' . $this->conf->api_key,
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];
        $body = new stdClass();
        $body->lang = "es";
        return json_decode($this->makeCall($url . "/v1/conversation", $headers, $body));
    }

    /**
     * Call to inbenta API sending a message and getting a response from chatbot
     * @param message Represent message we want to send to chatbot
     * @param access_token Represent the access token which is setted in the authorization header
     * @param session_token Represent the session token which is setted in the x-inbenta-session header
     * @param url Represent the chatbot api url
     * @return Returns an object reprensenting the response got from inbenta API
     */
    public function sendMessage($message, $access_token, $session_token, $url) {
        $headers = [
            'x-inbenta-key: ' . $this->conf->api_key,
            'Content-Type: application/json',
            'x-inbenta-session: Bearer ' . $session_token,
            'Authorization: Bearer ' . $access_token
        ];
        $body = [
            'message' => $message
        ];
        return json_decode($this->makeCall($url . "/v1/conversation/message", $headers, $body));
    }

    /**
     * Call to SWAPI API to get a list of films
     * @return Returns an object reprensenting the response got from SWAPI API
     */
    public function getFilms() {
        return json_decode($this->makeCall($this->conf->starwar_api . "/films/", ["User-Agent: 0.0.1", "Accept: application/json"]));
    }

    /**
     * Call to SWAPI API to get a list of characters
     * @return Returns an object reprensenting the response got from SWAPI API
     */
    public function getCharacters() {
        return json_decode($this->makeCall($this->conf->starwar_api . "/people/", ["User-Agent: 0.0.1", "Accept: application/json"]));
    }

}
