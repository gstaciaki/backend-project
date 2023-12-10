<?php

include 'src/router.php';
require __DIR__ . '/vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

header('Content-Type: application/json');

$result = handleRequest($method, $uri);

echo json_encode($result);
