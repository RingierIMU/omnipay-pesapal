# Omnipay: Pesapal

**Pesapal driver for the Omnipay PHP payment processing library**

[![Maintainability](https://api.codeclimate.com/v1/badges/0b7329e3c725e30c4344/maintainability)](https://codeclimate.com/github/lucidlogic/omnipay-pesapal/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/0b7329e3c725e30c4344/test_coverage)](https://codeclimate.com/github/lucidlogic/omnipay-pesapal/test_coverage)
[![Style CI](https://styleci.io/repos/121246094/shield)](https://styleci.io/repos/121246094/shield)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements Pesapal support for Omnipay. https://www.pesapal.com/
refer to the API docs here: http://http://developer.pesapal.com/
## Install

Via Composer

``` bash
$ composer require oneafricamedia/omnipay-pesapal
```

## Basic Usage

### Render the pesapal iframe

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

### Check transaction status (from the pesapal ipn)

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

