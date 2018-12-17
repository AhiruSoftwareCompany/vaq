<?php
class Quote implements JsonSerializable {
    private $id;
    private $date;
    private $body;

    function __construct($id, $date, $body) {
        $this->id = $id;
        $this->date = $date;
        $this->body = $body;
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

    // source: https://stackoverflow.com/questions/4697656/using-json-encode-on-objects-in-php-regardless-of-scope
    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'body' => $this->getBody()
        ];
    }
}
?>