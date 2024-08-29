
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Include the Facebook PHP SDK
    require_once './php-graph-sdk-5.x/src/Facebook/autoload.php';
    
    include 'getCurlData.php';
    // Include the modified getToken.php to get the long-lived access token
    include 'getToken.php';
    
    // Check if the long-lived access token was successfully obtained
    if (isset($longLivedAccessToken)) {
        // Initialize the Facebook PHP SDK
        $fb = new Facebook\Facebook([
            'app_id' => '301371725638221',
            'app_secret' => '611af302d63f56265e4fe8b1fb52acc5',
            'default_graph_version' => 'v17.0', // Use the appropriate API version
        ]);
    
        try {
            // Make the API request to retrieve the data
            $response = $fb->get('/me?fields=id,name,adaccounts{id,name,ads{leads{form_id}}}', $longLivedAccessToken);
            $userData = $response->getDecodedBody();
    
            // Process and display the data
            echo "User ID: " . $userData['id'] . PHP_EOL;
            echo "User Name: " . $userData['name'] . PHP_EOL;
    
            // Rest of the code to process leads data as before...
        } catch (Facebook\Exception\FacebookResponseException $e) {
            // Handle Facebook API errors
            echo 'Graph returned an error: ' . $e->getMessage();
        } catch (Facebook\Exception\FacebookSDKException $e) {
            // Handle SDK errors
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    } else {
        echo 'Error: Unable to obtain a long-lived access token.';
    }
    ?>
    