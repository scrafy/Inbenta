<?php

require_once("Load.php");

//Get the route path from $_SERVER variable in to know what action we have to execute
$path = $_SERVER["PATH_INFO"];

//Basic roputing system
switch ($path) {
    case '/history' :
        InbentaApiFactory::create(Conf::getInstance())->getConversation();
        break;
    case '/access' :
        InbentaApiFactory::create(Conf::getInstance())->getAccessToken();
        break;
    case '/message' :
        InbentaApiFactory::create(Conf::getInstance())->sendMessage();
        break;
    default:
        throw new RouteNotFoundException("Route not found", 404);
}
?>