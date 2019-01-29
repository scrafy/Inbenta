<?php

require_once(__ROOT__ . "/Infraestructure/Exceptions/RouteNotFoundException.php");
require_once(__ROOT__ . "/Infraestructure/Models/Output.php");

/**
 * Base class which all controller must inherit
 *
 * @author José Luis
 */
abstract class Controller {

    public function __construct() {
        $this->isPreflightRequest();
    }

    /**
     * Check if the request is a preflight request. In case of it is, we return a 200 HTTP response witout execute any bussines logic
     * @return Returns a HTTP 200 Ok response without body
     */
    private function isPreflightRequest() {
        $headers = getallheaders();
        if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
            $this->sendJsonResponse();
            exit;
        }
    }

    /**
     * Generic method to return a HTTP response to the client used by all child classes
     * @return Returns a HTTP 200 Ok response to the client
     */
    protected function sendJsonResponse($body = null) {

        if (!headers_sent()) {
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: http://inbenta.surge.sh');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            header("Allow: GET, POST, OPTIONS");
            header("Access-Control-Allow-Credentials: true");
            http_response_code(200);
        }
        if ($body != null) {

            echo json_encode($body);
        }
    }

}
