<?php

# depends on https://github.com/adoy/PHP-OAuth2

set_include_path (__DIR__.'/PHP-OAuth2/src/OAuth2');

if (isset($_GET["error"]))
{
    echo("<pre>OAuth Error: " . $_GET["error"]."\n");
    echo('<a href="index.php">Retry</a></pre>');
    die;
}

$authorizeUrl = 'https://sandbox.gloebit.com/oauth2/authorize';
$accessTokenUrl = 'https://sandbox.gloebit.com/oauth2/access-token';
$clientId = 'test-consumer';
$clientSecret = 's3cr3t';
$redirectUrl = "http://localhost/gloebit-php-example.php";

require ("Client.php");
require ("GrantType/IGrantType.php");
require ("GrantType/AuthorizationCode.php");

$client = new OAuth2\Client
  ($clientId,
   $clientSecret,
   OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);

if (!isset($_GET["code"]))
{
    $authUrl = $client->getAuthenticationUrl
      ($authorizeUrl,
       $redirectUrl,
       array ("scope" => "id inventory",
              "state" => "something"));
    header ("Location: ".$authUrl);
    die ("Redirect");
}
else
{
    $params = array("code" => $_GET["code"],
                    "redirect_uri" => $redirectUrl);
    $response = $client->getAccessToken
      ($accessTokenUrl,
       "authorization_code",
       $params);

    $accessTokenResult = $response["result"];

    error_log ('accessTokenResult='.$accessTokenResult);

    $client->setAccessToken
      ($accessTokenResult["access_token"]);
    $client->setAccessTokenType
      (OAuth2\Client::ACCESS_TOKEN_BEARER);

    $response = $client->fetch("https://sandbox.gloebit.com/id");
    echo('<strong>Response for fetch id:</strong><pre>');
    print_r($response);
    echo('</pre>');
}
?>
