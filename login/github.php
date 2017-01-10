<?php
require __DIR__ . '/init.php';

$provider = new League\OAuth2\Client\Provider\Github([
    'clientId' => github['ci'],
    'clientSecret' => github['cs'],
    'redirectUri' => (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER['SCRIPT_NAME']
]);

if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    // GitHub oauth url redirect.
    $authUrl = $provider->getAuthorizationUrl();

    // CSRF
    $_SESSION['oauth2state'] = $provider->getState();

    header('Location: ' . $authUrl);
    exit;
}

// get content
$user = $provider->getResourceOwner($provider->getAccessToken('authorization_code', ['code' => filter_input(INPUT_GET, 'code')]));

echo $mysql['id'];

register($user->getName(), $user->getNickname(), $user->getId(), 'github');

echo '<pre>';
var_dump($user);
echo '</pre>';
