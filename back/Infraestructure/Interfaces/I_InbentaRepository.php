<?php

/**
 * All repositories which need to blow inbenta API, have to implement this interface
 *
 * @author José Luis
 */
interface I_InbentaRepository {

    public function getAccessToken();

    public function openConversation($token, $url);

    public function getChatBotUrl($token);

    public function sendMessage($message, $access_token, $session_token, $url);
    
    public function getFilms();
    
    public function getCharacters();
}
