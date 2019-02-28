<?php

/**
 * Singleton
 */
class DAO {
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

    /**
     * Checks if the quotes-file exists.
     * Tries to create the auto-generated files if they do not already exist.
     */
    public function touchFiles() {
        if (!file_exists($this->quotesPath)) die("Quotes-file does not exist");

        $fr = fopen($this->ratingsPath, 'a+') or die("Can't create ratings-file");
        fclose($fr);

        $fu = fopen($this->usersPath, 'a+') or die("Can't create users-file");
        fclose($fu);
    }

    /**
     * Gets a random quote from the quotes-file.
     * @param User $user: The querying user
     * @param string $originString: The desired origins of the quote as a string
     * @return Quote|int: A random quote or an error code
     */
    public function getRandomQuote(User $user, $originString) {
        $entries = explode("---\n", file_get_contents($this->quotesPath));
        if (strlen(trim($entries[0])) < 5) return 404; // If there isn't any quote, return with an error. (count[entries] will always be > 0)

        if ($originString == '*') {
            $origins = $user->getOrigins();
        } else {
            if (!preg_match_all("/\/([\w\s]+)/", $originString, $matches)) return 400;
            $origins = array();
            foreach ($matches[1] as $match)
                if (in_array($match, $user->getOrigins()))
                    array_push($origins, $match);
        }
        if (count($origins) === 0) return 400;

        $timeout = 10000;
        do { // Kinda dirty solution. May want to work with known indices if performance is too bad.
            $index = rand(0, count($entries) - 1); // Get a random index in the range of entries
            $data = preg_split("#\n\s*\n#Uis", $entries[$index]); // Separate headers from body
            $headers = $this->http_parse_headers($data[0]);
            if ($timeout-- <= 0) return 400; // Did not find quotes form that origin
        } while (!in_array($headers["origin"], $origins)); // Check if acquired quote has desired origin

        $body = substr($data[1], 0, -1); // Remove the last \n from the body

        // Get the current rating of the quote
        $rating = 0;
        $lines = file($this->ratingsPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $values = explode(' ', $line);
            if ($values[0] === $headers["id"]) {
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

        $quote = new Quote($headers["id"], $headers["date"], $headers["origin"], $body, $rating, $vote ? $vote : 0);
        return $quote;
    }

    /**
     * Refreshes the user- and the ratings-file accordingly to the input.
     * Echoes the change in rating to the client.
     * @param Vote $vote: The new vote
     * @param User $user: The voting user
     * @return int: The http_response_code: 201 if nobody voted on that quote yet, 200 otherwise
     */
    public function refreshRating(Vote $vote, User $user) {
        $retVal = 200;
        $diff = 0;

        // Refresh user file
        $lines = file($this->usersPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $u = new User($line);
            if ($user == $u) {
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
                    $lines[$i] = "-1 $line"; // Mark line as invalid
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

        echo $diff;
        return $retVal;
    }

    /**
     * Tries to find the given user based on username and password in users-file.
     * @param User $user
     * @return User|bool: User if a fitting user was found, false if not
     */
    public function getUser($user) {
        $lines = file($this->usersPath, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $line) {
            $u = new User($line);
            if ($user->getName() == $u->getName() && $user->getPwd() == $u->getPwd())
                return $u;
        }

        return false;
    }

    /**
     * This function also exists in pecl, but isn't a standard php function.
     * @param $headers: HTTP-headers in the form {key]: {value}
     * @return array of {key} => {value} pairs
     */
    private function http_parse_headers($headers) {
        $retVal = array();
        $lines = explode("\n", $headers);
        foreach ($lines as $line) {
            $parts = explode(':', $line);
            $retVal[$parts[0]] = trim($parts[1]);
        }
        return $retVal;
    }
}
