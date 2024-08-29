<?php
// Include the Facebook PHP SDK
require_once './php-graph-sdk-5.x/src/Facebook/autoload.php';
// Include the file with the token retrieval code
include 'getToken.php';

// Check if the access token was successfully obtained
if (isset($data['access_token'])) {
    $accessToken = $data['access_token'];

    // Initialize the Facebook PHP SDK
    $fb = new Facebook\Facebook([
        'app_id' => '301371725638221',
        'app_secret' => '611af302d63f56265e4fe8b1fb52acc5',
        'default_graph_version' => 'v17.0', // Use the appropriate API version
    ]);

    try {
        // Make the API request to retrieve the user's ID, name, and ad accounts
        $response = $fb->get('/me?fields=id,name,adaccounts{id}', $accessToken);
        $userData = $response->getDecodedBody();

        // Process and display the user's data
        echo "User ID: " . $userData['id'] . PHP_EOL;
        echo "User Name: " . $userData['name'] . PHP_EOL;

        // Process and display the ad accounts data
        if (isset($userData['adaccounts'])) {
            foreach ($userData['adaccounts']['data'] as $adAccount) {
                echo "Ad Account ID: " . $adAccount['id'] . PHP_EOL;
                // You can access other properties of the ad account here if needed
            }
        } else {
            echo "No ad accounts found for the user.";
        }
    } catch (Facebook\Exception\FacebookResponseException $e) {
        // Handle Facebook API errors
        echo 'Graph returned an error: ' . $e->getMessage();
    } catch (Facebook\Exception\FacebookSDKException $e) {
        // Handle SDK errors
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
    }
} else {
    echo 'Error: Unable to obtain a long-term access token.';
}
?>
