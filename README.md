# Omnipay: Pesapal

**Pesapal driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/thephpleague/omnipay-paypal.png?branch=master)](https://travis-ci.org/thephpleague/omnipay-paypal)
[![Latest Stable Version](https://poser.pugx.org/omnipay/paypal/version.png)](https://packagist.org/packages/omnipay/paypal)
[![Total Downloads](https://poser.pugx.org/omnipay/paypal/d/total.png)](https://packagist.org/packages/omnipay/paypal)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayPal support for Omnipay.
This package is a driver for pesapal https://www.pesapal.com/
## Install

Via Composer

``` bash
$ composer require oneafricamedia/omnipay-pesapal
```

## Basic Usage

###to render the pesapal iframe

``` php
use Omnipay\Omnipay;


$iframeSrc = Omnipay::create('Pesapal')
    ->setCredentials(
        'your_key', 
        'your_secret'
    )
    ->setCallbackUrl('https://example.com/callback')
    ->getIframeSrc(
        'test@example.com',
        'my_reference',
        'description',
        100
    );
    
    
     echo "<iframe src='$iframeSrc' />";
```

### to check transaction history (from the pesapal ipn)

1) configure & setup an endpoint to receive the ipn message from pesapal
2) listen for the message and use `getTransactionStatus` (please handle the http GET vars accordingly)

``` php
use Omnipay\Omnipay;


$status = Omnipay::create('Pesapal')
    ->setCredentials(
        'your_key', 
        'your_secret'
    )
    ->getTransactionStatus(
        $_GET['pesapal_notification_type'],
        $_GET['pesapal_transaction_tracking_id'],
        $_GET['pesapal_transaction_tracking_id']
    );
    
```
3) `$status` will be either `PENDING`, `COMPLETED`, `FAILED` or `INVALID`. Handle these statuses in your application workflow accordingly.

