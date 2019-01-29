<?php

/**
 * Central class to host all necesary configuration parameters in order to the application works correctly
 *
 * @author José Luis
 */
class Conf {

    private $inbenta_auth = "https://api.inbenta.io/v1/auth";
    private $inbenta_apis = "https://api.inbenta.io/v1/apis";
    private $api_key = "nyUl7wzXoKtgoHnd2fB0uRrAv0dDyLC+b4Y6xngpJDY=";
    private $secret = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm9qZWN0IjoieW9kYV9jaGF0Ym90X2VuIn0.anf_eerFhoNq6J8b36_qbD4VqngX79-yyBKWih_eA1-HyaMe2skiJXkRNpyWxpjmpySYWzPGncwvlwz5ZRE7eg";
    private $starwar_api = "https://swapi.co/api";
    private static $instance = null;

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * During application execution, each time we need to inject a configuration instance inside of a any factory
     * we always will get the same instance. This pattern is known as singleton pattern
     * @author José Luis
     * @return An instance of Conf class only if it has not been created before
     */
    public static function getInstance() {

        if (self::$instance == null) {
            self::$instance = new Conf();
        }

        return self::$instance;
    }

}

?>
