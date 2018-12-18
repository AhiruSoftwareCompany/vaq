<?php

/**
 * set true to write DEBUG-information to the PHP-ErrorLog
 */
$debugToErrorLog = true;

function __autoload($class_name) {
    include $class_name . '.php';
}

$dataAccess = DataAccess::getInstance();


/**
 * Service Dispatcher
 */
$url = $_REQUEST['_url'];
$requestType = $_SERVER['REQUEST_METHOD'];
$body = file_get_contents('php://input');

if ($url === '/quote') {
    if ($requestType === 'GET')
        getQuote();
    elseif ($requestType === 'PUT')
        putQuote($body);
    else
        badRequest($requestType, $url, $body);
} else {
    badRequest($requestType, $url, $body);
}

/**
 * Services
 */

/**
 * GET quote: Gets a random quote from the file
 */
function getQuote() {
    global $dataAccess;
    $quote = $dataAccess->getRandomQuote();
    echo json_encode($quote);
    http_response_code(200);
}

/**
 * PUT quote <JSON-quote>: Refreshes the rating of the given quote
 */
function putQuote($data) {
    global $dataAccess;
    $result = $dataAccess->refreshRating($data);
    http_response_code($result);
}

function badRequest($requestType, $url, $body) {
    http_response_code(400);
    if ($GLOBALS["debugToErrorLog"]) {
        error_log("bad request");
    }
    die('Invalid request: '.$requestType.' '.$url.' '.$body);
}

?>