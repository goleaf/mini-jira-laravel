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
            'content' => json_encode($data),
            'ignore_errors' => true
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === false) {
        $error = error_get_last();
        return ['error' => $error['message']];
    }
    
    $responseHeaders = $http_response_header ?? [];
    $statusLine = $responseHeaders[0] ?? '';
    preg_match('{HTTP/\S*\s(\d{3})}', $statusLine, $match);
    $statusCode = $match[1] ?? 500;
    
    if ($statusCode >= 300) {
        return [
            'error' => "HTTP Error: $statusLine",
            'response' => $result,
            'status_code' => $statusCode
        ];
    }
    
    return json_decode($result, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];
    $token = $_POST['token'] ?? '';

    if (!$token) {
        $response['message'] = 'No active session. Please login first.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    switch ($action) {
        case 'createTask':
            $taskData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'status_id' => $_POST['status_id'] ?? '',
                'type_id' => $_POST['type_id'] ?? '',
                'due_date' => $_POST['due_date'] ?? '',
            ];
            
            $apiResponse = makeApiRequest('tasks', 'POST', $taskData, $token);
            
            if (isset($apiResponse['error'])) {
                $response = [
                    'success' => false,
                    'message' => 'Failed to create task: ' . $apiResponse['error'],
                    'details' => $apiResponse['response'] ?? ''
                ];
            } else {
                $response = [
                    'success' => isset($apiResponse['task']),
                    'data' => $apiResponse['task'] ?? null,
                    'message' => isset($apiResponse['task']) ? 'Task created successfully' : 'Failed to create task'
                ];
            }
            break;

        case 'listTasksWithComments':
            $apiResponse = makeApiRequest('tasks', 'GET', [], $token);
            
            if (isset($apiResponse['error'])) {
                $response = [
                    'success' => false,
                    'message' => 'Failed to retrieve tasks: ' . $apiResponse['error'],
                    'details' => [
                        'status_code' => $apiResponse['status_code'] ?? 'Unknown',
                        'response_body' => $apiResponse['response'] ?? 'No response body'
                    ]
                ];
            } elseif (isset($apiResponse['tasks'])) {
                $tasksWithComments = array_map(function($task) use ($token) {
                    $commentsResponse = makeApiRequest("tasks/{$task['id']}/comments", 'GET', [], $token);
                    $task['comments'] = isset($commentsResponse['error']) ? ['error' => $commentsResponse['error']] : ($commentsResponse['comments'] ?? []);
                    return $task;
                }, $apiResponse['tasks']);

                $response = [
                    'success' => true,
                    'data' => $tasksWithComments,
                    'message' => 'Tasks and comments retrieved successfully'
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => [],
                    'message' => 'Failed to retrieve tasks and comments',
                    'details' => [
                        'api_response' => $apiResponse
                    ]
                ];
            }
            break;

        case 'getTaskById':
            $taskId = $_POST['taskId'] ?? '';
            if (!$taskId) {
                $response = [
                    'success' => false,
                    'message' => 'Task ID is required'
                ];
            } else {
                $apiResponse = makeApiRequest("tasks/{$taskId}", 'GET', [], $token);
                if (isset($apiResponse['error'])) {
                    $response = [
                        'success' => false,
                        'message' => 'Failed to retrieve task: ' . $apiResponse['error'],
                        'details' => [
                            'status_code' => $apiResponse['status_code'] ?? 'Unknown',
                            'response_body' => $apiResponse['response'] ?? 'No response body'
                        ]
                    ];
                } else {
                    $task = $apiResponse['task'] ?? null;
                    if ($task) {
                        $commentsResponse = makeApiRequest("tasks/{$taskId}/comments", 'GET', [], $token);
                        $task['comments'] = $commentsResponse['comments'] ?? [];
                        $response = [
                            'success' => true,
                            'data' => $task,
                            'message' => 'Task and comments retrieved successfully'
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Task not found'
                        ];
                    }
                }
            }
            break;

        default:
            $response = [
                'success' => false,
                'message' => 'Invalid action'
            ];
            break;
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
    <title>Tasks and Comments API Test</title>
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
        .card {
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <h1 class="mb-4">Tasks and Comments API Test</h1>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">List Tasks with Comments</div>
                    <div class="card-body d-flex flex-column">
                        <form id="listTasksForm" class="mb-auto">
                            <input type="hidden" name="action" value="listTasksWithComments">
                            <button type="submit" class="btn btn-primary">List Tasks with Comments</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">Get Task by ID</div>
                    <div class="card-body d-flex flex-column">
                        <form id="getTaskByIdForm" class="mb-auto">
                            <div class="mb-3">
                                <label for="taskId" class="form-label">Task ID</label>
                                <input type="number" class="form-control" id="taskId" name="taskId" required>
                            </div>
                            <input type="hidden" name="action" value="getTaskById">
                            <button type="submit" class="btn btn-primary">Get Task</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">Create Task</div>
                    <div class="card-body d-flex flex-column">
                        <form id="createTaskForm" class="mb-auto">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required value="Test Task">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" required>This is a test task description.</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status_id" class="form-label">Status ID</label>
                                <input type="number" class="form-control" id="status_id" name="status_id" required value="1">
                            </div>
                            <div class="mb-3">
                                <label for="type_id" class="form-label">Type ID</label>
                                <input type="number" class="form-control" id="type_id" name="type_id" required value="1">
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" required value="<?php echo date('Y-m-d', strtotime('+1 week')); ?>">
                            </div>
                            <input type="hidden" name="action" value="createTask">
                            <button type="submit" class="btn btn-primary">Create Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="result" class="mt-4 alert" style="display: none;"></div>

        <div id="tasksOutput" class="mt-4">
            <h3>Tasks and Comments Output</h3>
            <pre id="tasksJson" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;"></pre>
        </div>

        <div class="mt-5">
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
                        <td>/api/v1/tasks</td>
                        <td>GET</td>
                        <td>List all tasks</td>
                        <td>
                            <pre>curl -X GET http://your-domain.com/api/v1/tasks \
-H "Authorization: Bearer {your_token}"</pre>
                        </td>
                    </tr>
                    <tr>
                        <td>/api/v1/tasks/{task_id}</td>
                        <td>GET</td>
                        <td>Get a specific task</td>
                        <td>
                            <pre>curl -X GET http://your-domain.com/api/v1/tasks/1 \
-H "Authorization: Bearer {your_token}"</pre>
                        </td>
                    </tr>
                    <tr>
                        <td>/api/v1/tasks</td>
                        <td>POST</td>
                        <td>Create a new task</td>
                        <td>
                            <pre>curl -X POST http://your-domain.com/api/v1/tasks \
-H "Authorization: Bearer {your_token}" \
-H "Content-Type: application/json" \
-d '{"title":"New Task","description":"Task description","status_id":1,"type_id":1,"due_date":"2023-06-30"}'</pre>
                        </td>
                    </tr>
                    <tr>
                        <td>/api/v1/tasks/{task_id}/comments</td>
                        <td>GET</td>
                        <td>List comments for a task</td>
                        <td>
                            <pre>curl -X GET http://your-domain.com/api/v1/tasks/1/comments \
-H "Authorization: Bearer {your_token}"</pre>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let token = localStorage.getItem('api_token');

            function showResult(message, isSuccess, details) {
                let resultHtml = `<p>${message}</p>`;
                if (details) {
                    resultHtml += '<pre>' + JSON.stringify(details, null, 2) + '</pre>';
                }
                $('#result').removeClass('alert-success alert-danger')
                    .addClass(isSuccess ? 'alert-success' : 'alert-danger')
                    .html(resultHtml).show();
            }

            function handleApiResponse(data) {
                if (data.success) {
                    showResult(data.message, true);
                    if (data.data) {
                        $('#tasksJson').text(JSON.stringify(data.data, null, 2));
                        $('#tasksOutput').show();
                    }
                } else {
                    showResult(data.message, false, data.details);
                }
            }

            $('#listTasksForm, #createTaskForm, #getTaskByIdForm').submit(function(e) {
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
                    error: function(jqXHR, textStatus, errorThrown) {
                        showResult('An error occurred: ' + textStatus + ' - ' + errorThrown, false);
                    }
                });
            });
        });
    </script>
</body>
</html>