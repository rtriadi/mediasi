<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| WA / SMS Gateway Configuration
|--------------------------------------------------------------------------
| Provider yang didukung: 'fonnte' | 'zenziva'
*/

$config['wagw_provider'] = 'fonnte';
$config['wagw_token']    = 'YOUR_FONNTE_API_TOKEN_HERE';
$config['wagw_enabled']  = TRUE;
