<?php

    $baseUrl = 'https://tasks.prus.dev/api/v1/';
    $apiToken = 'bb3bb6e0af521c10da9c0a7f36b846d3'; // API token

    // Fetch data for task-statuses
//    fetchApiData($baseUrl . 'task-statuses', $apiToken);

    // Fetch data for tasks
//    fetchApiData($baseUrl . 'tasks', $apiToken);

    // Fetch data for task-statuses again
    fetchApiData($baseUrl . 'task-types', $apiToken);

    function fetchApiData($baseUrl, $apiToken) {
        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '?api_token=' . $apiToken); // Include api_token in URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request and store the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            $errorMessage = curl_error($ch);
            echo "cURL Error: $errorMessage";
        } else {
            // Decode and display the response
            echo($response);
        }

        // Close cURL session
        curl_close($ch);
    }


?>
