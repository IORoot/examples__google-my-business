<?php 


/**
 * 
 * 
 * This is not working - google never tries to connect and get the binary data.
 * 
 * 
 * Uncaught Google\Service\Exception: { "error": { "code": 400, 
 * "message": "Request contains an invalid argument.", 
 * "status": "INVALID_ARGUMENT", 
 * "details": [ { "@type": "type.googleapis.com/google.mybusiness.v4.ValidationError", 
 * "errorDetails": [ { "code": 1000, "message": "Fetching image failed." } ] } ] } } 
 * 
 */


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
// │                               start_upload                              │
// └─────────────────────────────────────────────────────────────────────────┘

// New data_request (opens up a new empty container for uploading)
$data_request = new Google_Service_MyBusiness_StartUploadMediaItemDataRequest();

// Create a new data reference holder - this is where the binary data will be put into.
$binary_dataRef = $my_business_account->accounts_locations_media->startUpload($location_name, $data_request);


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                               media.upload                              │
// │                 There is no library for this endpoint!                  │
// │                            Using CURL instead.                          │
// └─────────────────────────────────────────────────────────────────────────┘

// start new curl connection
$ch = curl_init();

// Enable verbose logging
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

// Set image
$image = 'test_image.jpg';
$curl_image = curl_file_create($image,'image/jpeg','test_name');

// endpoint
curl_setopt($ch, CURLOPT_URL,'https://mybusiness.googleapis.com/upload/v1/media/'.$binary_dataRef->resourceName.'?uploadType=media');

// set the post file contents
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file_contents'=> $curl_image]);

// headers
// Expect: is set to NOT wait for a 100-continue
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-length:'   .filesize($image),
    'Authorization:'    .'Bearer ' . $token['access_token'],
    'X-GOOG-API-FORMAT-VERSION:'. '2 ' 
]);

// POST, not GET
curl_setopt($ch, CURLOPT_POST, 1);

// Set HTTP1.1 version to stop the "stream 0 was not closed properly" error
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

// return
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// set 10 sec timeout.
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

// get the returned result.
$message = curl_exec($ch);


// output the verbose log of curl
rewind($verbose);
$verboseLog = stream_get_contents($verbose);
echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

// close connection.
curl_close ($ch);



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
$mediaItem->setName('Test Image');

// Media Type: [MEDIA_FORMAT_UNSPECIFIED|PHOTO|VIDEO]
$mediaItem->setMediaFormat('PHOTO');

// Attach locationAssociation to mediaItem.
$mediaItem->setLocationAssociation($locationAssociation);

// Test Description.
$mediaItem->setDescription("Test Image");

// Public DATE REF.
$mediaItem->setDataRef($binary_dataRef);


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                             Create on GMB                               │
// └─────────────────────────────────────────────────────────────────────────┘

$new_image = $my_business_account->accounts_locations_media->create($location_name, $mediaItem);

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                            OUTPUT RESULT                                │
// └─────────────────────────────────────────────────────────────────────────┘

echo '<pre>';
echo print_r($new_image, true);
echo '</pre>';