<?php

/**
 * Singleton
 */
class DataAccess {
    var $path = "data/quotes";

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
        $lines = file($this->path, FILE_IGNORE_NEW_LINES);
        $index = rand(0, count($lines) - 1);

        $quote = array();
        $values = explode('#', $lines[$index]);

        $idPair = explode('=', $values[0]);
        $bodyPair = explode('=', $values[1]);
        $datePair = explode('=', $values[2]);
        $ratingPair = explode('=', $values[3]);

        $quote[$idPair[0]] = $idPair[1];
        $quote[$bodyPair[0]] = $bodyPair[1];
        $quote[$datePair[0]] = $datePair[1];
        $quote[$ratingPair[0]] = $ratingPair[1];

        return $quote;
    }

    public function refreshRating($quote) {
        $lines = file($this->path, FILE_IGNORE_NEW_LINES);
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
}

?>