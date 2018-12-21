<?php

/**
 * Singleton
 */
class DataAccess {
    private $quotesPath = "data/quotes";
    private $ratingsPath = "data/ratings";
    private $usersPath = "data/users";

    protected static $instance = null;
 
	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
    }

    protected function __construct() { }
    protected function __clone() { }

    public function getRandomQuote($user) {
        $entries = explode("---\n", file_get_contents($this->quotesPath));
        if (strlen(trim($entries[0])) < 5) return null; // If there isn't any quote, return with an error. (count[entries] will always be > 0)

        $index = rand(0, count($entries) - 1); // Get a random index in the range of entries
        $data = preg_split("#\n\s*\n#Uis", $entries[$index]); // Separate headers from body
        $headers = $this->http_parse_headers($data[0]); // also exists in pecl, but not a standard php function
        $body = substr($data[1], 0, -1); // Remove the last \n from the body

        // Get the current rating of the quote
        $rating = 0;
        $lines = file($this->ratingsPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $values = explode(' ', $line);
            if ($values[0] === $headers["id"
            ]) {
                $rating = $values[1];
                break;
            }
        }
        
        // Get the users vote for the quote
        $vote = 0;
        if ($user->getVotes() !== null) {
            foreach ($user->getVotes() as $v) {
                if ($v->getId() == $headers["id"]) {
                    $vote = $v->getVote();
                    break;
                }
            }
        }

        $quote = new Quote($json = false, $headers["id"], $headers["date"], $body, $rating, $vote?$vote:0);
        return $quote;
    }

    public function refreshRating($vote, $user) {
        $retVal = 200;
        $diff = 0;

        // Refresh user file
        $lines = file($this->usersPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $u = new User($line);
            if ($u->getUID() == $user->getUID()) {
                $diff = $u->vote($vote);
                $lines[$i] = json_encode($u);
                break;
            }
        } // Assume that it will always find the user-entry
        file_put_contents($this->usersPath, implode(PHP_EOL, $lines));

        // Refresh ratings file
        $found = false;
        $lines = file($this->ratingsPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $values = explode(' ', $line);
            if ($values[0] === $vote->getId()) {
                if ($found) { // Check if we already found and changed an entry
                    $lines[$i] = "-1 " . $line; // Mark line as invalid
                } else {
                    $found = true;
                    $values[1] += $diff;
                    $lines[$i] = implode(' ', $values);
                }
            }
        }
        if (!$found) {
            $lines[count($lines)] = $vote->getId() . ' ' . $vote->getVote();
            $retVal = 201;
        }
        file_put_contents($this->ratingsPath, implode(PHP_EOL, $lines));

        

        return $retVal;
    }

    private function http_parse_headers($headers) {
        $retVal = array();
        $lines = explode("\n", $headers);
        foreach($lines as $line) {
            $parts = explode(':', $line);
            $retVal[$parts[0]] = trim($parts[1]);
        }
        return $retVal;
    }

    public function findUserToId($uid = false) {
        $user;
        
        if ($uid) { // User should exist, find it
            $lines = file($this->usersPath, FILE_IGNORE_NEW_LINES);
            $found = false;

            foreach ($lines as $line) {
                $u = new User($line);
                if ($u->getUID() == $uid) {
                    $user = $u;
                    $found = true;
                    break;
                }
            }

            if (!$found)
                return $this->findUserToId(); // Did not find user in file, create new one

        } else { // User does not exist yet, create new one
            $lines = file($this->usersPath, FILE_IGNORE_NEW_LINES);
            $ids = array();

            foreach ($lines as $line) {
                $u = new User($line);
                array_push($ids, $u->getUID());
            }

            do {
                $uid = random_int(1, PHP_INT_MAX);
            } while(array_search($uid, $ids)); // Make sure the id is not already used

            $user = new User(false, $uid);
            $lines[count($lines)] = json_encode($user);
            file_put_contents($this->usersPath, implode(PHP_EOL, $lines));
        }
        
        return $user;
    }

    public function test($jsonBody) {
        
    }
}

?>