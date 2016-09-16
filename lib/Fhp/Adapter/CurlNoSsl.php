<?php

namespace Fhp\Adapter;

use Fhp\Adapter\Exception\AdapterException;

class CurlNoSsl extends Curl
{

    /**
     * Curl constructor.
     *
     * @param string $host
     * @param int    $port
     *
     * @throws AdapterException
     */
    public function __construct($host, $port)
    {

        parent::__construct($host, $port);

        curl_setopt($this->curlHandle, CURLOPT_SSLVERSION, 0);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYHOST, 0);

    }
}