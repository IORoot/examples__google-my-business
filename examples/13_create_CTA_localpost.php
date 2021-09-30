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
// │                           Get Latest Photo                              │
// └─────────────────────────────────────────────────────────────────────────┘

// List all media for location
$list_media = $my_business_account->accounts_locations_media->listAccountsLocationsMedia($location_name);

// Get Media ID (item 0) - PHOTO
$media_name = $list_media->mediaItems[0]->name;

// Get single Media Post Object
$media_item = $my_business_account->accounts_locations_media->get($media_name);

// Get the GOOGLE_URL of the media item.
$media_sourceUrl = $media_item->getGoogleUrl();

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                           NEW Media OBJECT                              │
// └─────────────────────────────────────────────────────────────────────────┘

// Create a new mediaItem to use on out localpost
$new_mediaItem = new Google_Service_MyBusiness_MediaItem();

// Set the source URL to the pre-existing google_url of an already uploaded video / photo
$new_mediaItem->setSourceUrl($media_sourceUrl);

// Set the media_format
$new_mediaItem->setMediaFormat('PHOTO');


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            CTA RESOURCE                                 │
// └─────────────────────────────────────────────────────────────────────────┘

// create a new call-to-action object
$cta = new Google_Service_MyBusiness_CallToAction();

// Type of Action
// - ACTION_TYPE_UNSPECIFIED
// - BOOK
// - ORDER
// - SHOP
// - LEARN_MORE
// - SIGN_UP
// - CALL
$cta->setActionType('LEARN_MORE');

// set the url to link to.
$cta->setUrl('http://londonparkour.com');


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                           localpost OBJECT                              │
// └─────────────────────────────────────────────────────────────────────────┘

// New LocalPost object.
$local_post = new Google_Service_MyBusiness_LocalPost();

// Set Post language.
$local_post->setLanguageCode('en-GB');

// Set body of post.
$local_post->setSummary("This is the main content of the post we will be creating.");

// Required (https://developers.google.com/my-business/reference/rest/v4/accounts.locations.localPosts#LocalPostTopicType)
// standard, event, offer, or alert
$local_post->setTopicType('STANDARD');

// Set the media item we want to associate to the post.
$local_post->setMedia([$new_mediaItem]);

// Set the call to action
$local_post->setCallToAction($cta);

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                         localpost RESOURCE                              │
// └─────────────────────────────────────────────────────────────────────────┘

// Create the new post
$post = $my_business_account->accounts_locations_localPosts->create($location_name, $local_post);

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            OUTPUT RESULT                                │
// └─────────────────────────────────────────────────────────────────────────┘

echo '<pre>';
echo print_r($post, true);
echo '</pre>';