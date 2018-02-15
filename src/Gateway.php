<?php

namespace Omnipay\Pesapal;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Omnipay\Common\AbstractGateway;
use Omnipay\Pesapal\OAuth\OAuthConsumer;
use Omnipay\Pesapal\OAuth\OAuthException;
use Omnipay\Pesapal\OAuth\OAuthRequest;
use Omnipay\Pesapal\OAuth\OAuthSignatureMethod_Hmac_Sha1;

/**
 * @method authorize(array $options = array())
 * @method completeAuthorize(array $options = array())
 * @method capture(array $options = array())
 * @method purchase(array $options = array())
 * @method completePurchase(array $options = array())
 * @method refund(array $options = array())
 * @method void(array $options = array())
 * @method createCard(array $options = array())
 * @method updateCard(array $options = array())
 * @method deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    const XMLNS = 'http://www.pesapal.com';
    const PROD_DOMAIN = 'www.pesapal.com';
    const DEBUG_DOMAIN = 'https://demo.pesapal.com';

    /**
     * @return string
     */
    public function getName()
    {
        return 'Pesapal';
    }

    /**
     * @param string $key
     * @param string $secret
     *
     * @return Gateway
     */
    public function setCredentials(
        string $key,
        string $secret
    ): self {
        return $this->setParameter('key', $key)
            ->setParameter('secret', $secret);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getParameter('key');
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->getParameter('secret');
    }

    /**
     * @return Gateway
     */
    public function setDebug(): self
    {
        return $this->setParameter('debug', true);
    }

    /**
     * @return bool
     */
    public function getDebug(): bool
    {
        return $this->getParameter('debug') ?? false;
    }

    /**
     * @param string $callbackUrl
     *
     * @return Gateway
     */
    public function setCallbackUrl(string $callbackUrl): self
    {
        return $this->setParameter('callbackUrl', $callbackUrl);
    }

    /**
     * @return string|null
     */
    public function getCallbackUrl()
    {
        return $this->getParameter('callbackUrl');
    }

    /**
     * @param string      $email
     * @param string      $reference
     * @param string      $description
     * @param float       $amount
     * @param string|null $currency
     * @param string      $type
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $phoneNumber
     *
     * @return string
     */
    public function getIframeSrc(
        string $email,
        string $reference,
        string $description,
        float $amount,
        string $currency = null,
        string $type = 'MERCHANT',
        string $firstName = null,
        string $lastName = null,
        string $phoneNumber = null
    ): string {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <PesapalDirectOrderInfo
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            Currency="' . $currency . '"
            Amount="' . $amount . '"
            Description="' . $description . '"
            Type="' . $type . '"
            Reference="' . $reference . '"
            FirstName="' . $firstName . '"
            LastName="' . $lastName . '"
            Email="' . $email . '"
            PhoneNumber="' . $phoneNumber . '"
            xmlns="' . $this::XMLNS . '" />';

        return (string) $this->getIframeRequest(htmlentities($xml));
    }

    /**
     * @return OAuthConsumer
     */
    protected function getConsumer(): OAuthConsumer
    {
        return new OAuthConsumer(
            $this->getKey(),
            $this->getSecret(),
            $this->getCallbackUrl()
        );
    }

    /**
     * @param string $xmlPayload
     *
     * @return OAuthRequest
     */
    protected function getIframeRequest(
        string $xmlPayload
    ): OAuthRequest {
        $consumer = $this->getConsumer();
        //post transaction to pesapal
        $iframeRequest = OAuthRequest::getRequest(
            $consumer,
            $this->getApiDomain() . '/API/PostPesapalDirectOrderV4'
        );
        $iframeRequest->set_parameter('oauth_callback', $this->getCallbackUrl());
        $iframeRequest->set_parameter('pesapal_request_data', $xmlPayload);
        $iframeRequest->sign_request(new OAuthSignatureMethod_Hmac_Sha1(), $consumer);

        return $iframeRequest;
    }

    /**
     * Process the pin message frmo pesapal,
     * this will query the pesapal api and
     * return a transaction status.
     *
     * @param string $type
     * @param string $id
     * @param string $reference
     *
     * @throws OAuthException
     *
     * @return string
     */
    public function getTransactionStatus(
        string $type,
        string $id,
        string $reference
    ): string {
        $response = null;
        if (
            $type == 'CHANGE'
            && !empty($id)
        ) {
            // get transaction status
            $statusRequest = OAuthRequest::getRequest(
                $this->getConsumer(),
                $this->getApiDomain() . '/api/querypaymentstatus'
                );
            $statusRequest->set_parameter('pesapal_merchant_reference', $reference);
            $statusRequest->set_parameter('pesapal_transaction_tracking_id', $id);
            $statusRequest->sign_request(new OAuthSignatureMethod_Hmac_Sha1(), $this->getConsumer());

            $client = new Client();
            $response = $client
                ->send(
                    new Request(
                        'GET',
                        (string) $statusRequest
                    )
                );

            if (!$response) {
                throw new OAuthException($response);
            }
        }

        return $this->transformResponse($response);
    }

    /**
     * @return string
     */
    protected function getApiDomain(): string
    {
        return $this->getDebug()
            ? $this::DEBUG_DOMAIN
            : $this::PROD_DOMAIN;
    }

    /**
     * @param $response
     *
     * @return string
     */
    protected function transformResponse($response): string
    {
        if (empty($response)) {
            return 'INVALID';
        }

        return str_replace(
            'pesapal_response_data=',
            '',
            $response
                ->getBody()
                ->getContents()
        );
    }
}
