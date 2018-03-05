<?php

namespace Omnipay\Pesapal\OAuth;

class OAuthConsumer
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $secret;

    /**
     * @param $key
     * @param $secret
     * @param null $callback_url
     */
    public function __construct(
        $key,
        $secret,
        $callback_url = null
    ) {
        $this->key = $key;
        $this->secret = $secret;
        $this->callback_url = $callback_url;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "OAuthConsumer[key=$this->key,secret=$this->secret]";
    }
}
