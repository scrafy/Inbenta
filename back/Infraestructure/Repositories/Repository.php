<?php

/**
 * This class represent the base class repository. All repositories implementations have to inherit from this class
 *
 * @author José Luis
 */
abstract class Repository {

    public abstract static function getInstance($conf);

    /**
     * Generic method to make a CURL request. It lets both GET and POST requests
     * @param $url the endpoint which we want to call
     * @param $header set of headers into request
     * @param $body body of the request. Normally in a json format
     * @return Returns the HTTP response
     */
    protected function makeCall($url, $headers = null, $body = null) {
        if (!$url || !is_string($url) || !preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
            throw new Exception("url not valid: " . $url);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (($body != null)) {
            $data = json_encode($body);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            if ($headers) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Content-Length: ' . strlen($data)]));
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            if ($headers) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
        }
        $resp = curl_exec($ch);
        if ($resp === false) {
            curl_close($ch);
            throw new Exception(curl_error($ch));
        }
        if (strstr($resp, "errors")) {
            $resp = json_decode($resp);
            if ($resp->errors) {
                throw new RouteNotFoundException($resp->errors[0]->message, $resp->errors[0]->code);
            }
        }
        curl_close($ch);
        return $resp;
    }

}
