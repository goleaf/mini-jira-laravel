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

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $apiResponse = makeApiRequest('login', 'POST', [
            'email' => $email,
            'password' => $password
        ]);
        
        if ($apiResponse && isset($apiResponse['token'])) {
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'token' => $apiResponse['token']
            ];
        } else {
            $response['message'] = 'Login failed';
        }
    } elseif ($action === 'logout') {
        $token = $_POST['token'] ?? '';
        
        if (empty($token)) {
            $response['message'] = 'Token is required';
        } else {
            $apiResponse = makeApiRequest('logout', 'POST', [], $token);
            
            $response = [
                'success' => $apiResponse && isset($apiResponse['message']),
                'message' => $apiResponse['message'] ?? 'Logout failed'
            ];
        }
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
    <title>API Authentication Test</title>
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
        .full-width {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <h1 class="mb-4 text-center">API Authentication Test</h1>
        
        <div class="row full-width g-0">
            <div class="col-12 col-md-4 p-3">
                <h2>Login</h2>
                <form id="loginForm">
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="admin@tasksportal.com">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required value="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
            <div class="col-12 col-md-4 p-3">
                <h2>Logout</h2>
                <button id="logoutBtn" class="btn btn-danger">Logout</button>
            </div>
            <div class="col-12 col-md-4 p-3">
                <h2>Result</h2>
                <div id="result" class="alert" style="display: none;"></div>
            </div>
        </div>
        
        <div class="mt-5 full-width">
            <h2 class="text-center">API Documentation</h2>
            <div class="table-responsive">
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
                            <td>/api/v1/login</td>
                            <td>POST</td>
                            <td>Login and get token</td>
                            <td>
                                <pre>curl -X POST http://your-domain.com/api/v1/login \
-H "Content-Type: application/json" \
-d '{"email":"admin@tasksportal.com","password":"password"}'</pre>
                            </td>
                        </tr>
                        <tr>
                            <td>/api/v1/logout</td>
                            <td>POST</td>
                            <td>Logout and invalidate token</td>
                            <td>
                                <pre>curl -X POST http://your-domain.com/api/v1/logout \
-H "Authorization: Bearer {your_token}"</pre>
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
                    if (data.token) {
                        token = data.token;
                        localStorage.setItem('api_token', token);
                    }
                    showResult(data.message, true);
                } else {
                    showResult(data.message, false);
                }
            }

            $('#loginForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: $(this).serialize() + '&action=login',
                    dataType: 'json',
                    success: handleApiResponse,
                    error: () => showResult('An error occurred', false)
                });
            });

            $('#logoutBtn').click(function() {
                if (!token) {
                    showResult('No active session', false);
                    return;
                }
                $.ajax({
                    url: '',
                    method: 'POST',
                    data: { action: 'logout', token: token },
                    dataType: 'json',
                    success: function(data) {
                        handleApiResponse(data);
                        if (data.success) {
                            localStorage.removeItem('api_token');
                            token = null;
                        } else {
                            showResult('Logout failed: ' + data.message, false);
                        }
                    },
                    error: () => showResult('An error occurred during logout', false)
                });
            });
        });
    </script>
</body>
</html>