<?php

    $task_id = 99;

    $data = [
        'body' => 'new comment body',
        'parent_id' => 12,
    ];

    $apiToken = 'bb3bb6e0af521c10da9c0a7f36b846d3';

    $baseUrl = 'https://tasks.prus.dev/api/v1/tasks/' . $task_id . '/comments';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $baseUrl . '?api_token=' . $apiToken);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        $errorMessage = curl_error($ch);
        echo "cURL Error: $errorMessage";
    } else {
        echo $response;
    }

    curl_close($ch);

?>
