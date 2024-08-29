


<style>
   .ad {
        border-bottom: 1px solid lightgrey;
        padding: 2rem 1rem;
    }
    .user-info {
        font-weight: bold;
        margin-bottom: 1rem;
    }
    .ad-accounts {
        margin-top: 1rem;
    }
    .ad-account {
        border: 1px solid grey;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .ads {
        margin-top: 1rem;
    }
</style>
<?php
session_start();

// Initialize cURL session
$ch = curl_init();

// Check if the long-lived access token is present in the session
if (isset($_SESSION['long_lived_access_token'])) {
    $accessToken = $_SESSION['long_lived_access_token'];

    // URL with encoded fields
    $url = "https://graph.facebook.com/v17.0/me?fields=id%2Cname%2Cadaccounts%7Baccount_id%2Cads%7Bcampaign_id%2Cadset%2Cname%7D%7D&access_token=$accessToken";

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false); // We don't need the headers in this case

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Error accessing the Graph API: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the decoding was successful
    if (!$data) {
        echo 'Error decoding the API response.';
        exit;
    }

    // Print the user ID and name
    echo '<div class="user-info">';
    echo 'User ID: ' . $data['id'] . '<br>';
    echo 'User Name: ' . $data['name'];
    echo '</div>';

    // Check if ad accounts are present in the response
    if (isset($data['adaccounts']['data'])) {
        // Print ad accounts, campaigns, ad sets, and names
        echo '<div class="ad-accounts">';
        foreach ($data['adaccounts']['data'] as $adAccount) {
            echo '<div class="ad-account">';
            echo 'Ad Account ID: ' . $adAccount['account_id'] . '<br>';
            if (isset($adAccount['ads']['data'])) {
                echo '<div class="ads">';
                foreach ($adAccount['ads']['data'] as $ad) {
                    echo '<div class="ad">';
                    echo 'Campaign ID: ' . $ad['campaign_id'] . '<br>';
                    echo 'Ad ID: ' . $ad['adset']['id'] . '<br>';
                    echo 'Ad Name: ' . $ad['name'];
                    echo '</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo 'No ad account data found.';
    }
} else {
    echo 'Error: Long-lived access token not found. Please acquire it first using gettoken.php.';
}
?>
