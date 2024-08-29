<?php
session_start();

// Validate CSRF token
if (!isset($_SESSION['csrf_token']) || empty($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
    die('Invalid CSRF token');
}

// Sanitize and validate the input
$appId = filter_var($_POST['app_id'], FILTER_SANITIZE_STRING);
$appSecret = filter_var($_POST['app_secret'], FILTER_SANITIZE_STRING);

// Validate App ID and App Secret format using regular expressions
if (!preg_match('/^[a-zA-Z0-9_]+$/', $appId) || !preg_match('/^[a-fA-F0-9]+$/', $appSecret)) {
    die('Invalid App ID or App Secret format');
}

// Replace these placeholders with actual values
$shortLivedAccessToken = $_POST['short_lived_access_token'];

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
    echo 'Error accessing the Graph API: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Parse the JSON response to get the extended access token
$data = json_decode($response, true);

// Check if the access_token is present in the response
if (isset($data['access_token']) && is_string($data['access_token']) && strlen($data['access_token']) > 0) {
    // Store the long-lived access token in a session variable
    $_SESSION['long_lived_access_token'] = $data['access_token'];
    $longLivedAccessToken = $data['access_token'];

    // Update the long-lived access token in the database (replace 'your_table_name' with the actual table name and 'your_column_name' with the column where the access token is stored)
    $query = "UPDATE access_tokens SET access_token = ? WHERE id = 1"; // Assuming you store the token in the first row with the ID 1

    // Use prepared statement to update the access token in the database
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $longLivedAccessToken);

    if ($stmt->execute()) {
        // Debug: Print the updated access token
        echo 'Token refreshed successfully and saved to the database. New Access Token: ' . $longLivedAccessToken;
    } else {
        echo 'Error updating record: ' . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo 'Error: Requires pages_manage_ads or leads_retrieval permission to manage the object';
}

// Close the database connection
$conn->close();
?>
