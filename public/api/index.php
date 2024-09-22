<?php
// Ensure this file is being accessed through the web server
if (php_sapi_name() !== 'cli-server' && php_sapi_name() !== 'fpm-fcgi') {
    die('This script can only be run through a web server.');
}

// Get the list of PHP files in the current directory
$files = glob('*.php');

// Remove the current file from the list
$current_file = basename(__FILE__);
$files = array_diff($files, [$current_file]);

// Define descriptions for each file
$file_descriptions = [
    'auth.php' => 'Authentication API for login and logout',
    'comments.php' => 'API for managing task comments',
    'tasks.php' => 'API for managing tasks'
];

// Define color scheme
$color_scheme = [
    'auth.php' => '#007bff',
    'comments.php' => '#28a745',
    'tasks.php' => '#ffc107'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Files Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .file-item {
            border-left: 5px solid;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">API Files Index</h1>
        <ul class="list-group">
            <?php foreach ($files as $file): ?>
                <li class="list-group-item file-item" style="border-left-color: <?= $color_scheme[$file] ?? '#6c757d' ?>;">
                    <a href="<?= htmlspecialchars($file) ?>" style="color: <?= $color_scheme[$file] ?? '#6c757d' ?>;">
                        <?= htmlspecialchars($file) ?>
                    </a>
                    <?php if (isset($file_descriptions[$file])): ?>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($file_descriptions[$file]) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>