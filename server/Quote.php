<?php
class Quote implements JsonSerializable {
    private $id;
    private $date;
    private $body;
    private $rating;

    function __construct($id, $date, $body, $rating = 0) {
        $this->id = $id;
        $this->date = $date;
        $this->body = $body;
        $this->rating = $rating;
    }

    function getId() {
        return $this->id;
    }

    function getDate() {
        return $this->date;
    }

    function getBody() {
        return $this->body;
    }

    function getRating() {
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