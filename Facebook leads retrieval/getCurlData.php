<style>
    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ccc;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
</style>
<?php
session_start();

include "db/config.php";

$query = "SELECT access_token FROM access_tokens WHERE id = 1"; 
$result = mysqli_query($connection, $query);

// Check if the query was successful
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the long-lived access token from the database
    $data = mysqli_fetch_assoc($result);
    $longLivedAccessToken = $data['access_token'];

    // Define the Campaign ID
    $campaign_id = '23853736441480551';

    // Initialize an array to store all lead data
    $all_lead_data = [];

    // Initialize a counter for sets
    $set_counter = 0;

    // Initialize a variable to track the current page
    $current_page = 1;

    // Define the limit of sets per page
    $sets_per_page = 4;

    // Construct the API URL for leads data
    $url = "https://graph.facebook.com/v17.0/$campaign_id/leads?access_token=$longLivedAccessToken";

    do {
        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

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

        // If there's lead data, add it to the all_lead_data array
        if (isset($data['data'])) {
            $all_lead_data = array_merge($all_lead_data, $data['data']);
            $set_counter++;

            // Check if the current page limit has been reached
            if ($set_counter >= $sets_per_page) {
                // Display the leads and reset the counter
                displayLeads($all_lead_data, $current_page);
                $all_lead_data = [];
                $set_counter = 0;
                $current_page++;
            }
        }

        // If there's a next page, update the URL for pagination
        if (isset($data['paging']['next'])) {
            $url = $data['paging']['next'];
        } else {
            // Display any remaining leads and exit the loop
            if (!empty($all_lead_data)) {
                displayLeads($all_lead_data, $current_page);
            }
            break;
        }
    } while (true);

    // Close the database connection
    mysqli_close($connection);
} else {
    echo 'Error: Long-lived access token not found in the database.';
}

// Function to display leads in a table with alternating colors
function displayLeads($leads, $page) {
    echo '<table>';
    echo '<tr>';
    echo '<th colspan="2">Page ' . $page . '</th>';
    echo '</tr>';
    
    foreach ($leads as $lead) {
        echo '<tr>';
        foreach ($lead['field_data'] as $field) {
            $field_name = $field['name'];
            $field_value = $field['values'][0];
            echo '<td>' . htmlspecialchars($field_name) . '</td>';
            echo '<td>' . htmlspecialchars($field_value) . '</td>';
            echo '</tr>';
        }
    }

    echo '</table>';
    echo '<hr>';
}
?>


