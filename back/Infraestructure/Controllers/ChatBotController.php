<?php

require_once(__ROOT__ . "/Infraestructure/Exceptions/MessageNotSentException.php");
require_once(__ROOT__ . "/Infraestructure/Exceptions/UnauthorizedException.php");
require_once(__ROOT__ . "/Infraestructure/Interfaces/I_InbentaRepository.php");
require_once(__ROOT__ . "/Infraestructure/Models/Output.php");
require_once("Controller.php");

/**
 * Controller class to manage all actions related with inbenta API
 *
 * @author José Luis
 */
class ChatBotController extends Controller {

    private $repository = null;

    public function __construct(I_InbentaRepository $_repository, $conf) {
        parent::__construct($conf);
        $this->repository = $_repository;
    }

    /**
     * We get by use of dependency injection an object which implements the I_InbentaRepository interface. In this way, we can use the object to make request to both inbenta API
     * and swapi API
     * @param _repository We get by use of dependency injection an object which implements the I_InbentaRepository interface. 
     */
    public static function getInstance(I_InbentaRepository $_repository, $conf) {

        return new ChatBotController($_repository, $conf);
    }

    /**
     * Checks if exists a access token in the session and if it´s valid (its not expired)
     * @return A boolean indicating if the access token is valid or not
     */
    private function isAccessTokenValid() {

        if (!isset($_SESSION["access_data"])) {
            return false;
        }
        $session = json_decode($_SESSION["access_data"]);
        if ($session->access_token) {
            if ($session->access_token_expiration < strtotime(gmdate(DATE_RFC822))) {
                unset($_SESSION["access_data"]);
                return false;
            }
            return true;
        }
        unset($_SESSION["access_data"]);
        return false;
    }

    /**
     * Checks if exists a session token in the session
     * @return A boolean indicating if the session token exists in the session or not
     */
    private function isSessionTokenValid() {

        if (!isset($_SESSION["session_token"])) {
            return false;
        }
        return true;
    }

    /**
     * Checks if exists a access token in the session and if it exists, the access token is returned
     * @return the access token in case of it exists in the session or null
     */
    private function getAccessTokenFromSession() {
        if ($this->isAccessTokenValid()) {
            $session = json_decode($_SESSION["access_data"]);
            return $session->access_token;
        }
        return null;
    }

    /**
     * Checks if exists a session token in the session and if it exists, the access token is returned
     * @return the session token in case of it exists in the session or null
     */
    private function getSessionTokenFromSession() {
        if ($this->isSessionTokenValid()) {
            $session = $_SESSION["session_token"];
            return $session;
        }
        return null;
    }

    /**
     * Checks if the chatbot url exists in the session and if it exists, the chatbot url is returned else it´s requested
     * to ibenta
     * @return The chatbot url necesary to both open a conversation and send a message actions
     */
    private function getChatBotUrl() {

        if (!isset($_SESSION["chatbot_url"])) {
            $token = $this->getAccessTokenFromSession();
            if ($token) {
                $response = $this->repository->getChatBotUrl($token);
                $_SESSION["chatbot_url"] = $response->apis->chatbot;
                return $response->apis->chatbot;
            }
            throw new Exception("The access token is not valid or it has expired");
        }
        return $_SESSION["chatbot_url"];
    }

    /**
     * Open a new conversation and get from inbenta a new session token, storing it in the session
     */
    private function openConversation() {

        $access_token = $this->getAccessTokenFromSession();
        if (!$access_token) {
            throw new Exception("The access token is not valid or it has expired");
        }
        $response = $this->repository->openConversation($access_token, $this->getChatBotUrl());
        $_SESSION["session_token"] = $response->sessionToken;
    }

    /**
     * Send to inbenta a new message and gets a response from chatbot
     * @return The chatbot response got from inbenta 
     */
    public function sendMessage() {

        $aux_conversation = new stdClass();
        $aux_conversation->me = "";
        $aux_conversation->YodaBot = new stdClass();
        $aux_conversation->YodaBot->message = "";
        $aux_conversation->YodaBot->list = [];

        $aux = new stdClass();
        $aux->message = "";
        $aux->list = [];

        $message_list = [];
        $access_token = $this->getAccessTokenFromSession();

        //if access token is not valid or not exists we launch an exception
        if (!$access_token) {
            throw new UnauthorizedException("The access token is not valid or it has expired", 401);
        }
        $session_token = $this->getSessionTokenFromSession();

        //if session token is not valid or not exists we launch an exception
        if (!$session_token) {
            $this->openConversation();
            $session_token = $this->getSessionTokenFromSession();
        }

        if (!isset($_GET["message"]) || empty($_GET["message"])) {
            throw new MessageNotSentException("The message has not been sent as a body parameter", 400);
        }

        //we sanitize the message
        $message = filter_var(strip_tags($_GET["message"]), FILTER_SANITIZE_STRING);

        //if the message contains the substring force we call to SWAPI to get films list
        if (strstr($message, "force")) {
            $aux->message = "La fuerza se encuentra en estas películas...";
            $response = $this->repository->getFilms();
            //store all films´s title in our variable message_list
            foreach ($response->results as $result) {
                array_push($message_list, $result->title);
            }
        } else { //if this block is executed, it means thar the substring force has not been found in the message
            $response = $this->repository->sendMessage($message, $access_token, $session_token, $this->getChatBotUrl());
            //if the chatbot has not found a result for the message
            if ($response->answers && $response->answers[0]->flags && $response->answers[0]->flags[0] === "no-results") {
                //we controls using session the number of times that the chatbot has not returned a valid result
                if (!isset($_SESSION["not-found"])) {
                    $_SESSION["not-found"] = 1;
                } else {
                    $_SESSION["not-found"] ++;
                }
                //when the chatbot has not returned a valid result for second time, we have to call SWAPI to get a list of characters
                if ($_SESSION["not-found"] === 2) {
                    $aux->message = "No he encontrado ningún resultado, pero aquí tienes una lista de los personajes de STAR WARS...";
                    unset($_SESSION["not-found"]);
                    $response = $this->repository->getCharacters();
                    //store all characters´s names in our variable message_list
                    foreach ($response->results as $result) {
                        array_push($message_list, $result->name);
                    }
                } else { //this block is executed when is the first time that the chatbot has not found a valid result. We dont return the list of characters yet
                    $aux->message = $response->answers[0]->messageList[0];
                }
            } else { //this block is executed when chatbot has found a valid response to our message
                $aux->message = $response->answers[0]->messageList[0];
            }
        }
        //we prepare our resp object model to serialize it and send it into HTTP response
        $resp = new Output();
        $resp->status = "OK";
        $aux->list = $message_list;
        $resp->data = $aux;

        //we store the conversation in session too in order to be able to get it when the client reloads the page
        if (!isset($_SESSION["conversation"])) {
            $_SESSION["conversation"] = [];
        }
        $aux_conversation->me = $message;
        $aux_conversation->YodaBot->message = $aux->message;
        $aux_conversation->YodaBot->list = $aux->list;
        array_push($_SESSION["conversation"], $aux_conversation);
        $this->sendJsonResponse($resp);
    }

    /**
     * Get the conversation stored in session
     * @return The HTTP response reprensenting the conversation
     */
    public function getConversation() {
        $resp = new Output();
        $resp->status = "OK";
        $resp->data = [];
        if (isset($_SESSION["conversation"])) {
            $resp->data = $_SESSION["conversation"];
            $this->sendJsonResponse($resp);
        } else {
            $this->sendJsonResponse($resp);
        }
    }

    /**
     * Get the access token from inbenta
     * @return The HTTP response
     */
    public function getAccessToken() {

        $output = new Output();
        $resp = $this->repository->getAccessToken();
        $output->data = $resp;
        $output->status = "OK";
        $_SESSION["access_data"] = json_encode($resp);
        $this->sendJsonResponse($output);
    }

}
