<?php

session_start();

$debugToErrorLog = true; // set true to write DEBUG-information to the PHP-ErrorLog

function __autoload($class_name) {
    include $class_name . '.php';
}

/**
 * Initiates the DAO
 */
$dao = DAO::getInstance();
$dao->touchFiles();

/**
 * Checks if there is already a user is the session.
 * If not, checks if there is a cookie with the uid.
 * If not, creates a new user.
 */
$uid = isset($_COOKIE["uid"]) ? $_COOKIE["uid"] : false;
$user = isset($_SESSION["user"]) ? $dao->putUser(new User($_SESSION["user"])) : $dao->getUser($uid);
setcookie("uid", $user->getUID(), 2147483647); // Set / Update the uid in the cookie
$_SESSION["user"] = json_encode($user); // Set (/ Update) the session-entry

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
 * GET quote: Gets a random quote from the file.
 */
function getQuote() {
    global $dao;
    global $user;

    $quote = $dao->getRandomQuote($user);
    if ($quote == null) {
        http_response_code(404);
    } else {
        echo json_encode($quote);
        http_response_code(200);
    }
}

/**
 * PUT quote/{id} vote: Refreshes the rating of the given quote.
 * @param $id   int: The id of the quote
 * @param $vote int: The actual vote
 */
function putQuote($id, $vote) {
    if (!($vote == 1 || $vote == -1) || $id < 0) {
        global $method;
        global $url;
        global $body;
        badRequest($method, $url, $body);
    }

    global $dao;
    global $user;
    $result = $dao->refreshRating(new Vote(false, $id, $vote), $user);
    http_response_code($result);
}

function badRequest($method, $url, $body) {
    http_response_code(400);
    if ($GLOBALS["debugToErrorLog"]) {
        error_log("bad request");
    }
    die('Invalid request: ' . $method . ' ' . $url . ' ' . $body);
}
