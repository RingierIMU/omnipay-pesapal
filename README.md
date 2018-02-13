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

1) to render the pesapal iframe

``` php
use Omnipay\Omnipay;


$iframeSrc = Omnipay::create('Pesapal')
    ->setCredentials(
        'your_key', 
        'your_secret'
    )
    ->getIframe(
        'test@example.com',
        'my_reference',
         'description',
         '1',
    );
    
    
    echo "<iframe scr=" . $iframeSrc . " />" ;
```

