<?php

session_start();
require 'autoload.php';
define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('OAUTH_TOKEN', ''); 
define('OAUTH_TOKEN_SECRET', ''); 
//define('OAUTH_CALLBACK', ''); //callback url, isi jika dibutuhkan. (optional)

define('WEBHOOK_URL', ''); //https://domain-kamu.com/twitter/webhook.php
define('ENV_LABEL', ''); //Dev environment label

define('TRIGGER_WORD', 'dog');
define('ADMIN_ID', ['965702083', '', '']); //User ID Admin (optional)
define('MINIMAL_FOLLOWERS', '10');
define('SLEEP_TIME', '20'); //delay ketika post tweet (detik)

if (!CONSUMER_KEY || !CONSUMER_SECRET || !WEBHOOK_URL || !ENV_LABEL) {
    echo 'CONSUMER_KEY, CONSUMER_SECRET, WEBHOOK_URL, and ENV_LABEL variables must be set';
}
