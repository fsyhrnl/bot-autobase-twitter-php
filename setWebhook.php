<?php
require 'config.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

$content = $connection->post('account_activity/all/'.ENV_LABEL.'/webhooks', ['url' => WEBHOOK_URL]);
print_r($content);
echo "\n\n";
echo "<br>";
$content2 = $connection->post('account_activity/all/'.ENV_LABEL.'/subscriptions');
print_r($content2);
echo "\n\n";
echo "<br>";
$request_token = $connection->oauth2('oauth2/token', ['grant_type' => 'client_credentials']);
$newConnection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token->access_token);
$content3 = $newConnection->get("account_activity/all/".ENV_LABEL."/subscriptions/list");
print_r($content3);

