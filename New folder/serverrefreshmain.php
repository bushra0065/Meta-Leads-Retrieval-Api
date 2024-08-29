<?php
session_start();

// Replace these placeholders with actual values
$shortLivedAccessToken = 'EAAESGJKm6k0BO5PHF4hnZBZCk0Wn6kg9sKYYXXMzg2X1lyKo38ZBWpyfeaP69ZCSUg7Rpu7erje6ZBlK5j1xJIAzZC4QcMmEaZBijZAAmmRFJ1dFxRTwb5HZBbfPZApNkWdwnztcdteX6NwSkFmtPmvexqzq31D7whSqmVZA3tWQQ23P761wW0oiB2Fqot92VQa9Xn2mUbfZCfWtlHqglJnN4ZCUYef1qfM1F';
$appId = '301371725638221';
$appSecret = '611af302d63f56265e4fe8b1fb52acc5';

// Your API request URL to exchange short-lived token for long-lived token
$apiUrl = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=$appId&client_secret=$appSecret&fb_exchange_token=$shortLivedAccessToken";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    // Log the error to a file
    file_put_contents('cron_error.log', 'Error accessing the Graph API: ' . curl_error($ch) . PHP_EOL, FILE_APPEND);
    curl_close($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Parse the response to get the extended access token
$data = json_decode($response, true);

// Check if the access_token is present in the response
if (isset($data['access_token'])) {
    // Store the long-lived access token in a session variable
    $_SESSION['long_lived_access_token'] = $data['access_token'];

    // Log success to a file
    file_put_contents('cron_success.log', 'Token refreshed successfully.' . PHP_EOL, FILE_APPEND);
} else {
    // Log error to a file
    file_put_contents('cron_error.log', 'Error: Requires pages_manage_ads or leads_retrieval permission to manage the object' . PHP_EOL, FILE_APPEND);
}
?>
