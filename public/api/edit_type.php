<?php


// Base URL of your API
    $baseUrl = 'https://tasks.prus.dev/api/v1/task-types';

// API token
    $apiToken = 'bb3bb6e0af521c10da9c0a7f36b846d3';

// ID of the status to edit
    $statusId = 1; // Change this to the ID of the status you want to edit

// New status data
    $newStatusData = [
        'name' => 'New Type Name ' . rand(0,999) // Change this to the new name
    ];

// Initialize cURL session
    $ch = curl_init();

// Set cURL options
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/' . $statusId . '?api_token=' . $apiToken);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Use PUT method for updating
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($newStatusData));
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


