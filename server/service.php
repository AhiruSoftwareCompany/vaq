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
$method = $_SERVER['REQUEST_METHOD'];
$body = file_get_contents('php://input');

if (preg_match("/\/quote\/(\d+)/", $url, $matches) && $method === 'PUT')
    putQuote($matches[1], json_decode($body));
elseif ($url === "/quote" && $method === 'GET')
    getQuote();
else
    badRequest($method, $url, $body);

/**
 * Services
 */

/**
 * GET quote: Gets a random quote from the file
 */
function getQuote() {
    global $dataAccess;
    $quote = $dataAccess->getRandomQuote();
    if ($quote == null) {
        http_response_code(404);
    } else {
        echo json_encode($quote);
        http_response_code(200);
    }
}

/**
 * PUT quote/{id} vote: Refreshes the rating of the given quote
 */
function putQuote($id, $vote) {
    if (!($vote == 1 || $vote == -1) || $id < 0) {
        global $method;
        global $url;
        global $body;
        badRequest($method, $url, $body);
    }

    global $dataAccess;
    $result = $dataAccess->refreshRating($id, $vote);
    http_response_code($result);
}

function badRequest($method, $url, $body) {
    http_response_code(400);
    if ($GLOBALS["debugToErrorLog"]) {
        error_log("bad request");
    }
    die('Invalid request: '.$method.' '.$url.' '.$body);
}

?>