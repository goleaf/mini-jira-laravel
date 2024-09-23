<?php
// Ensure this file is being accessed through the web server
if (php_sapi_name() !== 'cli-server' && php_sapi_name() !== 'fpm-fcgi') {
    die('This script can only be run through a web server.');
}

// Function to make API requests
function makeApiRequest($endpoint, $method, $data = [], $token = null) {
    $url = "https://mini-jira.prus.dev/api/v1/" . $endpoint;
    $headers = ["Content-type: application/json\r\n"];
    if ($token) {
        $headers[] = "Authorization: Bearer $token\r\n";
    }
    $options = [
        'http' => [
            'header'  => implode('', $headers),
            'method'  => $method,
            'content' => json_encode($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result ? json_decode($result, true) : null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];
    $token = $_POST['token'] ?? '';

    if ($action === 'listComments') {
        $taskId = $_POST['taskId'] ?? '';
        $apiResponse = makeApiRequest("tasks/{$taskId}/comments", 'GET', [], $token);
        $response = [
            'success' => $apiResponse && isset($apiResponse['comments']),
            'data' => $apiResponse['comments'] ?? [],
            'message' => $apiResponse ? 'Comments retrieved successfully' : 'Failed to retrieve comments'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } elseif ($action === 'createComment') {
        $taskId = $_POST['taskId'] ?? '';
        $body = $_POST['body'] ?? '';
        $parentId = $_POST['parentId'] ?? null;
        
        $apiResponse = makeApiRequest("tasks/{$taskId}/comments", 'POST', [
            'body' => $body,
            'parent_id' => $parentId
        ], $token);
        
        $response = [
            'success' => $apiResponse && isset($apiResponse['comment']),
            'data' => $apiResponse['comment'] ?? null,
            'message' => $apiResponse ? 'Comment created successfully' : 'Failed to create comment'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .api-table {
            background-color: #f8f9fa;
        }
        .api-table th {
            background-color: #007bff;
            color: white;
        }
        .api-table td {
            background-color: #e9ecef;
        }
        .api-table tr:nth-child(even) td {
            background-color: #f1f3f5;
        }
        .endpoint {
            color: #0066cc;
        }
        .method {
            font-weight: bold;
        }
        .method-get {
            color: #28a745;
        }
        .method-post {
            color: #ffc107;
        }
        pre {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .full-width-column {
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <h1 class="mb-4">Comments API Test</h1>
        
        <div class="row">
            <div class="col-12 full-width-column">
                <div class="card">
                    <div class="card-header">List Comments</div>
                    <div class="card-body">
                        <form id="listCommentsForm">
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" id="taskId" name="taskId" placeholder="Task ID" value="1" required>
                                <input type="hidden" name="action" value="listComments">
                                <button type="submit" class="btn btn-primary">List Comments</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 full-width-column">
                <div class="card">
                    <div class="card-header">Create Comment</div>
                    <div class="card-body">
                        <form id="createCommentForm">
                            <div class="mb-3">
                                <label for="taskIdForComment" class="form-label">Task ID</label>
                                <input type="number" class="form-control" id="taskIdForComment" name="taskId" required value="1">
                            </div>
                            <div class="mb-3">
                                <label for="body" class="form-label">Comment Body</label>
                                <textarea class="form-control" id="body" name="body" required>This is a test comment</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="parentId" class="form-label">Parent Comment ID (optional)</label>
                                <input type="number" class="form-control" id="parentId" name="parentId">
                            </div>
                            <input type="hidden" name="action" value="createComment">
                            <button type="submit" class="btn btn-primary">Create Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 full-width-column">
                <div id="result" class="mt-4 alert" style="display: none;"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 full-width-column">
                <div id="commentsOutput" class="mt-4">
                    <h3>Comments Output</h3>
                    <pre id="commentsJson" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;"></pre>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 full-width-column">
                <h2>API Documentation</h2>
                <table class="table table-bordered api-table">
                    <thead>
                        <tr>
                            <th>Endpoint</th>
                            <th>Method</th>
                            <th>Description</th>
                            <th>cURL Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>/api/v1/tasks/{task}/comments</td>
                            <td>GET</td>
                            <td>List comments for a task</td>
                            <td>
                                <pre>curl -X GET http://your-domain.com/api/v1/tasks/1/comments \
-H "Authorization: Bearer {your_token}"</pre>
                            </td>
                        </tr>
                        <tr>
                            <td>/api/v1/tasks/{task}/comments</td>
                            <td>POST</td>
                            <td>Create a new comment</td>
                            <td>
                                <pre>curl -X POST http://your-domain.com/api/v1/tasks/1/comments \
-H "Authorization: Bearer {your_token}" \
-H "Content-Type: application/json" \
-d '{"body":"This is a comment","parent_id":null}'</pre>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let token = localStorage.getItem('api_token');

            function showResult(message, isSuccess) {
                $('#result').removeClass('alert-success alert-danger')
                    .addClass(isSuccess ? 'alert-success' : 'alert-danger')
                    .text(message).show();
            }

            function handleApiResponse(data) {
                if (data.success) {
                    showResult(data.message, true);
                    if (data.data) {
                        $('#commentsJson').text(JSON.stringify(data.data, null, 2));
                        $('#commentsOutput').show();
                    }
                } else {
                    showResult(data.message, false);
                }
            }

            $('#listCommentsForm, #createCommentForm').submit(function(e) {
                e.preventDefault();
                if (!token) {
                    showResult('No active session. Please login first.', false);
                    return;
                }
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: $(this).serialize() + '&token=' + token,
                    dataType: 'json',
                    success: handleApiResponse,
                    error: () => showResult('An error occurred', false)
                });
            });
        });
    </script>
</body>
</html>