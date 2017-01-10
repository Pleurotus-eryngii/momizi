<?php
require __DIR__ . '/init.php';

// Make an alias "Client" instead of "mpyw\Cowitter\Client"
use mpyw\Cowitter\Client;

try {
    if (!isset($_SESSION['state'])) {
        /* User is completely unlogined */

        // Create a client object
        $_SESSION['client'] = new Client([
            twitter['ck'],
            twitter['cs']
        ]);

        // Update it with request_token (oauth_callback is http://127.0.0.1:8080/login.php)
        $_SESSION['client'] = $_SESSION['client']->oauthForRequestToken((empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER['SCRIPT_NAME']);

        // Change state
        $_SESSION['state'] = 'pending';

        // Redirect to Twitter
        header("Location: {$_SESSION['client']->getAuthorizeUrl()}");
        exit;
    } else {
        /* User is unlogined, but pending access_token */

        // Update it with access_token (Using $_GET['oauth_verifier'] returned from Twitter)
        $_SESSION['client'] = $_SESSION['client']->oauthForAccessToken(filter_input(INPUT_GET, 'oauth_verifier'));

        // Change state
        $_SESSION['state'] = 'logined';

        try {
            $user = $_SESSION['client']->get('account/verify_credentials');

            register($user->name, $user->screen_name, $user->id_str, 'twitter');

            echo '<pre>';
            var_dump($user);
            echo '</pre>';
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
        }
    }
} catch (\RuntimeException $e) {
    // Destroy session
    session_destroy();

    // "500 Internal Server Error"
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
}
