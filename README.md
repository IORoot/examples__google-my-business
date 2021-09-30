# Google My Business - Example PHP 

This is just a a simple repository of a few of the different methods you can use on the GMB API.
## Requirements

1. PHP v7.4+
2. Composer


## Steps before running

1. Make sure you do a `composer install` or `composer update` to download all vendor packages. 
1. Also, make sure the `client_secret.json` file is placed in the root of this folder (See below). There is a .gitignore on the file `client_secret.json`, so you don't commit it to a repo by accident.
1. You can run this with a simple PHP built-in webserver or any other. 
```bash
php -S localhost:8000
```


## Google My Business

Is one of the only client-libraries that is NOT part of the standard `google/apiclient-services`, so I've included the "bronhy/google-my-business-php-client" repo to automatically install it.

## OAUTH Keys

Head over the the google console https://console.cloud.google.com/ and create a project with the following APIs:

- Google My Business

The google my business API is a private one and you need to request access from google for it.

Next:
- Create an OAUTH 2.0 client ID.
- In the Authorised redirect URIs include:
    - http://localhost:8000
    - http://localhost:8000/index.php
- Download the JSON file and save it as `client_secret.json` and place it into the root of this folder.

## The Examples

All examples are in the `examples` folder. They are all self-contained, so each file should contain everything it needs. Pull out the one you want and place it into the root folder and rename it as`index.php`. Run your webserver and view!

## Changelog

0.0.1 - Initial.