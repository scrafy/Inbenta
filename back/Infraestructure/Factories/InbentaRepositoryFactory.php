<?php

require_once(__ROOT__ . "/Infraestructure/Repositories/InbentaApiRepository.php");

/**
 * This class is a factory which let us to get a concrete instance of a repository which implements I_InbentaRepository interface, letting us to inject it in anywhere
 *
 * @author José Luis
 */
class InbentaRepositoryFactory {

    public static function create($conf) {

        return InbentaApiRepository::getInstance($conf);
    }

}
