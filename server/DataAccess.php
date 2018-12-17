<?php

/**
 * Singleton
 */
class DataAccess {
    var $quotesPath = "data/quotes";

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
        $index = rand(0, count($entries) - 1);
        $data = preg_split("#\n\s*\n#Uis", $entries[$index]);
        $headers = $this->http_parse_headers($data[0]); // also exists in pecl, but not a standard php function
        $quote = new Quote($headers['id'], $headers['date'], $data[1]);
        return $quote;
    }

    public function refreshRating($quote) {
        $lines = file($this->quotesPath, FILE_IGNORE_NEW_LINES);
        $index = -1;
        
        foreach ($lines as $i => $line) {
            $values = explode('#', $line);
            $idPair = explode('=', $values[0]);
            if ($idPair[1] === $quote->{'id'}) {
                $index = $i;
                break;
            }
        }

        if ($index === -1)
            return 404;

        $ratingPair = explode('=', $values[3]);
        $ratingPair[1] = $quote->{'rating'};
        $values[3] = implode('=', $ratingPair);
        $lines[$i] = implode('#', $values);

        file_put_contents($this->path, implode(PHP_EOL, $lines));
        return 201;
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