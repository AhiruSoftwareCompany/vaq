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

        $fr = fopen($this->ratingsPath, 'r')        // Check if file is readable
            or $fr = fopen($this->ratingsPath, 'w') // If not, try to create new one
            or die("Can't create ratings-file");          // If can't, die
        fclose($fr);

        $fu = fopen($this->usersPath, 'r')          // See above
            or $fu = fopen($this->usersPath, 'w')
            or die("Can't create users-file");
        fclose($fu);
    }

    /**
     * Gets a random quote from the quotes-file.
     * @param User $user: The querying user
     * @return Quote|null: A random quote or null if none was found
     */
    public function getRandomQuote(User $user) {
        $entries = explode("---\n", file_get_contents($this->quotesPath));
        if (strlen(trim($entries[0])) < 5) return null; // If there isn't any quote, return with an error. (count[entries] will always be > 0)

        $index = rand(0, count($entries) - 1); // Get a random index in the range of entries
        $data = preg_split("#\n\s*\n#Uis", $entries[$index]); // Separate headers from body
        $headers = $this->http_parse_headers($data[0]);
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

        $quote = new Quote($headers["id"], $headers["date"], $body, $rating, $vote ? $vote : 0);
        return $quote;
    }

    /**
     * Refreshes the user- and the ratings-file accordingly to the input.
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

    /**
     * Tries to find user to the given uid in users-file.
     * Creates new user with unique uid if none can be found or uid == false.
     * @param bool $uid
     * @return User|null
     */
    public function getUser($uid = false) {
        $user = null;

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
                return $this->getUser(); // Did not find user in file, create new one

        } else { // User does not exist yet, create new one
            $lines = file($this->usersPath, FILE_IGNORE_NEW_LINES);
            $ids = array();

            foreach ($lines as $line) {
                $u = new User($line);
                array_push($ids, $u->getUID());
            }

            do {
                try {
                    $uid = random_int(1, PHP_INT_MAX);
                } catch (Exception $e) {
                    $uid = rand(1, PHP_INT_MAX);
                }
            } while (array_search($uid, $ids)); // Make sure the id is not already used

            $user = new User(false, $uid);
            $lines[count($lines)] = json_encode($user);
            file_put_contents($this->usersPath, implode(PHP_EOL, $lines));
        }

        return $user;
    }
}
