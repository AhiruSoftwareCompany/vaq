<?php

/**
 * Singleton
 */
class DataAccess {
    var $quotesPath = "data/quotes";
    var $ratingsPath = "data/ratings";

    protected static $instance = null;
 
	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
    }

    protected function __construct() { }
    protected function __clone() { }

    public function getRandomQuote() {
        $entries = explode("---\n", file_get_contents($this->quotesPath));
        $index = rand(0, count($entries) - 1); // Get a random index in the range of entries
        $data = preg_split("#\n\s*\n#Uis", $entries[$index]); // Separate headers from body
        $headers = $this->http_parse_headers($data[0]); // also exists in pecl, but not a standard php function
        $body = substr($data[1], 0, -1); // Remove the last \n from the body

        $rating = 0;
        $lines = file($this->ratingsPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $values = explode(' ', $line);
            if ($values[0] === $headers['id']) {
                $rating = $values[1];
                break;
            }
        }

        $quote = new Quote($json = false, $headers['id'], $headers['date'], $body, $rating);
        return $quote;
    }

    public function refreshRating($data) {
        $quote = new Quote($json = $data);
        $id = $quote->getId();
        if ($id == -1) return 400; // the id -1 is considered invalid
        $rating = $quote->getRating();

        $found = false;
        $lines = file($this->ratingsPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $i => $line) {
            $values = explode(' ', $line);
            if ($values[0] === $id) {
                if ($found) { // Check if we already found and changed an entry
                    $lines[$i] = "-1 " . $line; // Mark line as invalid
                } else {
                    $found = true;
                    $values[1] += $rating; // might change to absolute rating
                    $lines[$i] = implode(' ', $values);
                }
            }
        }

        if (!$found) {
            $lines[count($lines)] = $id . ' ' . $rating;
        }

        file_put_contents($this->ratingsPath, implode(PHP_EOL, $lines));
        return 200; // may want to distinguish results more
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
}

?>