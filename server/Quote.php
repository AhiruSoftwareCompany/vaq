<?php
class Quote implements JsonSerializable {
    private $id;
    private $date;
    private $body;
    private $rating;

    public function __construct($json = false, $id = -1, $date = "", $body = "", $rating = 0) {
        if ($json) {
            $this->set(json_decode($json, true));
        } else {
            $this->id = $id;
            $this->date = $date;
            $this->body = $body;
            $this->rating = $rating;
        }
    }

    // source: https://stackoverflow.com/questions/5397758/json-decode-to-custom-class/5398361#5398361
    public function set($data) {
        foreach ($data AS $key => $value) {
            if (is_array($value)) {
                $sub = new Quote;
                $sub->set($value);
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getDate() {
        return $this->date;
    }

    public function getBody() {
        return $this->body;
    }

    public function getRating() {
        return $this->rating;
    }

    // source: https://stackoverflow.com/questions/4697656/using-json-encode-on-objects-in-php-regardless-of-scope
    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'body' => $this->getBody(),
            'rating' => $this->getRating()
        ];
    }
}
?>