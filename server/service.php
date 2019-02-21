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

if ($url === "/origins" && $method === 'GET')
    getOrigins();
elseif (preg_match("/\/quote\/(\d+)/", $url, $matches) && $method === 'PUT')
    putQuote($matches[1], json_decode($body));
elseif (preg_match("/\/quote(\/.+)/", $url, $matches) && $method === 'GET')
    getQuote($matches[1]);
elseif ($url === "/quote" && $method === 'GET')
    getQuote('*');
else
    badRequest();

/**
 * Services
 */

/**
 * GET quote: Gets a random quote from the file.
 * @param string $originString: The desired origins (categories) of the quote
 *                              in the form of (the url after '/quote') or ('*')
 */
function getQuote($originString) {
    global $dao;
    global $user;

    $quote = $dao->getRandomQuote($user, $originString);
    if ($quote === null) {
        http_response_code(404);
    } elseif ($quote === 400) {
        badRequest();
    } else {
        echo json_encode($quote);
        http_response_code(200);
    }
}

/**
 * PUT quote/{id} vote: Refreshes the rating of the given quote.
 * @param int $id: The id of the quote
 * @param int $vote: The actual vote
 */
function putQuote($id, $vote) {
    if (!($vote == 1 || $vote == -1) || $id < 0)
        badRequest();

    global $dao;
    global $user;
    $result = $dao->refreshRating(new Vote(false, $id, $vote), $user);
    http_response_code($result);
}

function getOrigins() {
    global $dao;
    $result = $dao->getOrigins();
    http_response_code(200);
    echo json_encode($result);
}

function badRequest() {
    global $method;
    global $url;
    global $body;

    if ($GLOBALS["debugToErrorLog"])
        error_log("bad request");

    http_response_code(400);
    die('Invalid request: ' . $method . ' ' . $url . ' ' . $body);
}
