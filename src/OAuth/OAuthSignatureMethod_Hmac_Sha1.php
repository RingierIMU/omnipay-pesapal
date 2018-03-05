<?php

namespace Omnipay\Pesapal\OAuth;

class OAuthSignatureMethod_Hmac_Sha1
{
    /**
     * @return string
     */
    public function get_name(): string
    {
        return 'HMAC-SHA1';
    }

    /**
     * @param $request
     * @param $consumer
     * @param $token
     *
     * @return string
     */
    public function build_signature(
        $request,
        $consumer,
        $token
    ): string {
        $base_string = $request->get_signature_base_string();
        $request->base_string = $base_string;

        $key_parts = [
            $consumer->secret,
            ($token) ? $token->secret : '',
        ];

        $key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
        $key = implode('&', $key_parts);

        return base64_encode(hash_hmac('sha1', $base_string, $key, true));
    }

    /**
     * @param $request
     * @param $consumer
     * @param $token
     * @param $signature
     *
     * @return bool
     */
    public function check_signature(
        &$request,
        $consumer,
        $token,
        $signature
    ): bool {
        $built = $this->build_signature($request, $consumer, $token);

        return $built == $signature;
    }
}
