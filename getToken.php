<?php
session_start();

include "db/config.php";

// Fetch the long-lived access token from the database (replace 'your_table_name' with the actual table name)
$query = "SELECT access_token FROM access_tokens WHERE id = 1 LIMIT 1"; // Assuming you store the token in the first row
$result = mysqli_query($connection, $query);

// Check if the query was successful and if the access token exists
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the long-lived access token from the database
    $data = mysqli_fetch_assoc($result);
    $accessToken = $data['access_token'];

    // Store the token in the session
    $_SESSION['long_lived_access_token'] = $accessToken;

    // Close the database connection
    mysqli_close($connection);
} else {
    // Close the database connection
    mysqli_close($connection);

    // If the token is not found in the database, you can handle it based on your use case
    echo 'Error: Long-lived access token not found in the database.';
    exit;
}

// Replace these placeholders with actual values
$appId = '301371725638221';
$appSecret = '611af302d63f56265e4fe8b1fb52acc5';

// Your API request URL to exchange short-lived token for long-lived token
$apiUrl = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=$appId&client_secret=$appSecret&fb_exchange_token=$accessToken";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error accessing the Graph API: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Parse the JSON response to get the extended access token
$data = json_decode($response, true);

// Check if the access_token is present in the response
if (isset($data['access_token'])) {
    // Store the long-lived access token in a session variable
    $_SESSION['long_lived_access_token'] = $data['access_token'];
    $longLivedAccessToken = $data['access_token'];
    echo 'Token refreshed successfully. New Access Token: ' . $longLivedAccessToken;
} else {
    echo 'Error: Requires pages_manage_ads or leads_retrieval permission to manage the object';
}
?>
