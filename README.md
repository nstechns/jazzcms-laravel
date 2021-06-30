![JazzCMS](.github/logo.png?raw=true)

# Jazz CMS, Laravel (SMS API)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nstechns/jazzcms-laravel.svg?style=flat-square)](https://packagist.org/packages/nstechns/jazzcms-laravel)
![](https://github.com/nstechns/jazzcms-laravel/workflows/Run%20Tests/badge.svg?branch=master)
[![Latest Version](https://img.shields.io/github/release/nstechns/jazzcms-laravel.svg?style=flat-square)](https://github.com/nstechns/jazzcms-laravel/releases)
[![Build Status](https://img.shields.io/github/workflow/status/nstechns/jazzcms-laravel/CI?label=ci%20build&style=flat-square)](https://github.com/nstechns/jazzcms-laravel/actions?query=workflow%3ACI)
[![Quality Score](https://img.shields.io/scrutinizer/g/nstechns/jazzcms-laravel.svg?style=flat-square)](https://scrutinizer-ci.com/g/nstechns/jazzcms-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/nstechns/jazzcms-laravel.svg?style=flat-square)](https://packagist.org/packages/nstechns/jazzcms-laravel)

This package is use for Send SMS, Received SMS, Balance Inquiry, Send SMS Group, send SMS International, Schedule Job using Jazz / Mobilink api.

## Installation
You can install the package via [Composer](https://getcomposer.org/):
``` bash
composer require nstechns/jazzcms-laravel
```
Now add the service provider in `config/app.php` file:
```php
'providers' => [
    // ...
    NsTechNs\JazzCMS\JazzCMSServiceProvider::class,
];
```


You can publish the config-file with:
```bash
php artisan vendor:publish --provider="NsTechNs\JazzCMS\JazzCMSServiceProvider" --tag="config"
```
This is the contents of the published `config/jazz-cms.php` config file:
```php
return [
    /*
    * In order to integrate the JAZZ - Campaign Management Solution into your site,
    * you'll need to connect to JAZZ/ Mobilink company to get the credential of SMS API
    *
    * Using environment variables is the recommended way of
    * storing your username, password and mask. Make sure to update
    * your /.env file with your USERNAME, PASSWORD and From MASK.
    */

    'base_url' => env('JAZZ_CMS_URL', 'https://connect.jazzcmt.com'),

    'username' => env('JAZZ_CMS_USERNAME'),

    'password' => env('JAZZ_CMS_PASSWORD'),

    'from' => env('JAZZ_CMS_MASK'),

    'is_urdu' => env('JAZZ_IS_URDU', false),

    'show_status' => env('JAZZ_SHOW_STATUS', true),

    'short_code' => env('JAZZ_SHORT_CODE'),
];
```
You need to set these variables in .env file which you can get from Jazz/ Mobilink:
```bash
JAZZ_CMS_USERNAME=030xxxxxxxx
JAZZ_CMS_PASSWORD=YourPassword
JAZZ_CMS_MASK=MyMask
```
If you need implement to Received SMS API then you also get JAZZ Short Code:
```bash
JAZZ_SHORT_CODE=7005XXX
```
## Usage

### Send SMS 
you can send sms to local numbers with in country:

```php
use NsTechNs\JazzCMS\JazzCMS;
$response = (new JazzCMS)->sendSMS("030xxxxxxxx","message text");
// OR with extra parameters 
$response = (new JazzCMS)->sendSMS("030xxxxxxxx","message text", "identifier", "unique_id", "product_id", "channel", "transaction_id");

```

#### Response: 
```php
 ^ {#173 ▼
  +"request": array:7 [▼
    "request_size" => 310
    "curl_error" => ""
    "base_url" => "https://connect.jazzcmt.com"
    "content_type" => "text/html; charset=UTF-8"
    "redirect_count" => 0
    "effective_url" => "https://connect.jazzcmt.com/sendsms_xml.html"
    "total_time" => 1.511816
  ]
  +"http_code": 200
  +"data": array:7 [▼
    "statuscode" => "300"
    "statusmessage" => "Message Sent Successfully!"
    "messageid" => "31"
    "originator" => "MyMask"
    "recipient" => "9230xxxxxxxx"
    "responsedatetime" => "2021-06-30 17:13:53"
    "messagedata" => "testing"
  ]
  +"status": "success"
}

```
### Received SMS
From Date and To Date is Optional: Date Format: 2021-06-30 HH:MM:SS
```php
use NsTechNs\JazzCMS\JazzCMS;
$response = (new JazzCMS)->receivingSMS("From Date", "To Date");
```

### Balance Inquiry
Check you current balance of messages.
```php
use NsTechNs\JazzCMS\JazzCMS;
$response = (new JazzCMS)->balanceInquiry();
```

### Send SMS Group
You can send sms as group you can create group in Jazz/ Mobilink.
```php
use NsTechNs\JazzCMS\JazzCMS;
$response = (new JazzCMS)->sendSMSGroups("Group Name", "Message Text");
```

### send SMS International
You can send sms to international numbers.
```php
use NsTechNs\JazzCMS\JazzCMS;
$response = (new JazzCMS)->sendSMSInternational("030xxxxxxxx", "Message Text");
```

### Schedule Job
You can set Schedule Job as list of file contacts. Schedule Date Time Format: 2021-06-30 HH:MM:SS
The Schedule Date Time is optional you can send sms immediately
```php
use NsTechNs\JazzCMS\JazzCMS;
$response = (new JazzCMS)->scheduleJob("file_with_path", "Message Text", "Schedule Date Time");
```

## Change log
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing
``` bash
composer test
```

## Security
If you discover any security related issues, please email [nstech42@gmail.com](mailto:nstech42@gmail.com) instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
