<?php

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
];
