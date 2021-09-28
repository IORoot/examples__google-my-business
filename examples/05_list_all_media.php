<?php 

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                   Place this file into root directory                   │
// └─────────────────────────────────────────────────────────────────────────┘
include_once __DIR__ . '/vendor/autoload.php';

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            OAUTH HANDSHAKE                              │
// └─────────────────────────────────────────────────────────────────────────┘

$credentials = __DIR__ . '/client_secret.json';

// switch on errors
$client = new Google\Client(['api_format_v2' => true]);
$client->setAuthConfig($credentials);
$client->addScope("https://www.googleapis.com/auth/business.manage");
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client->setRedirectUri($redirect_uri);
$my_business_account = new Google_Service_MyBusiness($client);


if (isset($_GET['logout'])) { // logout: destroy token
    unset($_SESSION['token']);
  die('Logged out.');
}

if (isset($_GET['code'])) { // get auth code, get the token and store it in session
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
}

if (isset($_SESSION['token'])) { // get token and configure client
    $token = $_SESSION['token'];
    $client->setAccessToken($token);
}

if (!$client->getAccessToken()) { // auth call 
    $authUrl = $client->createAuthUrl();
    header("Location: ".$authUrl);
    die;
}


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            GMB API Calls                                │
// └─────────────────────────────────────────────────────────────────────────┘

// Get Account
$list_accounts  = $my_business_account->accounts->listAccounts();

// Account Name for first account
$account_name = $list_accounts->accounts[0]->name;

// Get Locations for account.
$list_account_locations = $my_business_account->accounts_locations->listAccountsLocations($account_name);

// Location Name for first location
$location_name = $list_account_locations->locations[0]->name;

// List all media for location
$list_media = $my_business_account->accounts_locations_media->listAccountsLocationsMedia($location_name);

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            OUTPUT RESULT                                │
// └─────────────────────────────────────────────────────────────────────────┘

echo '<pre>';
echo print_r($list_media, true);
echo '</pre>';