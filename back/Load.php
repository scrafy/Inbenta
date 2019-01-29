<?php

session_start();
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__ . "/Infraestructure/Factories/InbentaApiFactory.php");
require_once(__ROOT__ . "/Infraestructure/Factories/InbentaRepositoryFactory.php");
require_once(__ROOT__ . "/Infraestructure/Models/Output.php");
require_once(__ROOT__ . "/Infraestructure/Exceptions/RouteNotFoundException.php");
require_once("Conf.php");

/**
 * Global handler for all exception not managed. Registering this function 
 * we have  a central point where we can manage a not controlled exception
 * @author José Luis
 * @param $exception the current launched exception
 * @return A standar response notifying about of the error happened
 * */
function our_global_exception_handler($exception) {
    
    $conf = Conf::getInstance();
    $code = $exception->getCode() === 0 ? 500 : $exception->getCode();
    //$code = 200;
    if (!headers_sent()) {
        header("Access-Control-Allow-Origin: ".$conf->client_address);
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Allow: GET, POST");
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json');
        http_response_code($code);
    }
    $resp = new Output();
    $resp->err_description = $exception->getMessage();
    $resp->status = $code;
    $resp->type_exception = get_class($exception);
    echo json_encode($resp);
}

set_exception_handler("our_global_exception_handler");

//In case of we have not have setted the path which some of these values [/access, /message, /history] the application will return an standar error response
if (!isset($_SERVER["PATH_INFO"])) {
    throw new RouteNotFoundException("Route not found", 404);
}
