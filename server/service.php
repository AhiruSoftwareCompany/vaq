<?php

session_start();

$debugToErrorLog = true; // set true to write DEBUG-information to the PHP-ErrorLog

function __autoload($class_name) {
    include "$class_name.php";
}

/**
 * Initiates the DAO
 */
$dao = DAO::getInstance();
$dao->touchFiles();

/**
 * Checks if there is a legit user saved in the session
 * TODO Don't save the users password in cleartext
 */
$user = isset($_SESSION["user"]) ? $dao->getUser(new User($_SESSION["user"])) : null;
if ($user !== null) updateSession();

/**
 * Tries to update the session with the current user from file.
 * @return bool: false if no fitting user was found in file
 */
function updateSession() {
    global $dao, $user;
    if (($u = $dao->getUser($user)) === false) {
        $user = null;
        return false;
    }
    $user = $u;
    $_SESSION["user"] = json_encode($u);
    return true;
}

/**
 * Service Dispatcher
 */
$url = $_REQUEST['_url'];
$method = $_SERVER['REQUEST_METHOD'];
$body = file_get_contents('php://input');

if ($url === "/login" && $method === 'POST')
    login(new User($body));
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
    global $dao, $user;

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

    global $dao, $user;
    $result = $dao->refreshRating(new Vote(false, $id, $vote), $user);
    updateSession();
    http_response_code($result);
}

/**
 * Tries to log the client in.
 * Echoes the user if successful.
 * @param $receivedUser: The user as received from the client, trying to log in
 */
function login($receivedUser) {
    global $user;
    $user = $receivedUser;
    if (updateSession()) {
        echo json_encode($user);
        http_response_code(200);
    } else {
        http_response_code(403);
    }
}

function badRequest() {
    global $method, $url, $body;

    if ($GLOBALS["debugToErrorLog"])
        error_log("bad request");

    http_response_code(400);
    die('Invalid request: ' . $method . ' ' . $url . ' ' . $body);
}
