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

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            locationAssociation                          │
// └─────────────────────────────────────────────────────────────────────────┘

// A locationAssociation is needed for a MediaItem
$locationAssociation = new Google_Service_MyBusiness_LocationAssociation();

// Set Category(https://developers.google.com/my-business/reference/rest/v4/accounts.locations.media#MediaItem.Category)
$locationAssociation->setCategory('CATEGORY_UNSPECIFIED');

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                                mediaItem                                │
// └─────────────────────────────────────────────────────────────────────────┘

// Create a new MediaItem object
$mediaItem = new Google_Service_MyBusiness_MediaItem();

// Set MediaItem Name: string
$mediaItem->setName('Test Video');

// Media Type: [MEDIA_FORMAT_UNSPECIFIED|PHOTO|VIDEO]
$mediaItem->setMediaFormat('VIDEO');

// Attach locationAssociation to mediaItem.
$mediaItem->setLocationAssociation($locationAssociation);

// Test Description.
$mediaItem->setDescription("Test Video");

// Public URL of image.
$mediaItem->setSourceUrl("https://docs.google.com/uc?export=download&id=112xg44sZBeicWXsVm4ToVGUrEHeLjbYf");

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                             Create on GMB                               │
// └─────────────────────────────────────────────────────────────────────────┘

// Upload image via URL to location
$new_video = $my_business_account->accounts_locations_media->create($location_name, $mediaItem);

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            OUTPUT RESULT                                │
// └─────────────────────────────────────────────────────────────────────────┘

echo '<pre>';
echo print_r($new_video, true);
echo '</pre>';