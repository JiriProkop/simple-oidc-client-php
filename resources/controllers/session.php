
<?php

include __DIR__ . '/../../vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;

include __DIR__ . '/../../config.php';
include __DIR__ . '/../../src/MitreIdConnectUtils.php';

if (!isset($_SESSION)) {
    session_set_cookie_params(0, '/' . $sessionName);
    session_name($sessionName);
    @session_start();
}

if (empty($clientSecret)) {
    $clientSecret = null;
}

$oidc = new OpenIDConnectClient(
    $issuer,
    $clientId,
    $clientSecret
);
$scopes = array_keys($scopesDefine);
$oidc->addScope($scopes);
$oidc->setRedirectURL($redirectUrl);
$oidc->setResponseTypes(['code']);
if (!empty($pkceCodeChallengeMethod)) {
    $oidc->setCodeChallengeMethod($pkceCodeChallengeMethod);
}

if (isset($_SESSION['sub']) && time() - $_SESSION['CREATED'] < $sessionLifetime) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create-refresh-token':
                $_SESSION['action'] = 'create-refresh-token';
                $scopes[] = "offline_access";
                $oidc->addScope($scopes);
                $oidc->addAuthParam(['action' => 'create-refresh-token']);
                $oidc->authenticate();
                break;
            case 'revoke':
                $oidc->revokeToken($_POST['token'], '', $clientId, $clientSecret);
                $_SESSION['action'] = 'revoke';
                if ($_POST['token'] == $_SESSION['refresh_token']) {
                    $_SESSION['refresh_token'] = null;
                }
                break;
            default:
                break;
        }
    }
    if (isset($_SESSION['action']) && $_SESSION['action'] == 'create-refresh-token') {
        $oidc->authenticate();
        $refreshToken = $oidc->getRefreshToken();
        $sub = $oidc->requestUserInfo('sub');
        if ($sub) {
            $accessToken = $_SESSION['access_token'];
            $_SESSION['refresh_token'] = $refreshToken;
        }
        unset($_SESSION['action']);
    } else {
        $accessToken = $_SESSION['access_token'];
        $refreshToken = $_SESSION['refresh_token'];
        unset($_SESSION['action']);
    }
} else {
    $oidc->authenticate();
    $accessToken = $oidc->getAccessToken();
    $refreshToken = $oidc->getRefreshToken();
    $sub = $oidc->requestUserInfo('sub');
    if ($sub) {
        $_SESSION['sub'] = $sub;
        $_SESSION['access_token'] = $accessToken;
        $_SESSION['refresh_token'] = $refreshToken;
        $_SESSION['CREATED'] = time();
    }
}

$openidConfiguration = getMetadata($issuer);
$tokenEndpoint = $openidConfiguration->{'token_endpoint'};
$userInfoEndpoint = $openidConfiguration->{'userinfo_endpoint'};
$introspectionEndpoint = $openidConfiguration->{'introspection_endpoint'};
