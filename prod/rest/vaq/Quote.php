<?php

class Quote implements JsonSerializable {
    private $id;
    private $date;
    private $origin;
    private $body;
    private $rating;
    private $vote;

    public function __construct($id, $date, $origin, $body, $rating, $vote) {
        $this->id = $id;
        $this->date = $date;
        $this->origin = $origin;
        $this->body = $body;
        $this->rating = $rating;
        $this->vote = $vote;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'origin' => $this->origin,
            'body' => $this->body,
            'rating' => $this->rating,
            'vote' => $this->vote
        ];
    }
}
