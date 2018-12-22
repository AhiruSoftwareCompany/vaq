<?php

class Quote implements JsonSerializable {
    private $id;
    private $date;
    private $body;
    private $rating;
    private $vote;

    public function __construct($id = -1, $date = "", $body = "", $rating = 0, $vote = 0) {
        $this->id = $id;
        $this->date = $date;
        $this->body = $body;
        $this->rating = $rating;
        $this->vote = $vote;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'body' => $this->body,
            'rating' => $this->rating,
            'vote' => $this->vote
        ];
    }
}
