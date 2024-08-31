<?php
session_start();

include "db/config.php";

$query = "SELECT access_token FROM access_tokens WHERE id = 1"; 
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the short-lived access token from the database
    $data = mysqli_fetch_assoc($result);
    $shortLivedAccessToken = $data['access_token'];

    // Your API request URL to exchange short-lived token for long-lived token
    $appId = 'your app id';
    $appSecret = 'yourappsecretcode';
    $apiUrl = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=$appId&client_secret=$appSecret&fb_exchange_token=$shortLivedAccessToken";

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

        // Update the long-lived access token in the database (replace 'your_table_name' with the actual table name and 'your_column_name' with the column where the access token is stored)
        $query = "UPDATE access_tokens SET access_token = '$longLivedAccessToken' WHERE id = 1"; // Assuming you store the token in the first row with the ID 1
        $result = mysqli_query($connection, $query);
        if (!$result) {
            echo 'Error updating access token in the database: ' . mysqli_error($connection);
            exit;
        }

        // Debug: Print the updated access token
        echo 'Token refreshed successfully. New Access Token: ' . $longLivedAccessToken;
    } else {
        echo 'Error: Requires pages_manage_ads or leads_retrieval permission to manage the object';
    }
} else {
    echo 'Error: Short-lived access token not found in the database.';
}

// Close the database connection
mysqli_close($connection);
?>
