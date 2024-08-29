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
    $url = "https://graph.facebook.com/v17.0/me?fields=id%2Cname%2Cads%7Bcampaign_id%2Cadset%2Cname%2Cinsights.metric(leadgen.other)%7Bvalues%7D%7D%7D&access_token=$accessToken";

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
    if (isset($data['id'])) {
        echo 'User ID: ' . $data['id'] . '<br>';
    } else {
        echo 'User ID: Not available.<br>';
    }
    if (isset($data['name'])) {
        echo 'User Name: ' . $data['name'] . '<br>';
    } else {
        echo 'User Name: Not available.<br>';
    }
    echo '</div>';

    // Check if ads data is present in the response
    if (isset($data['ads']['data'])) {
        // Print ad accounts, campaigns, ad sets, and names
        echo '<div class="ad-accounts">';
        foreach ($data['ads']['data'] as $ad) {
            // Check if the ad account ID matches the desired one
            if ($ad['account_id'] === '570011121800124') {
                echo '<div class="ad-account">';
                echo 'Ad Account ID: ' . $ad['account_id'] . '<br>';
                echo 'Campaign ID: ' . $ad['campaign_id'] . '<br>';
                echo 'Ad ID: ' . $ad['adset']['id'] . '<br>';
                echo 'Ad Name: ' . $ad['name'] . '<br>';
                // Check if there are leads data available for this ad
                if (isset($ad['insights']['data'][0]['values'])) {
                    $leads = $ad['insights']['data'][0]['values'][0]['value'];
                    echo 'Leads: ' . $leads . '<br>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    } else {
        echo 'No ad account data found.';
    }
} else {
    echo 'Error: Long-lived access token not found. Please acquire it first using gettoken.php.';
}
?>
