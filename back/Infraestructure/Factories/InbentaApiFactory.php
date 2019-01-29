<?php

require_once(__ROOT__ . "/Infraestructure/Factories/InbentaRepositoryFactory.php");
require_once(__ROOT__ . "/Infraestructure/Controllers/ChatBotController.php");

/**
 * This class is a factory which let us to get a concrete instance of a controller class, letting us to inject it in anywhere
 *
 * @author José Luis
 */
class InbentaApiFactory {

    public static function create($conf) {
        return ChatBotController::getInstance(InbentaRepositoryFactory::create($conf));
    }

}
