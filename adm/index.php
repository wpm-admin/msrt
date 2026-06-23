<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Version
define('VERSION', '3.0.3.8');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}


// ===== SQL INJECTION BASIC FIREWALL =====

// Что проверяем
$check_data = [
    $_SERVER['REQUEST_URI'] ?? '',
    $_SERVER['QUERY_STRING'] ?? '',
    json_encode($_GET),
    json_encode($_POST)
];

// Опасные паттерны SQL-инъекций
$sql_patterns = [
    '/union\s+select/i',
    '/select\s+.*\s+from/i',
    '/insert\s+into/i',
    '/update\s+.+\s+set/i',
    '/delete\s+from/i',
    '/drop\s+table/i',
    '/sleep\s*\(/i',
    '/benchmark\s*\(/i',
    '/load_file\s*\(/i',
    '/into\s+outfile/i',
    '/information_schema/i',
    '/--/',
    '/#/',
    '/\/\*/',
    '/\bOR\b\s+1=1/i',
    '/\'\s+or\s+\'/i',
    '/"\s+or\s+"/i'
];
/*
foreach ($check_data as $data) {
    foreach ($sql_patterns as $pattern) {
        if (preg_match($pattern, $data)) {

            // Логируем попытку
            file_put_contents(
                DIR_LOGS . 'sql_injection.log',
                date('Y-m-d H:i:s') .
                ' | IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') .
                ' | URI: ' . ($_SERVER['REQUEST_URI'] ?? '') .
                PHP_EOL,
                FILE_APPEND
            );

            // Блокируем
            header('HTTP/1.1 403 Forbidden');
            exit('Forbidden');
        }
    }
}
// ===== END SQL FIREWALL =====   */



// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('admin');