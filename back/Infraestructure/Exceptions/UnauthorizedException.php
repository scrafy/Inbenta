<?php

/**
 * Represent a custmon exception
 *
 * @author José Luis
 */
class UnauthorizedException extends Exception {

    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }

}
