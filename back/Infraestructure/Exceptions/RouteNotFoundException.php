<?php

/**
 * Represent a custmon exception
 *
 * @author José Luis
 */
class RouteNotFoundException extends Exception {

    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }

}
