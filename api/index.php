<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/oc.php';

// load env vars from .env file using Dotenv (for local development)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}
// create API helper instance
$oc = new OcApi();

header('Content-Type: application/json; charset=utf-8');

// send JSON response with status code and exit
function out(int $code, array $message)
{
    http_response_code($code);
    echo json_encode($message, JSON_UNESCAPED_UNICODE);
    exit;
}

// router
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// handle /predict endpoint
if ($path === '/predict') {
    // extract text parameter from GET or POST JSON
    $text = $method === 'GET'
        ? ($_GET['text'] ?? '')
        : (json_decode(file_get_contents('php://input') ?: '{}', true)['text'] ?? '');

    if (trim($text) === '') {
        out(400, ['error' => 'missing text']);
    }

    try {
        out(200, $oc->predict($text));
    } catch (Throwable $e) {
        out(502, ['error' => 'upstream', 'msg' => $e->getMessage()]);
    }
}

// /batch endpoint
if ($path === '/batch') {
    if ($method !== 'POST') {
        out(405, ['error' => 'POST only']);
    }

    $body = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
    $texts = $body['texts'] ?? null;
    if (!is_array($texts) || !$texts) {
        out(400, ['error' => 'missing texts[]']);
    }

    try {
        // limit to 256 tokens
        $result = json_decode($oc->batch(array_slice($texts, 0, 256)), true) ?? [];
        out(200, $result);
    } catch (Throwable $e) {
        out(502, ['error' => 'upstream', 'msg' => $e->getMessage()]);
    }
}

// root endpoint API info
if ($path === '/') {
    out(200, [
        'name' => 'OpenCensor API',
        'description' => 'Hebrew profanity detection API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /predict' => 'Analyze single text for profanity',
            'POST /batch' => 'Analyze multiple texts for profanity (max 256)'
        ],
        'usage' => [
            'predict' => 'POST {"text": "your text here"}',
            'batch' => 'POST {"texts": ["text1", "text2"]}'
        ]
    ]);
}

// 404 for unknown routes
out(404, ['error' => 'route not found']);
