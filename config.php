<?php
$current_file = basename($_SERVER['SCRIPT_NAME']);
$base_url = '';

if (strpos($_SERVER['SCRIPT_NAME'], 'forms/') !== false) {
    $base_url = '../../';
} elseif ($current_file === 'AdminIndex.php') {
    $base_url = '../';
} elseif ($current_file === 'index.php') {
    $base_url = '../';
} else {
    echo "Hvor er du?";
}

define('BASE_URL', rtrim($base_url, '/'));
?>
