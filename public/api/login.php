<?php

// User credentials
    $email = "admin@tasksportal.com";
    $password = "password";

// API endpoint for authentication
    $apiUrl = "https://tasks.prus.dev/api/v1/login";

// Data to be sent in the request
    $data = [
        'email' => $email,
        'password' => $password
    ];

// Initialize Curl
    $ch = curl_init();

// Set Curl options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute Curl request
    $response = curl_exec($ch);

// Check for errors
    if(curl_errno($ch)){
        echo 'Curl error: ' . curl_error($ch);
    }

// Close Curl resource
    curl_close($ch);

// Output the response
    echo $response;
?>
