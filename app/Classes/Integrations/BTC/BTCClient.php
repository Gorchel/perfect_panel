<?php

namespace App\Classes\Integrations\BTC;

/**
 * Class BTCClient
 * @package App\Classes\Integrations\BTC
 */
class BTCClient
{
    /**
     * @var string
     */
    protected $url;

    /**
     * BTCClient constructor.
     */
    public function __construct()
    {
        $this->url = 'https://blockchain.info/ticker';
    }

    /**
     * @return bool|string
     */
    public function getRates()
    {
        $ch = curl_init( $this->url );
        curl_setopt( $ch, CURLOPT_ENCODING, "utf-8" );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 120 );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec( $ch );
        curl_close( $ch );

        return json_decode($result, true);
    }
}
